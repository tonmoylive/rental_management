<?php
require_once '../config.php';
global $conn;

$error = '';
$success = '';

// Fetch service plans
$service_plans = [];
$plans_query = $conn->query("SELECT * FROM service_plans ORDER BY price ASC");
while ($row = $plans_query->fetch_assoc()) {
    $service_plans[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $plan_id = (int)$_POST['plan_id'];

    // Get plan details for payment amount
    $selected_plan = null;
    foreach ($service_plans as $plan) {
        if ($plan['id'] == $plan_id) {
            $selected_plan = $plan;
            break;
        }
    }

    if (!$selected_plan) {
        $error = 'Invalid service plan selected.';
    } else {
        // Check if username or email exists
        $check = $conn->prepare("SELECT id FROM building_owners WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $error = 'Username or email already exists';
        } else {
            // Insert owner with pending status and selected plan
            $stmt = $conn->prepare("INSERT INTO building_owners (username, password, full_name, email, phone, address, plan_id, account_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("ssssssi", $username, $password, $full_name, $email, $phone, $address, $plan_id);
            
            if ($stmt->execute()) {
                $owner_id = $conn->insert_id;

                // Simulate payment record (status pending for admin approval)
                $payment_stmt = $conn->prepare("INSERT INTO owner_payments (owner_id, plan_id, amount, status) VALUES (?, ?, ?, 'pending')");
                $payment_stmt->bind_param("iid", $owner_id, $plan_id, $selected_plan['price']);
                $payment_stmt->execute();
                $payment_stmt->close();

                redirect('registration_success.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Registration</title>
	<link rel="icon" type="image/png" href="../icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 1000px;
            width: 90%;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
        }
        .register-header {
            background: rgba(0,0,0,0.2);
            color: white;
            padding: 20px 30px;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }
        .register-header i {
            font-size: 2.5rem;
            margin-bottom: 5px;
        }
        .register-header h3 {
            margin: 10px 0 5px 0;
            font-size: 1.8rem;
        }
        .register-body {
            padding: 30px 40px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .register-body {
                padding: 25px 30px;
            }
            .section-title {
                margin-top: 20px !important;
            }
            .section-title:first-of-type {
                margin-top: 0 !important;
            }
        }
        
        @media (max-width: 767px) {
            body {
                padding: 15px 10px;
            }
            .register-container {
                width: 100%;
            }
            .register-header {
                padding: 15px 20px;
            }
            .register-header i {
                font-size: 2rem;
            }
            .register-header h3 {
                font-size: 1.5rem;
            }
            .register-body {
                padding: 20px;
            }
            .plan-card {
                margin-bottom: 10px;
            }
        }
        
        @media (max-width: 576px) {
            .register-header {
                padding: 15px;
            }
            .register-header h3 {
                font-size: 1.3rem;
            }
            .register-body {
                padding: 15px;
            }
            .form-label {
                font-size: 0.85rem;
            }
            .form-control {
                font-size: 0.9rem;
            }
            .section-title {
                font-size: 1rem;
            }
            .btn-login {
                padding: 10px;
                font-size: 0.95rem;
            }
        }
        .form-control {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 8px 12px;
            font-size: 0.95rem;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.2);
            border-color: #764ba2;
            box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.5);
            color: white;
        }
        .form-label {
            color: white;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        .mb-3 {
            margin-bottom: 15px !important;
        }
		.btn-login {
			background: #764ba2;
			border: none;
			width: 40%;
			padding: 12px;
			font-weight: 600;
			color: white;
			display: block;
			margin: 20px auto 0; /* Centers the button horizontally */
			text-align: center;
		}
        .btn-login:hover {
            background: #563775;
        }
        .plan-card {
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.05);
        }
        .plan-card.selected {
            border-color: #667eea;
            box-shadow: 0 0 8px rgba(102, 126, 234, 0.5);
            background-color: rgba(102, 126, 234, 0.2);
        }
        .plan-card h6 {
            color: white;
            font-size: 1rem;
            margin-bottom: 8px;
        }
        .plan-card p {
            color: white;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        .plan-card small {
            color: #eee;
            font-size: 0.8rem;
        }
        .text-muted {
            color: #eee !important;
        }
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
            padding: 10px;
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 1.1rem;
            margin-top: 15px;
            margin-bottom: 15px;
            color: white;
        }
        textarea.form-control {
            resize: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h3>Owner Registration</h3>
            <p class="mb-0">Join Our Community</p>
        </div>
        <div class="register-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="row">
                    <!-- Left Column: Personal Information -->
                    <div class="col-lg-6">
                        <h5 class="section-title">Personal Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Service Plans -->
                    <div class="col-lg-6">
                        <h5 class="section-title">Select a Service Plan</h5>
                        <div class="row">
                            <?php if (!empty($service_plans)): ?>
                                <?php foreach ($service_plans as $plan): ?>
                                    <div class="col-md-6">
                                        <label class="plan-card d-block">
                                            <input type="radio" name="plan_id" value="<?php echo $plan['id']; ?>" class="d-none" required <?php echo (isset($_POST['plan_id']) && $_POST['plan_id'] == $plan['id']) ? 'checked' : ''; ?>>
                                            <h6><?php echo htmlspecialchars($plan['name']); ?></h6>
                                            <p class="mb-1"><strong>Price:</strong> <?php echo htmlspecialchars(number_format($plan['price'], 2)); ?></p>
                                            <p class="mb-0"><strong>Duration:</strong> <?php echo htmlspecialchars($plan['duration_days']); ?> days</p>
                                            <small class="text-muted d-block mt-1"><?php echo htmlspecialchars($plan['description']); ?></small>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-warning">No service plans available. Please contact support.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-login mt-3">
                    <i class="fas fa-user-plus"></i> Register & Proceed to Payment
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="login.php" class="text-white">Already have an account? Login</a>
                <span class="mx-2">|</span>
                <a href="../index.php" class="text-muted"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.plan-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.plan-card').forEach(innerCard => {
                    innerCard.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
            // Set initial selected state if a plan is pre-selected (e.g., after form submission error)
            if (this.querySelector('input[type="radio"]').checked) {
                this.classList.add('selected');
            }
        });
    </script>
</body>
</html>