<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_status']) && isset($_POST['new_status'])) {
    $toggle_id = mysqli_real_escape_string($conn, $_POST['toggle_status']);
    $new_status = (int)$_POST['new_status'] === 1 ? 1 : 0;
    mysqli_query($conn, "UPDATE users SET is_active=$new_status WHERE id='$toggle_id' AND role != 'admin'");
    header("Location: users.php");
    exit();
}

// Search/filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$role_filter = isset($_GET['role']) ? mysqli_real_escape_string($conn, $_GET['role']) : '';

$sql = "SELECT * FROM users WHERE 1=1";
if ($search) $sql .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
if ($role_filter) $sql .= " AND role='$role_filter'";

// Count total matches (before applying LIMIT) for pagination — wrap as
// subquery so it always works regardless of the exact SELECT columns.
$count_sql = "SELECT COUNT(*) as total FROM (" . $sql . ") as count_wrapper";
$count_result = mysqli_query($conn, $count_sql);
$total = $count_result ? mysqli_fetch_assoc($count_result)['total'] : 0;

$per_page = 10;
$total_pages = max(1, ceil($total / $per_page));
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$current_page = min($current_page, $total_pages);
$offset = ($current_page - 1) * $per_page;

$sql .= " ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";

$users = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--accent);
        }
        .page-header h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
            font-family: 'Playfair Display', serif;
        }
        .page-header p { font-size: 13px; opacity: 0.8; }
        .total-badge {
            background: var(--accent);
            color: var(--primary);
            padding: 8px 18px;
            border-radius: 3px;
            font-size: 13px;
            font-weight: 700;
        }
        .filter-box {
            background: white;
            padding: 18px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: wrap;
            border-top: 3px solid var(--accent);
        }
        .filter-box input, .filter-box select {
            flex: 1;
            margin-bottom: 0;
            min-width: 150px;
        }
        .filter-box button { width: auto !important; padding: 12px 25px; }
        .status-form { display: inline; }
        .status-btn {
            width: auto !important;
            display: inline-block !important;
            text-transform: none !important;
            border: none;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            padding: 4px 10px !important;
            border-radius: 3px;
            margin-bottom: 0;
        }
        .status-btn.deactivate { background: #fce4ec; color: #c62828; }
        .status-btn.deactivate:hover { background: #c62828; color: white; }
        .status-btn.activate { background: #e8f5e9; color: #2e7d32; }
        .status-btn.activate:hover { background: #2e7d32; color: white; }
        .active-badge {
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: #e8f5e9;
            color: #2e7d32;
        }
        .inactive-badge {
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: #f5f5f5;
            color: #999;
        }
        tr.inactive-row td { opacity: 0.55; }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: 1.5px solid var(--border);
            color: var(--text-gray);
        }
        .pagination a:hover {
            border-color: var(--accent);
            color: var(--primary);
        }
        .pagination .active-page {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .pagination .disabled {
            opacity: 0.4;
            pointer-events: none;
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Beauty <span style="color:#c9a96e;">Portal</span> — Admin</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="bookings.php">Bookings</a>
        <a href="analytics.php">Analytics</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>
<div class="container" style="max-width: 1050px;">
    <div class="page-header">
        <div>
            <h2>Manage Users</h2>
            <p>View, search and manage all registered users</p>
        </div>
        <div class="total-badge"><?php echo $total; ?> Users</div>
    </div>

    <form method="GET" class="filter-box">
        <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="role">
            <option value="">All Roles</option>
            <option value="customer" <?php echo $role_filter=='customer'?'selected':''; ?>>Customer</option>
            <option value="beautician" <?php echo $role_filter=='beautician'?'selected':''; ?>>Beautician</option>
        </select>
        <button type="submit">Filter</button>
        <?php if($search || $role_filter): ?>
        <a href="users.php" style="color:var(--text-gray); font-size:13px; text-decoration:none; padding:12px;">Clear</a>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Joined</th>
            <th>Action</th>
        </tr>
        <?php while($u = mysqli_fetch_assoc($users)): ?>
        <?php $is_active = !isset($u['is_active']) || $u['is_active'] == 1; ?>
        <tr class="<?php echo $is_active ? '' : 'inactive-row'; ?>">
            <td><?php echo $u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['name']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><span class="badge <?php echo htmlspecialchars($u['role']); ?>"><?php echo ucfirst($u['role']); ?></span></td>
            <td>
                <?php if($is_active): ?>
                <span class="active-badge">Active</span>
                <?php else: ?>
                <span class="inactive-badge">Deactivated</span>
                <?php endif; ?>
            </td>
            <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
            <td>
                <?php if($u['role'] != 'admin'): ?>
                    <?php if($is_active): ?>
                    <form method="POST" class="status-form" onsubmit="return confirm('Deactivate this user? They will not be able to log in until reactivated.')">
                        <input type="hidden" name="toggle_status" value="<?php echo $u['id']; ?>">
                        <input type="hidden" name="new_status" value="0">
                        <button type="submit" class="status-btn deactivate">Deactivate</button>
                    </form>
                    <?php else: ?>
                    <form method="POST" class="status-form">
                        <input type="hidden" name="toggle_status" value="<?php echo $u['id']; ?>">
                        <input type="hidden" name="new_status" value="1">
                        <button type="submit" class="status-btn activate">Activate</button>
                    </form>
                    <?php endif; ?>
                <?php else: ?>
                <span style="color:var(--text-gray); font-size:13px;">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if($total_pages > 1): ?>
    <div class="pagination">
        <?php
        $query_params = [];
        if ($search) $query_params['search'] = $search;
        if ($role_filter) $query_params['role'] = $role_filter;

        function build_page_url($page, $query_params) {
            $params = $query_params;
            $params['page'] = $page;
            return 'users.php?' . http_build_query($params);
        }
        ?>
        <a href="<?php echo build_page_url($current_page - 1, $query_params); ?>" class="<?php echo $current_page <= 1 ? 'disabled' : ''; ?>">&laquo; Prev</a>
        <?php for($p = 1; $p <= $total_pages; $p++): ?>
            <a href="<?php echo build_page_url($p, $query_params); ?>" class="<?php echo $p == $current_page ? 'active-page' : ''; ?>"><?php echo $p; ?></a>
        <?php endfor; ?>
        <a href="<?php echo build_page_url($current_page + 1, $query_params); ?>" class="<?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">Next &raquo;</a>
    </div>
    <?php endif; ?>
</div>
</body>
</html>