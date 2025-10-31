<?php
require_once '../config.php';

if (!isLoggedIn('tenant')) {
    redirect('login.php');
}

$tenant_id = $_SESSION['tenant_id'];
$floor_id = $_SESSION['tenant_floor'];

// Get floor and building details
$floor_query = $conn->query("
    SELECT f.*, b.building_name, b.address, b.id as building_id, b.owner_id
    FROM floors f 
    JOIN buildings b ON f.building_id = b.id 
    WHERE f.id = $floor_id
");
$floor_info = $floor_query->fetch_assoc();

// Get payment stats
$total_paid = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE tenant_id = $tenant_id")->fetch_assoc()['total'];
$this_month_paid = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE tenant_id = $tenant_id AND MONTH(payment_date) = MONTH(CURRENT_DATE()) AND YEAR(payment_date) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'];

// Get recent notices
$notices = $conn->query("SELECT * FROM notices WHERE building_id = {$floor_info['building_id']} ORDER BY created_at DESC LIMIT 5");

// Get recent payments
$payments = $conn->query("SELECT * FROM payments WHERE tenant_id = $tenant_id ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard</title>
	<link rel="icon" type="image/png" href="../icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #1a6339;
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
            background: #207d48;
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
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .notice-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.7rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-3">
            <h4><i class="fas fa-user"></i> Tenant Panel</h4>
            <hr>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="notices.php">
                <i class="fas fa-bell"></i> Notices
            </a>
            <a class="nav-link" href="rules.php">
                <i class="fas fa-gavel"></i> Building Rules
            </a>
            <a class="nav-link" href="payments.php">
                <i class="fas fa-money-bill"></i> My Payments
            </a>
            <a class="nav-link" href="pay_rent.php">
                <i class="fas fa-credit-card"></i> Pay Rent
            </a>
            <a class="nav-link" href="profile.php">
                <i class="fas fa-user-circle"></i> My Profile
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard</h2>
            <span>Welcome, <?php echo $_SESSION['tenant_name']; ?>!</span>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-info-circle"></i> Your Floor Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Building:</strong> <?php echo htmlspecialchars($floor_info['building_name']); ?></p>
                                <p><strong>Floor Number:</strong> <?php echo $floor_info['floor_number']; ?></p>
                                <p><strong>Floor Name:</strong> <?php echo htmlspecialchars($floor_info['floor_name']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Monthly Rent:</strong> <?php echo number_format($floor_info['rent_amount'], 2); ?></p>
                                <p><strong>Maintenance Fee:</strong> <?php echo number_format($floor_info['maintenance_fee'], 2); ?></p>
                                <p><strong>Total Monthly:</strong> <?php echo number_format($floor_info['rent_amount'] + $floor_info['maintenance_fee'], 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h5><i class="fas fa-bell"></i> Recent Notices</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($notices->num_rows > 0): ?>
                            <?php while ($notice = $notices->fetch_assoc()): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6><?php echo htmlspecialchars($notice['title']); ?></h6>
                                    <span class="notice-badge bg-<?php 
                                        echo $notice['notice_type'] == 'urgent' ? 'danger' : 
                                            ($notice['notice_type'] == 'maintenance' ? 'warning' : 'info'); 
                                    ?>">
                                        <?php echo ucfirst($notice['notice_type']); ?>
                                    </span>
                                </div>
                                <p class="mb-1"><?php echo nl2br(htmlspecialchars($notice['content'])); ?></p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($notice['created_at'])); ?>
                                </small>
                            </div>
                            <?php endwhile; ?>
                            <a href="notices.php" class="btn btn-sm btn-primary">View All Notices</a>
                        <?php else: ?>
                            <p class="text-muted">No notices at the moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6>This Month Paid</h6>
                    <h3><?php echo number_format($this_month_paid, 2); ?></h3>
                </div>
                
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h6>Total Paid</h6>
                    <h3><?php echo number_format($total_paid, 2); ?></h3>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h6><i class="fas fa-history"></i> Recent Payments</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($payments->num_rows > 0): ?>
                            <?php while ($payment = $payments->fetch_assoc()): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span><?php echo ucfirst($payment['payment_type']); ?></span>
                                    <strong><?php echo number_format($payment['amount'], 2); ?></strong>
                                </div>
                                <small class="text-muted"><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></small>
                            </div>
                            <?php endwhile; ?>
                            <a href="payments.php" class="btn btn-sm btn-success w-100 mt-2">View All</a>
                        <?php else: ?>
                            <p class="text-muted mb-0">No payment history yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>