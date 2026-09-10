<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function show($beautician_id)
    {
        $beautician = BeauticianProfile::with(['user', 'reviews.customer'])->findOrFail($beautician_id);

        $existingReview = Review::where('customer_id', Auth::id())
            ->where('beautician_id', $beautician_id)
            ->first();

        return view('customer.review', compact('beautician', 'existingReview'));
    }

    public function store(Request $request, $beautician_id)
    {
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

        return back()->with('success', 'Review submitted successfully! Thank you for your feedback.');
    }
}
