<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function show($beautician_id)
    {
        $beautician = BeauticianProfile::with(['user', 'reviews.customer'])->findOrFail($beautician_id);

        // a review is only fair if the customer actually finished an appointment
        if (!$this->hasFinishedAppointment($beautician_id)) {
            return redirect()
                ->route('beautician.view', $beautician_id)
                ->with('error', 'You can only review a beautician after one of your appointments with them is confirmed.');
        }

        $existingReview = Review::where('customer_id', Auth::id())
            ->where('beautician_id', $beautician_id)
            ->first();

        return view('customer.review', compact('beautician', 'existingReview'));
    }

    public function store(Request $request, $beautician_id)
    {
        if (!$this->hasFinishedAppointment($beautician_id)) {
            return back()->with('error', 'You can only review a beautician after one of your appointments with them is confirmed.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existing = Review::where('customer_id', Auth::id())
            ->where('beautician_id', $beautician_id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reviewed this beautician.');
        }

        Review::create([
            'customer_id' => Auth::id(),
            'beautician_id' => $beautician_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? '',
        ]);

        return back()->with('success', 'Review submitted. Thank you for your feedback!');
    }

    /**
     * Bookings link a customer to a beautician through the service they booked,
     * so the check has to walk that same path.
     */
    private function hasFinishedAppointment($beauticianId): bool
    {
        return Booking::where('customer_id', Auth::id())
            ->where('status', 'confirmed')
            ->whereHas('service', fn ($query) => $query->where('beautician_id', $beauticianId))
            ->exists();
    }
}