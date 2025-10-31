<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];
$tenant_id = $_GET['id'];

// Get tenant details
$tenant_query = $conn->prepare("SELECT t.* FROM tenants t JOIN floors f ON t.floor_id = f.id JOIN buildings b ON f.building_id = b.id WHERE t.id = ? AND b.owner_id = ?");
$tenant_query->bind_param("ii", $tenant_id, $owner_id);
$tenant_query->execute();
$tenant_result = $tenant_query->get_result();
$tenant = $tenant_result->fetch_assoc();

if (!$tenant) {
    $_SESSION['error_message'] = 'Tenant not found.';
    redirect('tenants.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete_query = $conn->prepare("DELETE FROM tenants WHERE id = ?");
    $delete_query->bind_param("i", $tenant_id);

    if ($delete_query->execute()) {
        $_SESSION['success_message'] = 'Tenant deleted successfully.';
        redirect('tenants.php');
    } else {
        $error_message = 'Error deleting tenant.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Tenant</title>
	<link rel="icon" type="image/png" href="../icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Delete Tenant</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <div class="alert alert-warning">
            Are you sure you want to delete the tenant "<?php echo htmlspecialchars($tenant['full_name']); ?>"?
        </div>
        <form method="POST">
            <button type="submit" class="btn btn-danger">Delete Tenant</button>
            <a href="tenants.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
