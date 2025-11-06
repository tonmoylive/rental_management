<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];
$rule_id = $_GET['id'];

// Fetch the rule to edit
$stmt = $conn->prepare("SELECT r.id, r.building_id, r.rule_title, r.rule_content FROM rules r JOIN buildings b ON r.building_id = b.id WHERE r.id = ? AND b.owner_id = ?");
$stmt->bind_param("ii", $rule_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$rule = $result->fetch_assoc();

if (!$rule) {
    // Rule not found or doesn't belong to the owner
    redirect('rules.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $building_id = $_POST['building_id'];
    $rule_title = $_POST['rule_title'];
    $rule_content = $_POST['rule_content'];

    // Validate that the building belongs to the owner
    $stmt = $conn->prepare("SELECT id FROM buildings WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $building_id, $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE rules SET building_id = ?, rule_title = ?, rule_content = ? WHERE id = ?");
        $stmt->bind_param("issi", $building_id, $rule_title, $rule_content, $rule_id);
        $stmt->execute();
        redirect('rules.php');
    }
}

// Get buildings for the dropdown
$buildings = $conn->query("SELECT id, building_name FROM buildings WHERE owner_id = $owner_id");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rule</title>
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
            <a class="nav-link" href="tenants.php">
                <i class="fas fa-users"></i> Tenants
            </a>
            <a class="nav-link" href="notices.php">
                <i class="fas fa-bell"></i> Notices
            </a>
            <a class="nav-link active" href="rules.php">
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
            <h2><i class="fas fa-edit"></i> Edit Rule</h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
					<div class="card-header text-white" style="background-color: #3498db;">
						<h5><i class="fas fa-edit"></i> Edit Rule</h5>
					</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="building_id" class="form-label">Select Building</label>
                                <select class="form-select" id="building_id" name="building_id" required>
                                    <option value="">Choose a building...</option>
                                    <?php while ($building = $buildings->fetch_assoc()): ?>
                                    <option value="<?php echo $building['id']; ?>" <?php if ($building['id'] == $rule['building_id']) echo 'selected'; ?>><?php echo htmlspecialchars($building['building_name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="rule_title" class="form-label">Rule Title</label>
                                <input type="text" class="form-control" id="rule_title" name="rule_title" value="<?php echo htmlspecialchars($rule['rule_title']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="rule_content" class="form-label">Rule Details</label>
                                <textarea class="form-control" id="rule_content" name="rule_content" rows="5" required><?php echo htmlspecialchars($rule['rule_content']); ?></textarea>
                            </div>
                            <button type="submit" name="update_rule" class="btn btn-primary w-40">
                                <i class="fas fa-save"></i> Update Rule
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
