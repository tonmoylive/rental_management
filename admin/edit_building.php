<?php
// edit_building.php - Placeholder for future functionality
require_once '../config.php';
if (!isLoggedIn('admin')) {
    redirect('login.php');
}

$building_id = $_GET['id'] ?? null;

// In a real application, you would fetch building data and display an edit form here.
// For now, it's just a placeholder.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Building</title>
	<link rel="icon" type="image/png" href="../icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Building (ID: <?php echo htmlspecialchars($building_id); ?>)</h2>
        <p>This is a placeholder page for editing building details.</p>
        <a href="buildings.php" class="btn btn-primary">Back to Buildings</a>
    </div>
</body>
</html>