<?php
require_once '../config.php';
global $conn;

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];

// Fetch owner's account status
$stmt = $conn->prepare("SELECT account_status FROM building_owners WHERE id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$owner_data = $result->fetch_assoc();
$stmt->close();

if ($owner_data['account_status'] !== 'approved') {
    redirect('pending_approval.php');
}

// Get statistics
$stats = [];
$stats['buildings'] = $conn->query("SELECT COUNT(*) as count FROM buildings WHERE owner_id = $owner_id")->fetch_assoc()['count'];
$stats['floors'] = $conn->query("SELECT COUNT(*) as count FROM floors f JOIN buildings b ON f.building_id = b.id WHERE b.owner_id = $owner_id")->fetch_assoc()['count'];
$stats['tenants'] = $conn->query("SELECT COUNT(*) as count FROM tenants t JOIN floors f ON t.floor_id = f.id JOIN buildings b ON f.building_id = b.id WHERE b.owner_id = $owner_id")->fetch_assoc()['count'];
$stats['revenue'] = $conn->query("SELECT COALESCE(SUM(p.amount), 0) as total FROM payments p JOIN tenants t ON p.tenant_id = t.id JOIN floors f ON t.floor_id = f.id JOIN buildings b ON f.building_id = b.id WHERE b.owner_id = $owner_id AND MONTH(p.payment_date) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];

// Get buildings
$buildings = $conn->query("SELECT * FROM buildings WHERE owner_id = $owner_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard</title>
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
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
        }
        .stat-card i {
            font-size: 2.5rem;
            opacity: 0.8;
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
            <a class="nav-link active" href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="buildings.php">
                <i class="fas fa-building"></i> My Buildings
            </a>
            <a class="nav-link" href="tenants.php">
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
            <h2>Dashboard Overview</h2>
            <span>Welcome, <?php echo $_SESSION['owner_name']; ?>!</span>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><?php echo $stats['buildings']; ?></h3>
                            <p class="mb-0">Buildings</p>
                        </div>
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><?php echo $stats['floors']; ?></h3>
                            <p class="mb-0">Total Floors</p>
                        </div>
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><?php echo $stats['tenants']; ?></h3>
                            <p class="mb-0">Active Tenants</p>
                        </div>
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><?php echo number_format($stats['revenue'], 2); ?></h3>
                            <p class="mb-0">This Month</p>
                        </div>
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-building"></i> My Buildings</h5>
                        <a href="buildings.php?action=add" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Building
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($buildings->num_rows > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Building Name</th>
                                    <th>Address</th>
                                    <th>Total Floors</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($building = $buildings->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($building['building_name']); ?></td>
                                    <td><?php echo htmlspecialchars($building['address']); ?></td>
                                    <td><?php echo $building['total_floors']; ?></td>
                                    <td>
                                        <a href="building_details.php?id=<?php echo $building['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p>No buildings added yet. <a href="buildings.php?action=add">Add your first building</a></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>