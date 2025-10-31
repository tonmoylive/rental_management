<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];
$building_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verify ownership
$building_query = $conn->query("SELECT * FROM buildings WHERE id = $building_id AND owner_id = $owner_id");
if ($building_query->num_rows == 0) {
    redirect('buildings.php');
}
$building = $building_query->fetch_assoc();

$success = '';
$error = '';

// Update floor
if (isset($_POST['update_floor'])) {
    $floor_id = intval($_POST['floor_id']);
    $rent_amount = floatval($_POST['rent_amount']);
    $maintenance_fee = floatval($_POST['maintenance_fee']);
    $floor_name = sanitize($_POST['floor_name']);
    
    $stmt = $conn->prepare("UPDATE floors SET rent_amount = ?, maintenance_fee = ?, floor_name = ? WHERE id = ? AND building_id = ?");
    $stmt->bind_param("ddsii", $rent_amount, $maintenance_fee, $floor_name, $floor_id, $building_id);
    
    if ($stmt->execute()) {
        $success = 'Floor updated successfully!';
    }
}

// Add tenant
if (isset($_POST['add_tenant'])) {
    $floor_id = intval($_POST['floor_id']);
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $nid = sanitize($_POST['nid_number']);
    $move_in_date = sanitize($_POST['move_in_date']);
    
    // Check username
    $check = $conn->prepare("SELECT id FROM tenants WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $error = 'Username or email already exists';
    } else {
        $stmt = $conn->prepare("INSERT INTO tenants (floor_id, username, password, full_name, email, phone, nid_number, move_in_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $floor_id, $username, $password, $full_name, $email, $phone, $nid, $move_in_date);
        
        if ($stmt->execute()) {
            // Update floor status
            $conn->query("UPDATE floors SET status = 'occupied' WHERE id = $floor_id");
            $success = 'Tenant added successfully!';
        } else {
            $error = 'Failed to add tenant.';
        }
    }
}

// Get floors
$floors = $conn->query("SELECT f.*, t.id as tenant_id, t.full_name as tenant_name, t.status as tenant_status FROM floors f LEFT JOIN tenants t ON f.id = t.floor_id WHERE f.building_id = $building_id ORDER BY f.floor_number");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Building Details</title>
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
        .sidebar .nav-link:hover {
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
        .floor-card {
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
        }
        .floor-vacant {
            border-left-color: #95a5a6;
        }
        .floor-occupied {
            border-left-color: #2ecc71;
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
            <div>
                <h2><i class="fas fa-building"></i> <?php echo htmlspecialchars($building['building_name']); ?></h2>
                <p class="text-muted mb-0"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($building['address']); ?></p>
            </div>
            <a href="buildings.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
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
            <?php while ($floor = $floors->fetch_assoc()): ?>
            <div class="col-md-6 mb-3">
                <div class="card floor-card <?php echo $floor['status'] == 'occupied' ? 'floor-occupied' : 'floor-vacant'; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($floor['floor_name']); ?></h5>
                                <span class="badge bg-<?php echo $floor['status'] == 'occupied' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($floor['status']); ?>
                                </span>
                            </div>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editFloor<?php echo $floor['id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1"><strong>Floor:</strong> <?php echo $floor['floor_number']; ?></p>
                                <p class="mb-1"><strong>Rent:</strong> <?php echo number_format($floor['rent_amount'], 2); ?></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><strong>&nbsp;</strong></p>
                                <p class="mb-1"><strong>Maintenance:</strong> <?php echo number_format($floor['maintenance_fee'], 2); ?></p>
                            </div>
                        </div>

                        <?php if ($floor['tenant_id']): ?>
                            <hr>
                            <p class="mb-0"><i class="fas fa-user"></i> <strong>Tenant:</strong> <?php echo htmlspecialchars($floor['tenant_name']); ?></p>
                            <a href="tenants.php" class="btn btn-sm btn-info mt-2">View Details</a>
                        <?php else: ?>
                            <hr>
                            <button class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#addTenant<?php echo $floor['id']; ?>">
                                <i class="fas fa-user-plus"></i> Add Tenant
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Edit Floor Modal -->
            <div class="modal fade" id="editFloor<?php echo $floor['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Floor</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="floor_id" value="<?php echo $floor['id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Floor Name</label>
                                    <input type="text" name="floor_name" class="form-control" value="<?php echo htmlspecialchars($floor['floor_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Rent Amount (BDT)</label>
                                    <input type="number" name="rent_amount" class="form-control" step="0.01" value="<?php echo $floor['rent_amount']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Maintenance Fee (BDT)</label>
                                    <input type="number" name="maintenance_fee" class="form-control" step="0.01" value="<?php echo $floor['maintenance_fee']; ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="update_floor" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add Tenant Modal -->
            <div class="modal fade" id="addTenant<?php echo $floor['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Tenant to <?php echo htmlspecialchars($floor['floor_name']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="floor_id" value="<?php echo $floor['id']; ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="full_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NID Number</label>
                                        <input type="text" name="nid_number" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Move-in Date</label>
                                        <input type="date" name="move_in_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="add_tenant" class="btn btn-success">
                                    <i class="fas fa-user-plus"></i> Add Tenant
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>