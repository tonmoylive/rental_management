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

$owner_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$owner = null;
$service_plans = [];

// Fetch owner data
if ($owner_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM building_owners WHERE id = ?");
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $owner = $result->fetch_assoc();
    $stmt->close();

    if (!$owner) {
        $_SESSION['admin_message'] = 'Owner not found.';
        $_SESSION['admin_message_type'] = 'danger';
        redirect('owners.php');
    }
} else {
    $_SESSION['admin_message'] = 'Invalid owner ID.';
    $_SESSION['admin_message_type'] = 'danger';
    redirect('owners.php');
}

// Fetch service plans for dropdown
$plans_query = $conn->query("SELECT id, name FROM service_plans ORDER BY name ASC");
while ($row = $plans_query->fetch_assoc()) {
    $service_plans[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $plan_id = (int)sanitize($_POST['plan_id']);
    $account_status = sanitize($_POST['account_status']);
    $status = sanitize($_POST['status']);

    // Basic validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($plan_id) || empty($account_status) || empty($status)) {
        $message = 'All fields are required.';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format.';
        $message_type = 'danger';
    } else {
        $stmt = $conn->prepare("UPDATE building_owners SET full_name = ?, email = ?, phone = ?, address = ?, plan_id = ?, account_status = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssissi", $full_name, $email, $phone, $address, $plan_id, $account_status, $status, $owner_id);

        if ($stmt->execute()) {
            $_SESSION['admin_message'] = 'Owner details updated successfully.';
            $_SESSION['admin_message_type'] = 'success';
            redirect('owners.php');
        } else {
            $message = 'Error updating owner: ' . $conn->error;
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
    <title>Admin - Edit Building Owner</title>
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
            <a class="nav-link active" href="owners.php">
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
            <h2>Edit Building Owner</h2>
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
                <h5><i class="fas fa-edit"></i> Edit Owner: <?php echo htmlspecialchars($owner['full_name']); ?></h5>
            </div>
            <div class="card-body">
                <form action="edit_owner.php?id=<?php echo $owner_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($owner['full_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($owner['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($owner['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($owner['address']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="plan_id" class="form-label">Service Plan</label>
                        <select class="form-select" id="plan_id" name="plan_id" required>
                            <option value="">Select a plan</option>
                            <?php foreach ($service_plans as $plan): ?>
                                <option value="<?php echo $plan['id']; ?>" <?php echo ($plan['id'] == $owner['plan_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($plan['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="account_status" class="form-label">Account Status</label>
                        <select class="form-select" id="account_status" name="account_status" required>
                            <option value="pending" <?php echo ($owner['account_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo ($owner['account_status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo ($owner['account_status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Active Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?php echo ($owner['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($owner['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Owner</button>
                    <a href="owners.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>