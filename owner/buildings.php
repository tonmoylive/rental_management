<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];
$success = '';
$error = '';

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Add building
if (isset($_POST['add_building'])) {
    $building_name = sanitize($_POST['building_name']);
    $address = sanitize($_POST['address']);
    $total_floors = intval($_POST['total_floors']);
    $description = sanitize($_POST['description']);
    
    $stmt = $conn->prepare("INSERT INTO buildings (owner_id, building_name, address, total_floors, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issis", $owner_id, $building_name, $address, $total_floors, $description);
    
    if ($stmt->execute()) {
        $building_id = $conn->insert_id;
        
        // Create floors for the building
        for ($i = 1; $i <= $total_floors; $i++) {
            $floor_stmt = $conn->prepare("INSERT INTO floors (building_id, floor_number, floor_name, rent_amount) VALUES (?, ?, ?, 0)");
            $floor_name = "Floor " . $i;
            $floor_stmt->bind_param("iis", $building_id, $i, $floor_name);
            $floor_stmt->execute();
        }
        
        $success = 'Building added successfully!';
    } else {
        $error = 'Failed to add building.';
    }
}

// Get all buildings
$buildings = $conn->query("SELECT * FROM buildings WHERE owner_id = $owner_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Buildings</title>
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
        .building-card {
            transition: transform 0.3s;
        }
        .building-card:hover {
            transform: translateY(-5px);
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
            <a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a class="nav-link active" href="buildings.php"><i class="fas fa-building"></i> My Buildings</a>
            <a class="nav-link" href="tenants.php"><i class="fas fa-users"></i> Tenants</a>
            <a class="nav-link" href="notices.php"><i class="fas fa-bell"></i> Notices</a>
            <a class="nav-link" href="rules.php"><i class="fas fa-gavel"></i> Rules</a>
            <a class="nav-link" href="payments.php"><i class="fas fa-money-bill"></i> Payments</a>
            <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-building"></i> My Buildings</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBuildingModal">
                <i class="fas fa-plus"></i> Add Building
            </button>
        </div>

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
            <?php if ($buildings->num_rows > 0): ?>
                <?php while ($building = $buildings->fetch_assoc()): ?>
                    <?php
                    $building_id = $building['id'];
                    $floor_count = $conn->query("SELECT COUNT(*) as count FROM floors WHERE building_id = $building_id")->fetch_assoc()['count'];
                    $tenant_count = $conn->query("SELECT COUNT(*) as count FROM tenants t JOIN floors f ON t.floor_id = f.id WHERE f.building_id = $building_id")->fetch_assoc()['count'];
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card building-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-building text-primary"></i>
                                    <?php echo htmlspecialchars($building['building_name']); ?>
                                </h5>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($building['address']); ?>
                                </p>
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <h4 class="text-primary"><?php echo $building['total_floors']; ?></h4>
                                        <small class="text-muted">Floors</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success"><?php echo $tenant_count; ?></h4>
                                        <small class="text-muted">Tenants</small>
                                    </div>
                                </div>
                                <a href="building_details.php?id=<?php echo $building['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="edit_building.php?id=<?php echo $building['id']; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_building.php?id=<?php echo $building['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this building? This action cannot be undone.');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-building fa-4x text-muted mb-3"></i>
                            <h5>No Buildings Added Yet</h5>
                            <p class="text-muted">Start by adding your first building</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBuildingModal">
                                <i class="fas fa-plus"></i> Add Building
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Building Modal -->
    <div class="modal fade" id="addBuildingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Building</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Building Name</label>
                            <input type="text" name="building_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Floors</label>
                            <input type="number" name="total_floors" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_building" class="btn btn-primary">
                            <i class="fas fa-save"></i> Add Building
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>