<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $statusCounts = Booking::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $confirmed = (int) $statusCounts->get('confirmed', 0);
        $pending = (int) $statusCounts->get('pending', 0);
        $cancelled = (int) $statusCounts->get('cancelled', 0);
        $totalBookings = $confirmed + $pending + $cancelled;

        $revenue = Booking::where('status', 'confirmed')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->sum('services.price');

        $roleCounts = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $beauticians = (int) $roleCounts->get('beautician', 0);
        $customers = (int) $roleCounts->get('customer', 0);
        $totalUsers = (int) $roleCounts->sum();

        $activeServices = Service::where('is_active', 1)->count();
        $totalReviews = Review::count();
        $avgRating = Review::avg('rating') ?: 0;

        $recentBookings = Booking::with(['service.beautician.user', 'customer'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        $pendingRequests = Booking::with(['service', 'customer'])
            ->where('status', 'pending')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->limit(5)
            ->get();

        $topBeauticians = BeauticianProfile::with('user')
            ->withCount(['bookings' => fn ($query) => $query->where('status', 'confirmed')])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        $recentUsers = User::latest('created_at')->limit(5)->get();

        $recentReviews = Review::with(['customer', 'beautician.user'])
            ->latest('created_at')
            ->limit(4)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'customers',
            'beauticians',
            'totalBookings',
            'confirmed',
            'pending',
            'cancelled',
            'revenue',
            'activeServices',
            'totalReviews',
            'avgRating',
            'recentBookings',
            'pendingRequests',
            'topBeauticians',
            'recentUsers',
            'recentReviews'
        ));
    }
}