<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];
$success = '';
$error = '';

// Handle profile update
if (isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);

    $stmt = $conn->prepare("UPDATE building_owners SET full_name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $email, $phone, $owner_id);
    if ($stmt->execute()) {
        $_SESSION['owner_name'] = $full_name;
        $success = 'Profile updated successfully!';
    } else {
        $error = 'Failed to update profile.';
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $owner_query = $conn->query("SELECT password FROM building_owners WHERE id = $owner_id");
    $owner_data = $owner_query->fetch_assoc();

    if (password_verify($current_password, $owner_data['password'])) {
        if ($new_password == $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $conn->query("UPDATE building_owners SET password = '$hashed_password' WHERE id = $owner_id");
            $success = 'Password changed successfully!';
        } else {
            $error = 'New passwords do not match.';
        }
    } else {
        $error = 'Incorrect current password.';
    }
}

// Get owner data
$owner_query = $conn->query("SELECT * FROM building_owners WHERE id = $owner_id");
$owner = $owner_query->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
	<link rel="icon" type="image/png" href="../icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #2776ab;
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
            background: #2980b9;
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
            <h4><i class="fas fa-user-tie"></i> Owner Panel</h4>
            <hr>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php"> <i class="fas fa-home"></i> Dashboard </a>
            <a class="nav-link" href="buildings.php"> <i class="fas fa-building"></i> My Buildings </a>
            <a class="nav-link" href="tenants.php"> <i class="fas fa-users"></i> Tenants </a>
            <a class="nav-link" href="notices.php"> <i class="fas fa-bell"></i> Notices </a>
            <a class="nav-link" href="rules.php"> <i class="fas fa-gavel"></i> Rules </a>
            <a class="nav-link" href="payments.php"> <i class="fas fa-money-bill"></i> Payments </a>
            <a class="nav-link active" href="profile.php"> <i class="fas fa-user"></i> Profile </a>
            <a class="nav-link" href="logout.php"> <i class="fas fa-sign-out-alt"></i> Logout </a>
        </nav>
    </div>

    <div class="main-content">
        <h2 class="mb-4"><i class="fas fa-user"></i> My Profile</h2>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-white" style="background-color: #3498db;">
                        <h5><i class="fas fa-edit"></i> Edit Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($owner['full_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($owner['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($owner['phone']); ?>">
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary w-40">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-white" style="background-color: #3498db;">
                        <h5><i class="fas fa-key"></i> Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-warning w-40">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
