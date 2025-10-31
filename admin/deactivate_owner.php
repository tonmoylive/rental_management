<?php
require_once '../config.php';
global $conn;

if (!isLoggedIn('admin')) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $owner_id = (int)$_GET['id'];

    // Update owner's account status to 'inactive' (or a similar status for deactivation)
    // Note: The ENUM for building_owners status is 'active', 'inactive'.
    // We are using account_status for approval workflow, and status for active/inactive state.
    $stmt = $conn->prepare("UPDATE building_owners SET account_status = 'deactivated', status = 'inactive' WHERE id = ?");
    $stmt->bind_param("i", $owner_id);

    if ($stmt->execute()) {
        $_SESSION['admin_message'] = 'Owner account deactivated successfully.';
        $_SESSION['admin_message_type'] = 'warning';
    } else {
        $_SESSION['admin_message'] = 'Error deactivating owner account: ' . $conn->error;
        $_SESSION['admin_message_type'] = 'danger';
    }
    $stmt->close();
}

redirect('owners.php');
?>