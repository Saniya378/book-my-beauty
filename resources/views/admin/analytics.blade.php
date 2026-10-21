@extends('layouts.admin')

@section('title', 'Analytics - Admin Console')

@section('content')
    <div class="admin-head">
        <div>
            <h1 class="admin-head__title">Analytics</h1>
            <p class="admin-head__subtitle">Revenue, demand and booking outcomes for the last six months</p>
        </div>

        <div class="admin-head__actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn--ghost">
                <x-icon name="trend" :size="15" />
                Back to dashboard
            </a>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi">
            <span class="kpi__icon"><x-icon name="revenue" :size="19" /></span>
            <div>
                <div class="kpi__value">Rs. {{ number_format($periodRevenue, 0) }}</div>
                <div class="kpi__label">Revenue in period</div>
                <div class="kpi__note">Confirmed bookings only</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="bookings" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $periodBookings }}</div>
                <div class="kpi__label">Bookings in period</div>
                <div class="kpi__note">{{ $periodConfirmed }} of them confirmed</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="services" :size="19" /></span>
            <div>
                <div class="kpi__value">Rs. {{ number_format($averageBookingValue, 0) }}</div>
                <div class="kpi__label">Average booking value</div>
                <div class="kpi__note">Per confirmed appointment</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="pending" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $completionRate }}%</div>
                <div class="kpi__label">Confirmation rate</div>
                <div class="kpi__note">Share of bookings that completed</div>
            </div>
        </div>
    </div>

    <div class="admin-grid admin-grid--split">
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Revenue and booking volume</h2>
                    <p class="panel__meta">Confirmed earnings against number of appointments per month</p>
                </div>
            </div>

            <div class="panel__body">
                <div class="chart">
                    <canvas id="revenueChart"></canvas>
                </div>

                <div class="legend">
                    <span class="legend__item">
                        <span class="legend__dot" style="background: #2d3561"></span>
                        Confirmed revenue
                    </span>
                    <span class="legend__item">
                        <span class="legend__dot legend__dot--line" style="background: #c9992b"></span>
                        Bookings
                    </span>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Booking status split</h2>
                    <p class="panel__meta">All {{ $totalBookings }} bookings ever made</p>
                </div>
            </div>

            <div class="panel__body">
                <div class="chart chart--short">
                    <canvas id="statusChart"></canvas>
                </div>

                <div class="legend">
                    @foreach ([
                        ['Confirmed', $confirmed, '#2e6b34'],
                        ['Pending', $pending, '#c9992b'],
                        ['Cancelled', $cancelled, '#a63b3b'],
                    ] as [$label, $value, $color])
                        <span class="legend__item">
                            <span class="legend__dot" style="background: {{ $color }}"></span>
                            {{ $label }}
                            <strong>{{ $value }}</strong>
                            <span>{{ $totalBookings ? round($value / $totalBookings * 100) : 0 }}%</span>
                        </span>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <div class="admin-grid admin-grid--even">
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Most booked services</h2>
                    <p class="panel__meta">Top five by number of appointments</p>
                </div>
            </div>

            <div class="panel__body">
                <div class="chart chart--short">
                    <canvas id="servicesChart"></canvas>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Top earning beauticians</h2>
                    <p class="panel__meta">Confirmed revenue earned per stylist</p>
                </div>
            </div>

            <div class="panel__body">
                <div class="chart chart--short">
                    <canvas id="beauticiansChart"></canvas>
                </div>
            </div>
        </section>
    </div>

    <section class="panel">
        <div class="panel__head">
            <div>
                <h2 class="panel__title">Month by month</h2>
                <p class="panel__meta">The same figures behind the charts above</p>
            </div>
        </div>

        <div class="panel__body panel__body--flush">
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Bookings</th>
                            <th>Confirmed revenue</th>
                            <th>Average booking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ $row['bookings'] }}</td>
                                <td>Rs. {{ number_format($row['revenue'], 0) }}</td>
                                <td>Rs. {{ number_format($row['average'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong>{{ $periodBookings }}</strong></td>
                            <td><strong>Rs. {{ number_format($periodRevenue, 0) }}</strong></td>
                            <td><strong>Rs. {{ number_format($averageBookingValue, 0) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        const revenue = @json($revenueSeries);
        const bookings = @json($bookingSeries);

        Chart.defaults.font.family = "'Poppins', sans-serif";
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#777777';

        const grid = { color: '#f1f1f1', drawTicks: false };
        const noBorder = { grid: { display: false }, border: { display: false } };

        new Chart(document.getElementById('revenueChart'), {
            data: {
                labels: @json($labels),
                datasets: [
                    {
                        type: 'bar',
                        label: 'Revenue (Rs.)',
                        data: revenue,
                        backgroundColor: '#2d3561',
                        borderRadius: 3,
                        maxBarThickness: 42,
                        yAxisID: 'y',
                    },
                    {
                        type: 'line',
                        label: 'Bookings',
                        data: bookings,
                        borderColor: '#c9992b',
                        backgroundColor: '#c9992b',
                        borderWidth: 2,
                        tension: 0,
                        pointRadius: 3,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        position: 'left',
                        grid: grid,
                        border: { display: false },
                        ticks: { callback: (value) => 'Rs. ' + value.toLocaleString() },
                    },
                    y1: {
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        border: { display: false },
                        ticks: { precision: 0 },
                    },
                    x: { ...noBorder },
                },
            },
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Confirmed', 'Pending', 'Cancelled'],
                datasets: [
                    {
                        data: [@json($confirmed), @json($pending), @json($cancelled)],
                        backgroundColor: ['#2e6b34', '#c9992b', '#a63b3b'],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: { legend: { display: false } },
            },
        });

        function rankedBar(canvasId, labels, values, color, prefix) {
            new Chart(document.getElementById(canvasId), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: values,
                            backgroundColor: color,
                            borderRadius: 3,
                            maxBarThickness: 22,
                        },
                    ],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => prefix + context.parsed.x.toLocaleString(),
                            },
                        },
                    },
                    scales: {
                        x: { grid: grid, border: { display: false } },
                        y: { ...noBorder },
                    },
                },
            });
        }

        rankedBar('servicesChart', @json($topServices->pluck('service_name')), @json($topServices->pluck('bookings_count')), '#2d3561', 'Bookings: ');
        rankedBar('beauticiansChart', @json($topEarners->pluck('name')), @json($topEarners->map(fn ($earner) => (float) $earner->revenue)), '#c9992b', 'Rs. ');
    </script>
@endsection