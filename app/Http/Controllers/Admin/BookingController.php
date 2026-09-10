<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', '');
        $search = $request->input('search', '');

        $query = Booking::with(['service.beautician.user', 'customer']);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%");
                })->orWhereHas('service', function ($sq) use ($search) {
                    $sq->where('service_name', 'like', "%{$search}%")
                       ->orWhereHas('beautician.user', function ($buq) use ($search) {
                           $buq->where('name', 'like', "%{$search}%");
                       });
                });
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')->orderBy('booking_time', 'desc')->paginate(10)->withQueryString();

        return view('admin.bookings', compact('bookings', 'status', 'search'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => 'required|in:confirmed,cancelled',
        ]);

        $booking = Booking::with(['service', 'customer'])->findOrFail($id);
        $action = $validated['action'];

        $booking->status = $action;
        $booking->save();

        // In-app notification to customer
        $serviceName = $booking->service->service_name ?? 'service';
        Notification::create([
            'user_id' => $booking->customer_id,
            'message' => "Your {$serviceName} booking has been {$action} by Admin",
        ]);

        return back()->with('success', "Booking #{$booking->id} marked as {$action}.");
    }
}
