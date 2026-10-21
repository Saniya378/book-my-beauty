<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Booking;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeauticianProfileController extends Controller
{
    public function show($id)
    {
        $beautician = BeauticianProfile::with(['user', 'activeServices', 'portfolioImages', 'availability', 'reviews.customer'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);

        $isFavorited = false;
        $canReview = false;

        if (Auth::check() && Auth::user()->role === 'customer') {
            $isFavorited = Favorite::where('customer_id', Auth::id())
                ->where('beautician_id', $id)
                ->exists();

            $canReview = Booking::where('customer_id', Auth::id())
                ->where('status', 'confirmed')
                ->whereHas('service', fn ($query) => $query->where('beautician_id', $id))
                ->exists();
        }

        return view('customer.view_beautician', compact('beautician', 'isFavorited', 'canReview'));
    }
}
