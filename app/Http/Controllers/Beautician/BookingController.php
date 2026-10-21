<?php

namespace App\Http\Controllers\Beautician;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $bookings = Booking::with(['service', 'customer'])
            ->whereHas('service', function ($q) use ($profile) {
                $q->where('beautician_id', $profile->id);
            })
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(10);

        return view('beautician.bookings', compact('bookings'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => 'required|in:confirmed,cancelled',
        ]);

        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $booking = Booking::with(['service', 'customer'])
            ->whereHas('service', function ($q) use ($profile) {
                $q->where('beautician_id', $profile->id);
            })
            ->findOrFail($id);

        $action = $validated['action'];
        $booking->status = $action;
        $booking->save();

        // In-app notification to customer
        $beauticianName = Auth::user()->name;
        $serviceName = $booking->service->service_name ?? 'service';
        Notification::create([
            'user_id' => $booking->customer_id,
            'message' => "Your appointment for {$serviceName} has been {$action} by {$beauticianName}",
        ]);

        return back()->with('success', "Booking marked as {$action} successfully.");
    }
}
