<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
         header("Location: login.php");
        exit();
}
include '../config.php';

// ---- Revenue trend: last 6 months (confirmed bookings only) ----
$revenue_labels = [];
$revenue_values = [];
for ($i = 5; $i >= 0; $i--) {
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_end = date('Y-m-t', strtotime("-$i months"));
    $label = date('M Y', strtotime($month_start));

    $rev = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COALESCE(SUM(s.price), 0) as total
         FROM bookings b JOIN services s ON b.service_id = s.id
         WHERE b.status = 'confirmed'
         AND b.booking_date BETWEEN '$month_start' AND '$month_end'"));

    $revenue_labels[] = $label;
    $revenue_values[] = (float)$rev['total'];
}

// ---- Top 5 services by number of bookings ----
$top_services_labels = [];
$top_services_values = [];
$ts = mysqli_query($conn,
    "SELECT s.service_name, COUNT(b.id) as total_bookings
     FROM bookings b JOIN services s ON b.service_id = s.id
     GROUP BY s.id ORDER BY total_bookings DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($ts)) {
    $top_services_labels[] = $row['service_name'];
    $top_services_values[] = (int)$row['total_bookings'];
}

// ---- Top 5 beauticians by confirmed revenue ----
$top_beauticians_labels = [];
$top_beauticians_values = [];
$tb = mysqli_query($conn,
    "SELECT u.name, COALESCE(SUM(s.price), 0) as revenue
     FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN beautician_profiles bp ON s.beautician_id = bp.id
     JOIN users u ON bp.user_id = u.id
     WHERE b.status = 'confirmed'
     GROUP BY bp.id ORDER BY revenue DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($tb)) {
    $top_beauticians_labels[] = $row['name'];
    $top_beauticians_values[] = (float)$row['revenue'];
}

// ---- Booking status breakdown ----
$status_counts = ['pending' => 0, 'confirmed' => 0, 'cancelled' => 0];
$sc = mysqli_query($conn, "SELECT status, COUNT(*) as total FROM bookings GROUP BY status");
while ($row = mysqli_fetch_assoc($sc)) {
    if (isset($status_counts[$row['status']])) {
        $status_counts[$row['status']] = (int)$row['total'];
    }
}

// ---- Summary numbers ----
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COALESCE(SUM(s.price), 0) as total FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.status='confirmed'"))['total'];
$avg_booking_value = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COALESCE(AVG(s.price), 0) as avg FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.status='confirmed'"))['avg'];
$total_confirmed = array_sum($status_counts) > 0 ? $status_counts['confirmed'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            border-bottom: 3px solid var(--accent);
        }
        .page-header h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; font-family: 'Playfair Display', serif; }
        .page-header p { font-size: 13px; opacity: 0.8; }
        .summary-row {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .summary-card {
            background: white;
            padding: 20px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            flex: 1;
            min-width: 180px;
            border-bottom: 3px solid var(--accent);
        }
        .summary-card .label {
            font-size: 11px;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .summary-card .value {
            font-size: 26px;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Playfair Display', serif;
        }
        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .chart-box {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 22px 25px;
        }
        .chart-box h3 {
            color: var(--primary);
            font-size: 15px;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-light);
        }
        .chart-box canvas { max-height: 260px; }
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .empty-note {
            color: var(--text-gray);
            font-size: 12px;
            text-align: center;
            padding: 30px 0;
        }
        @media (max-width: 900px) {
            .chart-grid, .two-col { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Beauty <span style="color:#c9a96e;">Portal</span> — Admin</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="bookings.php">Bookings</a>
        <a href="users.php">Users</a>
        <a href="analytics.php">Analytics</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container" style="max-width: 1150px;">
    <div class="page-header">
        <h2>Analytics</h2>
        <p>Platform performance at a glance</p>
    </div>

    <div class="summary-row">
        <div class="summary-card">
            <div class="label">Total Revenue (Confirmed)</div>
            <div class="value">Rs. <?php echo number_format($total_revenue, 0); ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Avg. Booking Value</div>
            <div class="value">Rs. <?php echo number_format($avg_booking_value, 0); ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Confirmed Bookings</div>
            <div class="value"><?php echo $total_confirmed; ?></div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="chart-box">
            <h3>Revenue Trend (Last 6 Months)</h3>
            <?php if(array_sum($revenue_values) > 0): ?>
            <canvas id="revenueChart"></canvas>
            <?php else: ?>
            <div class="empty-note">No confirmed bookings yet to show a revenue trend.</div>
            <?php endif; ?>
        </div>
        <div class="chart-box">
            <h3>Booking Status</h3>
            <?php if(array_sum($status_counts) > 0): ?>
            <canvas id="statusChart"></canvas>
            <?php else: ?>
            <div class="empty-note">No bookings yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="two-col">
        <div class="chart-box">
            <h3>Top 5 Services by Bookings</h3>
            <?php if(!empty($top_services_values)): ?>
            <canvas id="servicesChart"></canvas>
            <?php else: ?>
            <div class="empty-note">No bookings yet.</div>
            <?php endif; ?>
        </div>
        <div class="chart-box">
            <h3>Top 5 Beauticians by Revenue</h3>
            <?php if(!empty($top_beauticians_values)): ?>
            <canvas id="beauticiansChart"></canvas>
            <?php else: ?>
            <div class="empty-note">No confirmed bookings yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const navy = '#1a1a2e';
const gold = '#c9a96e';
const goldLight = '#f0e6d3';

<?php if(array_sum($revenue_values) > 0): ?>
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($revenue_labels); ?>,
        datasets: [{
            label: 'Revenue (Rs.)',
            data: <?php echo json_encode($revenue_values); ?>,
            borderColor: navy,
            backgroundColor: 'rgba(26,26,46,0.08)',
            fill: true,
            tension: 0.3,
            pointBackgroundColor: gold,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
<?php endif; ?>

<?php if(array_sum($status_counts) > 0): ?>
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Confirmed', 'Cancelled'],
        datasets: [{
            data: [<?php echo $status_counts['pending']; ?>, <?php echo $status_counts['confirmed']; ?>, <?php echo $status_counts['cancelled']; ?>],
            backgroundColor: ['#e65100', '#2e7d32', '#c62828']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});
<?php endif; ?>

<?php if(!empty($top_services_values)): ?>
new Chart(document.getElementById('servicesChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($top_services_labels); ?>,
        datasets: [{
            label: 'Bookings',
            data: <?php echo json_encode($top_services_values); ?>,
            backgroundColor: gold,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
<?php endif; ?>

<?php if(!empty($top_beauticians_values)): ?>
new Chart(document.getElementById('beauticiansChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($top_beauticians_labels); ?>,
        datasets: [{
            label: 'Revenue (Rs.)',
            data: <?php echo json_encode($top_beauticians_values); ?>,
            backgroundColor: navy,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});
<?php endif; ?>
</script>
</body>
</html>