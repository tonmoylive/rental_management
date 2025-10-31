<?php
require_once '../config.php';

if (!isLoggedIn('tenant')) {
    redirect('login.php');
}

$tenant_id = $_SESSION['tenant_id'];
$floor_id = $_SESSION['tenant_floor'];

// Get floor info
$floor_query = $conn->query("SELECT f.*, b.building_name FROM floors f JOIN buildings b ON f.building_id = b.id WHERE f.id = $floor_id");
$floor_info = $floor_query->fetch_assoc();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_type = sanitize($_POST['payment_type']);
    $amount = floatval($_POST['amount']);
    $payment_month = sanitize($_POST['payment_month']);
    $payment_method = sanitize($_POST['payment_method']);
    $transaction_id = sanitize($_POST['transaction_id']);
    $notes = sanitize($_POST['notes']);
    $payment_date = date('Y-m-d');
    
    $stmt = $conn->prepare("INSERT INTO payments (tenant_id, floor_id, payment_type, amount, payment_month, payment_date, payment_method, transaction_id, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iisdsssss", $tenant_id, $floor_id, $payment_type, $amount, $payment_month, $payment_date, $payment_method, $transaction_id, $notes);
    
    if ($stmt->execute()) {
        $success = 'Payment submitted successfully!';
    } else {
        $error = 'Payment submission failed. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Rent</title>
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
        .amount-btn {
            width: 100%;
            margin-bottom: 10px;
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
            <a class="nav-link" href="rules.php">
                <i class="fas fa-gavel"></i> Building Rules
            </a>
            <a class="nav-link" href="payments.php">
                <i class="fas fa-money-bill"></i> My Payments
            </a>
            <a class="nav-link active" href="pay_rent.php">
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
        <h2 class="mb-4"><i class="fas fa-credit-card"></i> Make Payment</h2>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-file-invoice-dollar"></i> Payment Form</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Type</label>
                                    <select name="payment_type" class="form-select" required>
                                        <option value="rent">Rent</option>
                                        <option value="maintenance">Maintenance Fee</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Month</label>
                                    <input type="month" name="payment_month" class="form-control" value="<?php echo date('Y-m'); ?>" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Amount (BDT)</label>
                                    <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('amount').value = <?php echo $floor_info['rent_amount']; ?>">
                                            Rent: <?php echo number_format($floor_info['rent_amount'], 2); ?>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="document.getElementById('amount').value = <?php echo $floor_info['maintenance_fee']; ?>">
                                            Maintenance: <?php echo number_format($floor_info['maintenance_fee'], 2); ?>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="document.getElementById('amount').value = <?php echo $floor_info['rent_amount'] + $floor_info['maintenance_fee']; ?>">
                                            Total: <?php echo number_format($floor_info['rent_amount'] + $floor_info['maintenance_fee'], 2); ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="bkash">bKash</option>
                                        <option value="nagad">Nagad</option>
                                        <option value="rocket">Rocket</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Transaction ID</label>
                                    <input type="text" name="transaction_id" class="form-control" placeholder="Enter transaction ID">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-40">
                                <i class="fas fa-check"></i> Submit Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6><i class="fas fa-info-circle"></i> Payment Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Building:</strong><br><?php echo htmlspecialchars($floor_info['building_name']); ?></p>
                        <p><strong>Floor:</strong> <?php echo $floor_info['floor_number']; ?></p>
                        <hr>
                        <p><strong>Monthly Rent:</strong><br><?php echo number_format($floor_info['rent_amount'], 2); ?></p>
                        <p><strong>Maintenance Fee:</strong><br><?php echo number_format($floor_info['maintenance_fee'], 2); ?></p>
                        <hr>
                        <p class="mb-0"><strong>Total Monthly:</strong><br>
                            <h4 class="text-success"><?php echo number_format($floor_info['rent_amount'] + $floor_info['maintenance_fee'], 2); ?></h4>
                        </p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Important</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Keep your transaction ID safe</li>
                            <li>Payment confirmation may take 24 hours</li>
                            <li>Contact owner for payment issues</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>