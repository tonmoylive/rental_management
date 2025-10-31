<?php
require_once '../config.php';
global $conn;

if (!isLoggedIn('admin')) {
    redirect('login.php');
}

$message = '';
$message_type = '';

if (isset($_SESSION['admin_message'])) {
    $message = $_SESSION['admin_message'];
    $message_type = $_SESSION['admin_message_type'];
    unset($_SESSION['admin_message']);
    unset($_SESSION['admin_message_type']);
}

// Fetch all owner payments
$owner_payments_query = $conn->query("SELECT op.*, bo.full_name as owner_name, sp.name as plan_name FROM owner_payments op JOIN building_owners bo ON op.owner_id = bo.id JOIN service_plans sp ON op.plan_id = sp.id ORDER BY op.payment_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Owner Payments</title>
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
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="owners.php">
                <i class="fas fa-user-tie"></i> Building Owners
            </a>
            <a class="nav-link" href="service_plans.php">
                <i class="fas fa-clipboard-list"></i> Service Plans
            </a>
            <a class="nav-link active" href="owner_payments.php">
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
            <h2>Owner Payments</h2>
            <span>Welcome, <?php echo $_SESSION['admin_username']; ?>!</span>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-white">
                <h5><i class="fas fa-money-bill"></i> All Owner Payments</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Owner Name</th>
                            <th>Service Plan</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Transaction ID</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($owner_payments_query->num_rows > 0): ?>
                            <?php while ($payment = $owner_payments_query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $payment['id']; ?></td>
                                <td><?php echo htmlspecialchars($payment['owner_name']); ?></td>
                                <td><?php echo htmlspecialchars($payment['plan_name']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($payment['amount'], 2)); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($payment['payment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                                <td>
                                    <?php
                                        $payment_status = 'unknown'; // Default value
                                        if (isset($payment['status'])) {
                                            $payment_status = $payment['status'];
                                        }
                                        $status_class = '';
                                        switch ($payment_status) {
                                            case 'pending':
                                                $status_class = 'warning';
                                                break;
                                            case 'completed':
                                                $status_class = 'success';
                                                break;
                                            case 'failed':
                                                $status_class = 'danger';
                                                break;
                                            default:
                                                $status_class = 'secondary';
                                                break;
                                        }
                                    ?>
                                    <span class="badge bg-<?php echo $status_class; ?>">
                                        <?php echo ucfirst($payment_status); ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Actions for owner payments (e.g., view details, mark as completed/failed if pending) -->
                                    <?php if ($payment['status'] == 'pending'): ?>
                                        <a href="#" class="btn btn-sm btn-success me-1" title="Mark as Completed"><i class="fas fa-check"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger" title="Mark as Failed"><i class="fas fa-times"></i></a>
                                    <?php else: ?>
                                        <span class="text-muted">No actions</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No owner payments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>