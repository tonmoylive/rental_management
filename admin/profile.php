<?php
require_once '../config.php';
global $conn;

if (!isLoggedIn('admin')) {
    redirect('login.php');
}

$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'];
$message = '';
$message_type = '';

// Fetch admin details for current password verification
$stmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$current_hashed_password = $admin['password'];
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $message = 'All password fields are required.';
        $message_type = 'danger';
    } elseif (!password_verify($current_password, $current_hashed_password)) {
        $message = 'Current password is incorrect.';
        $message_type = 'danger';
    } elseif ($new_password !== $confirm_new_password) {
        $message = 'New password and confirm new password do not match.';
        $message_type = 'danger';
    } elseif (strlen($new_password) < 6) {
        $message = 'New password must be at least 6 characters long.';
        $message_type = 'danger';
    } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_hashed_password, $admin_id);

        if ($stmt->execute()) {
            $message = 'Password changed successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error changing password: ' . $conn->error;
            $message_type = 'danger';
        }
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Profile</title>
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
            <a class="nav-link" href="owner_payments.php">
                <i class="fas fa-money-bill"></i> Payments
            </a>
            <a class="nav-link active" href="profile.php">
                <i class="fas fa-user-circle"></i> Profile
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Admin Profile</h2>
            <span>Welcome, <?php echo $_SESSION['admin_username']; ?>!</span>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5><i class="fas fa-user-circle"></i> Your Profile Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($admin_username); ?></p>
                <p><strong>Admin ID:</strong> <?php echo htmlspecialchars($admin_id); ?></p>
                <!-- Add more profile information here as needed -->
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5><i class="fas fa-key"></i> Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>