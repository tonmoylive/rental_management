<?php
require_once '../config.php';
global $conn;

if (!isLoggedIn('admin')) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $plan_id = (int)$_GET['id'];

    // Check if any owners are currently using this plan
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM building_owners WHERE plan_id = ?");
    $check_stmt->bind_param("i", $plan_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $count = $result->fetch_row()[0];
    $check_stmt->close();

    if ($count > 0) {
        $_SESSION['admin_message'] = 'Cannot delete service plan: It is currently assigned to ' . $count . ' owner(s). Please reassign or delete those owners first.';
        $_SESSION['admin_message_type'] = 'danger';
    } else {
        $stmt = $conn->prepare("DELETE FROM service_plans WHERE id = ?");
        $stmt->bind_param("i", $plan_id);

        if ($stmt->execute()) {
            $_SESSION['admin_message'] = 'Service plan deleted successfully.';
            $_SESSION['admin_message_type'] = 'success';
        } else {
            $_SESSION['admin_message'] = 'Error deleting service plan: ' . $conn->error;
            $_SESSION['admin_message_type'] = 'danger';
        }
        $stmt->close();
    }
}

redirect('service_plans.php');
?>