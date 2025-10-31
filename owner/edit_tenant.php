<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];
$tenant_id = $_GET['id'];

// Get tenant details
$tenant_query = $conn->prepare("SELECT t.*, b.building_name, f.floor_name FROM tenants t JOIN floors f ON t.floor_id = f.id JOIN buildings b ON f.building_id = b.id WHERE t.id = ? AND b.owner_id = ?");
$tenant_query->bind_param("ii", $tenant_id, $owner_id);
$tenant_query->execute();
$tenant_result = $tenant_query->get_result();
$tenant = $tenant_result->fetch_assoc();

if (!$tenant) {
    $_SESSION['error_message'] = 'Tenant not found.';
    redirect('tenants.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $move_in_date = $_POST['move_in_date'];
    $status = $_POST['status'];

    $update_query = $conn->prepare("UPDATE tenants SET full_name = ?, email = ?, phone = ?, move_in_date = ?, status = ? WHERE id = ?");
    $update_query->bind_param("sssssi", $full_name, $email, $phone, $move_in_date, $status, $tenant_id);

    if ($update_query->execute()) {
        $_SESSION['success_message'] = 'Tenant details updated successfully.';
        redirect('tenants.php');
    } else {
        $error_message = 'Error updating tenant details.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tenant</title>
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
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="buildings.php">
                <i class="fas fa-building"></i> My Buildings
            </a>
            <a class="nav-link active" href="tenants.php">
                <i class="fas fa-users"></i> Tenants
            </a>
            <a class="nav-link" href="notices.php">
                <i class="fas fa-bell"></i> Notices
            </a>
            <a class="nav-link" href="rules.php">
                <i class="fas fa-gavel"></i> Rules
            </a>
            <a class="nav-link" href="payments.php">
                <i class="fas fa-money-bill"></i> Payments
            </a>
            <a class="nav-link" href="profile.php">
                <i class="fas fa-user"></i> Profile
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Tenant</h2>
            <a href="tenants.php" class="btn btn-secondary">Back to Tenants</a>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($tenant['full_name']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($tenant['email']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($tenant['phone']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="move_in_date" class="form-label">Move-in Date</label>
                                <input type="date" class="form-control" id="move_in_date" name="move_in_date" value="<?php echo htmlspecialchars($tenant['move_in_date']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" <?php echo $tenant['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $tenant['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Update Tenant</button>
                        <a href="tenants.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>