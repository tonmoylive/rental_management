<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];

// Get all tenants for the owner's buildings
$tenants_query = $conn->query("
    SELECT t.*, b.building_name, f.floor_name
    FROM tenants t
    JOIN floors f ON t.floor_id = f.id
    JOIN buildings b ON f.building_id = b.id
    WHERE b.owner_id = $owner_id
    ORDER BY b.building_name, f.floor_name
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tenants</title>
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
            <h2><i class="fas fa-users"></i> My Tenants</h2>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <?php if ($tenants_query->num_rows > 0): ?>
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Building</th>
                            <th>Floor</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Move-in Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($tenant = $tenants_query->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tenant['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['building_name']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['floor_name']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['email']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['phone']); ?></td>
                            <td><?php echo date('F j, Y', strtotime($tenant['move_in_date'])); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $tenant['status'] == 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($tenant['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_tenant.php?id=<?php echo $tenant['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete_tenant.php?id=<?php echo $tenant['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">You don't have any tenants yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
