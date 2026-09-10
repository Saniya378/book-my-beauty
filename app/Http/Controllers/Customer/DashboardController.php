<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $customerId = Auth::id();

        $totalBookings = Booking::where('customer_id', $customerId)->count();
        $pendingBookings = Booking::where('customer_id', $customerId)->where('status', 'pending')->count();
        $confirmedBookings = Booking::where('customer_id', $customerId)->where('status', 'confirmed')->count();
        $favoriteCount = Favorite::where('customer_id', $customerId)->count();

        $recentBookings = Booking::with(['service.beautician.user'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentFavorites = Favorite::with(['beautician.user'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('customer.dashboard', compact(
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'favoriteCount',
            'recentBookings',
            'recentFavorites'
        ));
    }
}
