<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];
$success = '';
$error = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('buildings.php');
}

$building_id = $_GET['id'];

// Check if the building belongs to the owner
$stmt = $conn->prepare("SELECT * FROM buildings WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $building_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$building = $result->fetch_assoc();

if (!$building) {
    redirect('buildings.php');
}

if (isset($_POST['update_building'])) {
    $building_name = sanitize($_POST['building_name']);
    $address = sanitize($_POST['address']);
    $total_floors = intval($_POST['total_floors']);
    $description = sanitize($_POST['description']);

    $stmt = $conn->prepare("UPDATE buildings SET building_name = ?, address = ?, total_floors = ?, description = ? WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("ssisii", $building_name, $address, $total_floors, $description, $building_id, $owner_id);

    if ($stmt->execute()) {
        $success = 'Building updated successfully!';

        // Get current number of floors
        $floor_count_result = $conn->query("SELECT COUNT(*) as count FROM floors WHERE building_id = $building_id");
        $current_floor_count = $floor_count_result->fetch_assoc()['count'];

        if ($total_floors > $current_floor_count) {
            // Add new floors
            $stmt = $conn->prepare("INSERT INTO floors (building_id, floor_number, floor_name) VALUES (?, ?, ?)");
            for ($i = $current_floor_count + 1; $i <= $total_floors; $i++) {
                $floor_name = "Floor " . $i;
                $stmt->bind_param("iis", $building_id, $i, $floor_name);
                $stmt->execute();
            }
        } elseif ($total_floors < $current_floor_count) {
            // Remove extra floors
            $conn->query("DELETE FROM floors WHERE building_id = $building_id AND floor_number > $total_floors");
        }


        // Refresh building data
        $stmt = $conn->prepare("SELECT * FROM buildings WHERE id = ? AND owner_id = ?");
        $stmt->bind_param("ii", $building_id, $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $building = $result->fetch_assoc();
    } else {
        $error = 'Failed to update building.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Building</title>
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
            <h2><i class="fas fa-edit"></i> Edit Building</h2>
            <a href="buildings.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back to Buildings</a>
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

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Building Name</label>
                        <input type="text" name="building_name" class="form-control" value="<?php echo htmlspecialchars($building['building_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($building['address']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Floors</label>
                        <input type="number" name="total_floors" class="form-control" min="1" value="<?php echo $building['total_floors']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($building['description']); ?></textarea>
                    </div>
                    <button type="submit" name="update_building" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Building
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
