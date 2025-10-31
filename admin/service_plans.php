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

// Fetch all service plans
$service_plans_query = $conn->query("SELECT * FROM service_plans ORDER BY price ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Service Plans</title>
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
            <a class="nav-link active" href="service_plans.php">
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
            <h2>Service Plans</h2>
            <span>Welcome, <?php echo $_SESSION['admin_username']; ?>!</span>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-clipboard-list"></i> Manage Service Plans</h5>
                <a href="add_service_plan.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Plan
                </a>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Duration (Days)</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($service_plans_query->num_rows > 0): ?>
                            <?php while ($plan = $service_plans_query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $plan['id']; ?></td>
                                <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                <td><?php echo htmlspecialchars($plan['description']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($plan['price'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($plan['duration_days']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($plan['created_at'])); ?></td>
                                <td>
                                    <a href="edit_service_plan.php?id=<?php echo $plan['id']; ?>" class="btn btn-sm btn-primary me-1"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="delete_service_plan.php?id=<?php echo $plan['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service plan?');"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No service plans found.</td>
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