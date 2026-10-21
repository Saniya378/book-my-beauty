<?php

namespace App\Http\Controllers\Beautician;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $reviews = Review::with('customer')
            ->where('beautician_id', $profile->id)
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        // how the average was reached, five stars down to one
        $breakdown = Review::where('beautician_id', $profile->id)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $average = (float) ($reviews->total() ? Review::where('beautician_id', $profile->id)->avg('rating') : 0);

        // how much of the review flow they have actually unlocked
        $confirmed = Booking::where('status', 'confirmed')
            ->whereHas('service', fn ($query) => $query->where('beautician_id', $profile->id))
            ->count();

        $activeServices = Service::where('beautician_id', $profile->id)->where('is_active', 1)->count();

        return view('beautician.reviews', compact(
            'profile',
            'reviews',
            'breakdown',
            'average',
            'confirmed',
            'activeServices'
        ));
    }
}