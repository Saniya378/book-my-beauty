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
        $months = collect(range(5, 0))
            ->map(fn (int $back) => Carbon::now()->startOfMonth()->subMonths($back));

        $from = $months->first()->toDateString();
        $to = Carbon::now()->endOfMonth()->toDateString();

        $revenuePerMonth = Booking::where('status', 'confirmed')
            ->whereBetween('booking_date', [$from, $to])
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->selectRaw("DATE_FORMAT(bookings.booking_date, '%Y-%m') AS month, SUM(services.price) AS revenue")
            ->groupBy('month')
            ->pluck('revenue', 'month');

        $bookingsPerMonth = Booking::whereBetween('booking_date', [$from, $to])
            ->selectRaw("DATE_FORMAT(booking_date, '%Y-%m') AS month, COUNT(*) AS total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $report = $months->map(function (Carbon $month) use ($revenuePerMonth, $bookingsPerMonth) {
            $key = $month->format('Y-m');
            $bookings = (int) ($bookingsPerMonth->get($key) ?? 0);
            $revenue = (float) ($revenuePerMonth->get($key) ?? 0);

            return [
                'label' => $month->format('M Y'),
                'bookings' => $bookings,
                'revenue' => $revenue,
                'average' => $bookings ? round($revenue / $bookings) : 0.0,
            ];
        });

        $statusCounts = Booking::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $confirmed = (int) $statusCounts->get('confirmed', 0);
        $pending = (int) $statusCounts->get('pending', 0);
        $cancelled = (int) $statusCounts->get('cancelled', 0);
        $totalBookings = $confirmed + $pending + $cancelled;

        $periodBookings = (int) $bookingsPerMonth->sum();
        $periodConfirmed = Booking::where('status', 'confirmed')
            ->whereBetween('booking_date', [$from, $to])
            ->count();
        $periodRevenue = (float) $revenuePerMonth->sum();

        $topServices = Service::with('beautician.user')
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        $topEarners = Service::query()
            ->join('bookings', 'bookings.service_id', '=', 'services.id')
            ->join('beautician_profiles', 'beautician_profiles.id', '=', 'services.beautician_id')
            ->join('users', 'users.id', '=', 'beautician_profiles.user_id')
            ->where('bookings.status', 'confirmed')
            ->select('users.name', DB::raw('SUM(services.price) AS revenue'))
            ->groupBy('beautician_profiles.id', 'users.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('admin.analytics', [
            'report' => $report,
            'labels' => $report->pluck('label'),
            'revenueSeries' => $report->pluck('revenue'),
            'bookingSeries' => $report->pluck('bookings'),
            'confirmed' => $confirmed,
            'pending' => $pending,
            'cancelled' => $cancelled,
            'totalBookings' => $totalBookings,
            'periodBookings' => $periodBookings,
            'periodConfirmed' => $periodConfirmed,
            'periodRevenue' => $periodRevenue,
            'averageBookingValue' => $periodConfirmed ? round($periodRevenue / $periodConfirmed) : 0,
            'completionRate' => $periodBookings ? round($periodConfirmed / $periodBookings * 100) : 0,
            'topServices' => $topServices,
            'topEarners' => $topEarners,
        ]);
    }
}