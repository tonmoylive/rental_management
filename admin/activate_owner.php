<?php
require_once '../config.php';
global $conn;

if (!isLoggedIn('admin')) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $owner_id = (int)$_GET['id'];

    // Update owner's account status to 'approved' and status to 'active'
    $stmt = $conn->prepare("UPDATE building_owners SET account_status = 'approved', status = 'active' WHERE id = ?");
    $stmt->bind_param("i", $owner_id);

    if ($stmt->execute()) {
        $_SESSION['admin_message'] = 'Owner account activated successfully.';
        $_SESSION['admin_message_type'] = 'success';
    } else {
        $_SESSION['admin_message'] = 'Error activating owner account: ' . $conn->error;
        $_SESSION['admin_message_type'] = 'danger';
    }
    $stmt->close();
}

redirect('owners.php');
?>