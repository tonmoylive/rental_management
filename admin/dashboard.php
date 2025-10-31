<?php
require_once '../config.php';

if (!isLoggedIn('admin')) {
    redirect('login.php');
}

// Get statistics
$stats = [];
$stats['owners'] = $conn->query("SELECT COUNT(*) as count FROM building_owners")->fetch_assoc()['count'];
$stats['buildings'] = $conn->query("SELECT COUNT(*) as count FROM buildings")->fetch_assoc()['count'];
$stats['service_plans'] = $conn->query("SELECT COUNT(*) as count FROM service_plans")->fetch_assoc()['count'];
$stats['owner_payments'] = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM owner_payments")->fetch_assoc()['total'];

// Get recent owners
$recent_owners = $conn->query("SELECT * FROM building_owners ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
	<link rel="icon" type="image/png" href="../icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            width: 250px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #34495e;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
        }
        .stat-card i {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-3">
            <h4><i class="fas fa-user-shield"></i> Admin Panel</h4>
            <hr>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="owners.php">
                <i class="fas fa-user-tie"></i> Building Owners
            </a>
            <a class="nav-link" href="service_plans.php">
                <i class="fas fa-clipboard-list"></i> Service Plans
            </a>
			<a class="nav-link" href="owner_payments.php">
				<i class="fas fa-money-bill"></i> Payments
            </a>
            <a class="nav-link" href="profile.php">
                <i class="fas fa-user-circle"></i> Profile
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard Overview</h2>
            <span>Welcome, <?php echo $_SESSION['admin_username']; ?>!</span>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><?php echo $stats['owners']; ?></h3>
                            <p class="mb-0">Building Owners</p>
                        </div>
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><?php echo $stats['buildings']; ?></h3>
                            <p class="mb-0">Total Buildings</p>
                        </div>
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><?php echo $stats['service_plans']; ?></h3>
                            <p class="mb-0">Active Service Plans</p>
                        </div>
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><?php echo number_format($stats['owner_payments'], 2); ?></h3>
                            <p class="mb-0">Total Owner Payments</p>
                        </div>
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5><i class="fas fa-user-tie"></i> Recent Building Owners</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($owner = $recent_owners->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $owner['id']; ?></td>
                                    <td><?php echo htmlspecialchars($owner['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($owner['email']); ?></td>
                                    <td><?php echo htmlspecialchars($owner['phone']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $owner['status'] == 'active' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($owner['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($owner['created_at'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>