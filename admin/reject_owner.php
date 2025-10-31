<?php
require_once '../config.php';
global $conn;

if (!isLoggedIn('admin')) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $owner_id = (int)$_GET['id'];

    // Update owner's account status to 'rejected'
    $stmt = $conn->prepare("UPDATE building_owners SET account_status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $owner_id);

    if ($stmt->execute()) {
        // Optionally, you might want to delete or mark the pending payment as failed
        $payment_stmt = $conn->prepare("UPDATE owner_payments SET status = 'failed' WHERE owner_id = ? AND status = 'pending'");
        $payment_stmt->bind_param("i", $owner_id);
        $payment_stmt->execute();
        $payment_stmt->close();

        $_SESSION['admin_message'] = 'Owner account rejected.';
        $_SESSION['admin_message_type'] = 'warning';
    } else {
        $_SESSION['admin_message'] = 'Error rejecting owner account: ' . $conn->error;
        $_SESSION['admin_message_type'] = 'danger';
    }
    $stmt->close();
}

redirect('owners.php');
?>