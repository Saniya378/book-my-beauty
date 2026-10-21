<?php

namespace App\Http\Controllers;

use App\Models\BeauticianProfile;
use App\Models\Booking;
use App\Models\Review;
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
        $avgPlatformRating = Review::avg('rating') ?: 0;

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
            'avgPlatformRating',
            'featuredBeauticians'
        ));
    }

    public function about()
    {
        $totalBeauticians = User::where('role', 'beautician')->where('is_active', 1)->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalBookings = Booking::where('status', 'confirmed')->count();
        $totalServices = Service::where('is_active', 1)->count();

        return view('about', compact(
            'totalBeauticians',
            'totalCustomers',
            'totalBookings',
            'totalServices'
        ));
    }

    public function contact()
    {
        return view('contact');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        return back()->with('success', 'Thank you for reaching out! Your message has been received. We will get back to you shortly.');
    }
}

