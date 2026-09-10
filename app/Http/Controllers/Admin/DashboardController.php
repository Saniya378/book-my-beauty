<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalBeauticians = User::where('role', 'beautician')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalBookings = Booking::count();
        $totalReviews = Review::count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        $totalRevenue = Booking::where('status', 'confirmed')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->sum('services.price');

        $recentBookings = Booking::with(['service.beautician.user', 'customer'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalBeauticians',
            'totalCustomers',
            'totalBookings',
            'totalReviews',
            'pendingBookings',
            'totalRevenue',
            'recentBookings',
            'recentUsers'
        ));
    }
}
