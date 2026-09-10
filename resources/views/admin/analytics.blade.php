@extends('layouts.admin')

@section('title', 'Platform Analytics - Admin Console')

@section('styles')
<style>
    .chart-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 24px;
        box-shadow: var(--shadow);
    }
    .chart-card h3 {
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        color: var(--primary);
        margin-bottom: 5px;
    }
    .chart-card p {
        font-size: 12px;
        color: var(--text-gray);
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 25px 30px; border-radius: var(--border-radius); margin-bottom: 25px; border-bottom: 3px solid var(--accent); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">Platform Performance & Analytics</h2>
        <p style="font-size: 13px; opacity: 0.85; margin: 0;">Visual insights into revenue trends, top performing artists, in-demand services, and appointment conversion.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(480px, 1fr)); gap: 25px;">
    <!-- 1. Revenue Trend Line Chart -->
    <div class="chart-card">
        <h3>6-Month Revenue Trend</h3>
        <p>Confirmed booking earnings over the past 6 months (PKR)</p>
        <div style="position: relative; height: 260px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- 2. Booking Status Doughnut Chart -->
    <div class="chart-card">
        <h3>Booking Distribution</h3>
        <p>Breakdown by appointment status (pending, confirmed, cancelled)</p>
        <div style="position: relative; height: 260px;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- 3. Top 5 Services Bar Chart -->
    <div class="chart-card">
        <h3>Top 5 Booked Services</h3>
        <p>Most popular treatments and salon services by booking volume</p>
        <div style="position: relative; height: 260px;">
            <canvas id="servicesChart"></canvas>
        </div>
    </div>

    <!-- 4. Top Beauticians Horizontal Bar Chart -->
    <div class="chart-card">
        <h3>Top Beauticians by Earnings</h3>
        <p>Highest grossing verified salon artists on the platform (PKR)</p>
        <div style="position: relative; height: 260px;">
            <canvas id="beauticiansChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueLabels) !!},
            datasets: [{
                label: 'Revenue (Rs.)',
                data: {!! json_encode($revenueValues) !!},
                borderColor: '#c9a96e',
                backgroundColor: 'rgba(201, 169, 110, 0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#1a1a2e',
                pointBorderColor: '#c9a96e',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: val => 'Rs. ' + Number(val).toLocaleString() }
                }
            }
        }
    });

    // 2. Status Breakdown Doughnut
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Confirmed', 'Pending', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $statusCounts['confirmed'] }},
                    {{ $statusCounts['pending'] }},
                    {{ $statusCounts['cancelled'] }}
                ],
                backgroundColor: ['#2e7d32', '#b78103', '#c62828'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 3. Top Services Bar Chart
    new Chart(document.getElementById('servicesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topServicesLabels) !!},
            datasets: [{
                label: 'Bookings',
                data: {!! json_encode($topServicesValues) !!},
                backgroundColor: '#1a1a2e',
                borderColor: '#c9a96e',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // 4. Top Beauticians Horizontal Bar Chart
    new Chart(document.getElementById('beauticiansChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topBeauticiansLabels) !!},
            datasets: [{
                label: 'Gross Revenue (Rs.)',
                data: {!! json_encode($topBeauticiansValues) !!},
                backgroundColor: '#0f3460',
                borderColor: '#c9a96e',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { callback: val => 'Rs. ' + Number(val).toLocaleString() }
                }
            }
        }
    });
});
</script>
@endsection
