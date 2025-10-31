<?php
require_once '../config.php';

if (!isLoggedIn('tenant')) {
    redirect('login.php');
}

$tenant_id = $_SESSION['tenant_id'];
$floor_id = $_SESSION['tenant_floor'];

// Get building details
$floor_query = $conn->query("SELECT b.id as building_id, b.building_name FROM floors f JOIN buildings b ON f.building_id = b.id WHERE f.id = $floor_id");
$building_info = $floor_query->fetch_assoc();
$building_id = $building_info['building_id'];
$building_name = $building_info['building_name'];

// Get rules for the building
$rules = $conn->query("SELECT * FROM rules WHERE building_id = $building_id ORDER BY created_at DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Building Rules</title>
	<link rel="icon" type="image/png" href="../icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #1a6339;
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
            background: #207d48;
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
            <h4><i class="fas fa-user"></i> Tenant Panel</h4>
            <hr>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="notices.php">
                <i class="fas fa-bell"></i> Notices
            </a>
            <a class="nav-link active" href="rules.php">
                <i class="fas fa-gavel"></i> Building Rules
            </a>
            <a class="nav-link" href="payments.php">
                <i class="fas fa-money-bill"></i> My Payments
            </a>
            <a class="nav-link" href="pay_rent.php">
                <i class="fas fa-credit-card"></i> Pay Rent
            </a>
            <a class="nav-link" href="profile.php">
                <i class="fas fa-user-circle"></i> My Profile
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-gavel"></i> Building Rules for <?php echo htmlspecialchars($building_name); ?></h2>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if ($rules->num_rows > 0): ?>
                <div class="accordion" id="rulesAccordion">
                    <?php while ($rule = $rules->fetch_assoc()): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $rule['id']; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $rule['id']; ?>" aria-expanded="false" aria-controls="collapse<?php echo $rule['id']; ?>">
                                <?php echo htmlspecialchars($rule['rule_title']); ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $rule['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $rule['id']; ?>" data-bs-parent="#rulesAccordion">
                            <div class="accordion-body">
                                <?php echo nl2br(htmlspecialchars($rule['rule_content'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-gavel fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No rules have been set for this building yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
