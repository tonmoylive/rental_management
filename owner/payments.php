<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];

// Handle status update
if (isset($_POST['update_status'])) {
    $payment_id = (int)$_POST['payment_id'];
    $status = $_POST['status'];

    // Validate that the payment belongs to the owner
    $stmt = $conn->prepare("SELECT p.id FROM payments p JOIN floors f ON p.floor_id = f.id JOIN buildings b ON f.building_id = b.id WHERE p.id = ? AND b.owner_id = ?");
    $stmt->bind_param("ii", $payment_id, $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $update_stmt = $conn->prepare("UPDATE payments SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $status, $payment_id);
        $update_stmt->execute();
    }
    redirect('payments.php');
}

// Get all payments for the owner's buildings
$payments_query = $conn->query("
    SELECT p.*, t.full_name as tenant_name, b.building_name, f.floor_name
    FROM payments p
    JOIN tenants t ON p.tenant_id = t.id
    JOIN floors f ON p.floor_id = f.id
    JOIN buildings b ON f.building_id = b.id
    WHERE b.owner_id = $owner_id
    ORDER BY p.payment_date DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
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
            <a class="nav-link" href="dashboard.php"> <i class="fas fa-home"></i> Dashboard </a>
            <a class="nav-link" href="buildings.php"> <i class="fas fa-building"></i> My Buildings </a>
            <a class="nav-link" href="tenants.php"> <i class="fas fa-users"></i> Tenants </a>
            <a class="nav-link" href="notices.php"> <i class="fas fa-bell"></i> Notices </a>
            <a class="nav-link" href="rules.php"> <i class="fas fa-gavel"></i> Rules </a>
            <a class="nav-link active" href="payments.php"> <i class="fas fa-money-bill"></i> Payments </a>
            <a class="nav-link" href="profile.php"> <i class="fas fa-user"></i> Profile </a>
            <a class="nav-link" href="logout.php"> <i class="fas fa-sign-out-alt"></i> Logout </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-money-bill"></i> Payment History</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if ($payments_query->num_rows > 0): ?>
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>Building</th>
                            <th>Floor</th>
                            <th>Amount</th>
                            <th>Payment Month</th>
                            <th>Payment Date</th>
                            <th>Method</th>
                            <th>Transaction ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments_query->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['tenant_name']); ?></td>
                            <td><?php echo htmlspecialchars($payment['building_name']); ?></td>
                            <td><?php echo htmlspecialchars($payment['floor_name']); ?></td>
                            <td><?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo date('F Y', strtotime($payment['payment_month'] . '-01')); ?></td>
                            <td><?php echo date('F j, Y', strtotime($payment['payment_date'])); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                            <td>
                                <form method="POST" class="d-flex">
                                    <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                    <select name="status" class="form-select form-select-sm me-2">
                                        <option value="pending" <?php if ($payment['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="completed" <?php if ($payment['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                        <option value="failed" <?php if ($payment['status'] == 'failed') echo 'selected'; ?>>Failed</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-sm btn-primary"> <i class="fas fa-check"></i> </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No payment history found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>