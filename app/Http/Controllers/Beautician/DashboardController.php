<?php

namespace App\Http\Controllers\Beautician;

use App\Http\Controllers\Controller;
use App\Models\BeauticianProfile;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $profile = BeauticianProfile::firstOrCreate(['user_id' => Auth::id()]);

        $totalBookings = Booking::whereHas('service', function ($q) use ($profile) {
            $q->where('beautician_id', $profile->id);
        })->count();

        $pendingBookings = Booking::whereHas('service', function ($q) use ($profile) {
            $q->where('beautician_id', $profile->id);
        })->where('status', 'pending')->count();

        $confirmedBookings = Booking::whereHas('service', function ($q) use ($profile) {
            $q->where('beautician_id', $profile->id);
        })->where('status', 'confirmed')->count();

        $totalRevenue = Booking::whereHas('service', function ($q) use ($profile) {
            $q->where('beautician_id', $profile->id);
        })->where('status', 'confirmed')
          ->join('services', 'bookings.service_id', '=', 'services.id')
          ->sum('services.price');

        $recentBookings = Booking::with(['service', 'customer'])
            ->whereHas('service', function ($q) use ($profile) {
                $q->where('beautician_id', $profile->id);
            })
            ->orderBy('booking_date', 'desc')
            ->limit(6)
            ->get();

        $avgRating = $profile->reviews()->avg('rating') ?: 0;
        $reviewCount = $profile->reviews()->count();

        return view('beautician.dashboard', compact(
            'profile',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'totalRevenue',
            'recentBookings',
            'avgRating',
            'reviewCount'
        ));
    }
}
