<?php
require_once '../config.php';

if (!isLoggedIn('admin')) {
    redirect('login.php');
}

// Fetch all buildings
$buildings_query = $conn->query("SELECT b.*, bo.full_name as owner_name FROM buildings b JOIN building_owners bo ON b.owner_id = bo.id ORDER BY b.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - All Buildings</title>
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
            <a class="nav-link active" href="buildings.php">
                <i class="fas fa-building"></i> All Buildings
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
            <h2>All Buildings</h2>
            <span>Welcome, <?php echo $_SESSION['admin_username']; ?>!</span>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5><i class="fas fa-building"></i> List of Buildings</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Owner</th>
                            <th>Units</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($buildings_query->num_rows > 0): ?>
                            <?php while ($building = $buildings_query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $building['id']; ?></td>
                                <td><?php echo htmlspecialchars($building['name']); ?></td>
                                <td><?php echo htmlspecialchars($building['address']); ?></td>
                                <td><?php echo htmlspecialchars($building['owner_name']); ?></td>
                                <td><?php echo htmlspecialchars($building['total_units']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($building['created_at'])); ?></td>
                                <td>
                                    <a href="edit_building.php?id=<?php echo $building['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="delete_building.php?id=<?php echo $building['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this building?');"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No buildings found.</td>
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