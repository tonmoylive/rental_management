<?php
require_once '../config.php';

// Ensure owner is logged in, but don't redirect if not approved yet
if (!isset($_SESSION['owner_id'])) {
    redirect('login.php');
}

// Optionally, fetch owner's current status to display more specific message
global $conn;
$owner_id = $_SESSION['owner_id'];
$stmt = $conn->prepare("SELECT account_status FROM building_owners WHERE id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$owner_data = $result->fetch_assoc();
$stmt->close();

$status_message = "Your account is currently pending administrator approval.";
if ($owner_data && $owner_data['account_status'] == 'rejected') {
    $status_message = "Your account has been rejected. Please contact support for more information.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Pending Approval</title>
	<link rel="icon" type="image/png" href="../icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .status-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 600px;
            padding: 40px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="status-container">
        <i class="fas fa-hourglass-half fa-5x text-warning mb-4"></i>
        <h2>Account Status</h2>
        <p class="lead"><?php echo htmlspecialchars($status_message); ?></p>
        <p>We appreciate your patience. You will be notified once your account status changes.</p>
        <a href="logout.php" class="btn btn-primary mt-3">Logout</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>