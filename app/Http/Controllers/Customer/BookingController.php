<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Service;
use App\Services\ReceiptPdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create($beautician_id)
    {
        $beautician = BeauticianProfile::with(['user', 'activeServices', 'availability'])->findOrFail($beautician_id);

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $availabilityByDay = [];
        foreach ($beautician->availability as $avail) {
            $availabilityByDay[$avail->day_of_week] = $avail;
        }

        return view('customer.book', compact('beautician', 'availabilityByDay', 'daysOfWeek'));
    }

    public function store(Request $request, $beautician_id)
    {
        $validated = $request->validate([
            'service_id' => 'required|integer',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);

        $beautician = BeauticianProfile::with('user')->findOrFail($beautician_id);

        // Verify service belongs to this beautician and is active
        $service = Service::where('id', $validated['service_id'])
            ->where('beautician_id', $beautician_id)
            ->where('is_active', 1)
            ->first();

        if (!$service) {
            return back()->withInput()->with('error', 'Invalid service selected.');
        }

        // Calculate requested start and end time using this service's duration
        $requestedStart = Carbon::parse($validated['booking_date'] . ' ' . $validated['booking_time']);
        $requestedEnd   = $requestedStart->copy()->addMinutes($service->duration);

        // Check the beautician's availability for the requested day
        $dayOfWeek = $requestedStart->format('l'); // e.g. "Monday"

        $availability = $beautician->availability()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$availability) {
            return back()->withInput()->with('error', "This beautician is not available on {$dayOfWeek}s. Please choose a different date.");
        }

        $availStart = Carbon::parse($validated['booking_date'] . ' ' . $availability->start_time);
        $availEnd   = Carbon::parse($validated['booking_date'] . ' ' . $availability->end_time);

        if ($requestedStart->lt($availStart) || $requestedEnd->gt($availEnd)) {
            $availStartFormatted = $availStart->format('h:i A');
            $availEndFormatted   = $availEnd->format('h:i A');
            return back()->withInput()->with('error', "This beautician is only available from {$availStartFormatted} to {$availEndFormatted} on {$dayOfWeek}s. Please choose a time within that window.");
        }

        // Fetch all active (non-cancelled) bookings for this beautician on the same date,
        // along with each booking's own service duration, to check for time overlap
        $existingBookings = Booking::whereHas('service', function ($q) use ($beautician_id) {
                $q->where('beautician_id', $beautician_id);
            })
            ->where('booking_date', $validated['booking_date'])
            ->where('status', '!=', 'cancelled')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->select('bookings.*', 'services.duration as service_duration')
            ->get();

        $hasConflict = $existingBookings->contains(function ($booking) use ($validated, $requestedStart, $requestedEnd) {
            $existingStart = Carbon::parse($validated['booking_date'] . ' ' . $booking->booking_time);
            $existingEnd   = $existingStart->copy()->addMinutes($booking->service_duration);

            // Overlap if requested range starts before existing ends AND ends after existing starts
            return $requestedStart->lt($existingEnd) && $requestedEnd->gt($existingStart);
        });

        if ($hasConflict) {
            return back()->withInput()->with('error', 'This time slot is already booked. Please select another time.');
        }

        // Check daily booking limit for this beautician (max 10 per day)
        $dailyBookingCount = $existingBookings->count();

        if ($dailyBookingCount >= 10) {
            return back()->withInput()->with('error', 'This beautician has reached their daily limit of 10 appointments for this date. Please choose a different date.');
        }

        $booking = Booking::create([
            'customer_id' => Auth::id(),
            'service_id' => $service->id,
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'status' => 'pending',
        ]);

        // In-app notification to beautician
        $customerName = Auth::user()->name;
        $dateFormatted = Carbon::parse($validated['booking_date'])->format('d M Y');
        $timeFormatted = date('h:i A', strtotime($validated['booking_time']));
        $endTimeFormatted = $requestedEnd->format('h:i A');

        Notification::create([
            'user_id' => $beautician->user_id,
            'message' => "New booking from {$customerName} for {$service->service_name} on {$dateFormatted} at {$timeFormatted} - {$endTimeFormatted}",
        ]);

        // Send email confirmation to customer
        $customer = Auth::user();
        try {
            Mail::send([], [], function ($message) use ($customer, $beautician, $service, $dateFormatted, $timeFormatted, $endTimeFormatted) {
                $message->to($customer->email, $customer->name)
                    ->subject('Your BookMyBeauty appointment is booked!')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px;'>
                            <h2 style='color: #1a1a2e;'>Appointment Confirmed</h2>
                            <p>Hi <strong>" . htmlspecialchars($customer->name) . "</strong>,</p>
                            <p>Your appointment has been booked successfully. Here are the details:</p>
                            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                                <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Beautician:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($beautician->user->name ?? 'Beautician') . "</td></tr>
                                <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Service:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($service->service_name) . "</td></tr>
                                <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Date:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$dateFormatted}</td></tr>
                                <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Time:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$timeFormatted} - {$endTimeFormatted}</td></tr>
                                <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Price:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>Rs. " . number_format($service->price, 0) . "</td></tr>
                            </table>
                            <p style='color: #777; font-size: 13px;'>Note: Appointments can be cancelled within 2 hours of booking from the My Bookings page.</p>
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            // Log mail failure gracefully
        }

        return redirect()->route('customer.bookings')->with('success', 'Appointment booked successfully!');
    }

    public function index()
    {
        $bookings = Booking::with(['service.beautician.user'])
            ->where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.my_bookings', compact('bookings'));
    }

    public function cancel($id)
    {
        $booking = Booking::with(['service.beautician'])->where('id', $id)
            ->where('customer_id', Auth::id())
            ->firstOrFail();

        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Cancellation window expired. Bookings can only be cancelled within 2 hours of creation.');
        }

        $booking->status = 'cancelled';
        $booking->save();

        // Notify beautician
        $customerName = Auth::user()->name;
        $serviceName = $booking->service->service_name ?? 'service';
        $beauticianUserId = $booking->service->beautician->user_id ?? 0;

        if ($beauticianUserId > 0) {
            Notification::create([
                'user_id' => $beauticianUserId,
                'message' => "{$customerName} cancelled their {$serviceName} booking",
            ]);
        }

        return back()->with('success', 'Booking cancelled successfully.');
    }

    public function downloadReceipt($id, ReceiptPdfService $pdfService)
    {
        $booking = Booking::with(['service.beautician.user', 'customer'])
            ->where('id', $id)
            ->where('customer_id', Auth::id())
            ->firstOrFail();

        $pdfOutput = $pdfService->generateReceipt($booking);

        return response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Receipt_Booking' . $booking->id . '.pdf"',
        ]);
    }
}