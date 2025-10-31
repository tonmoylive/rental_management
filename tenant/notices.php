<?php
require_once '../config.php';

if (!isLoggedIn('tenant')) {
    redirect('login.php');
}

$tenant_id = $_SESSION['tenant_id'];
$floor_id = $_SESSION['tenant_floor'];

// Get building ID
$building_id_query = $conn->query("SELECT building_id FROM floors WHERE id = $floor_id");
$building_id = $building_id_query->fetch_assoc()['building_id'];

// Get notices for the building
$notices = $conn->query("SELECT * FROM notices WHERE building_id = $building_id ORDER BY created_at DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices</title>
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
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .notice-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
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
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link active" href="notices.php">
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
            <h2>Building Notices</h2>
        </div>

        <div class="card">
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
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <p>There are no notices for your building at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
