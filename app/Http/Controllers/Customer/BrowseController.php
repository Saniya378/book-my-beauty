<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $location = $request->input('location', '');
        $service = $request->input('service', '');
        $minPrice = $request->filled('min_price') ? (float) $request->input('min_price') : 0;
        $maxPrice = $request->filled('max_price') ? (float) $request->input('max_price') : 999999;
        $minRating = $request->filled('rating') ? (float) $request->input('rating') : 0;

        $query = BeauticianProfile::with(['user', 'activeServices'])
            ->whereHas('user', function ($q) use ($search) {
                $q->where('is_active', 1);
                if (!empty($search)) {
                    $q->where('name', 'like', "%{$search}%");
                }
            })
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if (!empty($location)) {
            $query->where('location', 'like', "%{$location}%");
        }

        if (!empty($service) || $request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('services', function ($q) use ($service, $minPrice, $maxPrice) {
                $q->where('is_active', 1);
                if (!empty($service)) {
                    $q->where('service_name', 'like', "%{$service}%");
                }
                $q->whereBetween('price', [$minPrice, $maxPrice]);
            });
        }

        if ($minRating > 0) {
            $query->having('reviews_avg_rating', '>=', $minRating);
        }

        $beauticians = $query->get();

        $favoritedIds = [];
        if (Auth::check() && Auth::user()->role === 'customer') {
            $favoritedIds = Favorite::where('customer_id', Auth::id())->pluck('beautician_id')->toArray();
        }

        return view('customer.browse', compact(
            'beauticians',
            'favoritedIds',
            'search',
            'location',
            'service',
            'minPrice',
            'maxPrice',
            'minRating'
        ));
    }
}
