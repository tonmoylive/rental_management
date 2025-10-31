<?php
require_once '../config.php';
global $conn;

if (!isLoggedIn('admin')) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $owner_id = (int)$_GET['id'];

    // Update owner's account status to 'approved'
    $stmt = $conn->prepare("UPDATE building_owners SET account_status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $owner_id);

    if ($stmt->execute()) {
        // Also update the corresponding payment status to 'completed'
        $payment_stmt = $conn->prepare("UPDATE owner_payments SET status = 'completed' WHERE owner_id = ? AND status = 'pending'");
        $payment_stmt->bind_param("i", $owner_id);
        $payment_stmt->execute();
        $payment_stmt->close();

        $_SESSION['admin_message'] = 'Owner account approved successfully.';
        $_SESSION['admin_message_type'] = 'success';
    } else {
        $_SESSION['admin_message'] = 'Error approving owner account: ' . $conn->error;
        $_SESSION['admin_message_type'] = 'danger';
    }
    $stmt->close();
}

redirect('owners.php');
?>