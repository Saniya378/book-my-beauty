<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Revenue trend: last 6 months (confirmed bookings only)
        $revenueLabels = [];
        $revenueValues = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth()->toDateString();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth()->toDateString();
            $label = Carbon::now()->subMonths($i)->format('M Y');

            $rev = Booking::where('status', 'confirmed')
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->sum('services.price');

            $revenueLabels[] = $label;
            $revenueValues[] = (float) $rev;
        }

        // 2. Top 5 services by number of bookings
        $topServices = Booking::select('services.service_name', DB::raw('count(bookings.id) as total_bookings'))
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->groupBy('services.id', 'services.service_name')
            ->orderByDesc('total_bookings')
            ->limit(5)
            ->get();

        $topServicesLabels = $topServices->pluck('service_name')->toArray();
        $topServicesValues = $topServices->pluck('total_bookings')->toArray();

        // 3. Top 5 beauticians by confirmed revenue
        $topBeauticians = Booking::select('users.name', DB::raw('COALESCE(SUM(services.price), 0) as total_revenue'))
            ->where('bookings.status', 'confirmed')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->join('beautician_profiles', 'services.beautician_id', '=', 'beautician_profiles.id')
            ->join('users', 'beautician_profiles.user_id', '=', 'users.id')
            ->groupBy('beautician_profiles.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        $topBeauticiansLabels = $topBeauticians->pluck('name')->toArray();
        $topBeauticiansValues = $topBeauticians->pluck('total_revenue')->map(fn($v) => (float) $v)->toArray();

        // 4. Booking status breakdown
        $statusCountsRaw = Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusCounts = [
            'pending' => $statusCountsRaw['pending'] ?? 0,
            'confirmed' => $statusCountsRaw['confirmed'] ?? 0,
            'cancelled' => $statusCountsRaw['cancelled'] ?? 0,
        ];

        return view('admin.analytics', compact(
            'revenueLabels',
            'revenueValues',
            'topServicesLabels',
            'topServicesValues',
            'topBeauticiansLabels',
            'topBeauticiansValues',
            'statusCounts'
        ));
    }
}
