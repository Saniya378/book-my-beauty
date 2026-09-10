<?php

namespace App\Http\Controllers;

use App\Models\BeauticianProfile;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'beautician') {
                return redirect()->route('beautician.dashboard');
            }
            return redirect()->route('customer.dashboard');
        }

        $totalBeauticians = User::where('role', 'beautician')->where('is_active', 1)->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalBookings = Booking::where('status', 'confirmed')->count();
        $totalServices = Service::where('is_active', 1)->count();

        $featuredBeauticians = BeauticianProfile::with(['user', 'services' => function ($q) {
            $q->where('is_active', 1)->limit(3);
        }])
        ->whereHas('user', function ($q) {
            $q->where('is_active', 1);
        })
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->orderByDesc('reviews_avg_rating')
        ->limit(6)
        ->get();

        return view('home', compact(
            'totalBeauticians',
            'totalCustomers',
            'totalBookings',
            'totalServices',
            'featuredBeauticians'
        ));
    }
}
