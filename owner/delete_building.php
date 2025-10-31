<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('buildings.php');
}

$building_id = $_GET['id'];

// Check if the building belongs to the owner
$stmt = $conn->prepare("SELECT * FROM buildings WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $building_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$building = $result->fetch_assoc();

if (!$building) {
    $_SESSION['error'] = 'Building not found.';
    redirect('buildings.php');
}

// Start a transaction
$conn->begin_transaction();

try {
    // Delete tenants associated with the building
    $conn->query("DELETE FROM tenants WHERE floor_id IN (SELECT id FROM floors WHERE building_id = $building_id)");

    // Delete floors associated with the building
    $conn->query("DELETE FROM floors WHERE building_id = $building_id");

    // Delete the building
    $stmt = $conn->prepare("DELETE FROM buildings WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $building_id, $owner_id);
    $stmt->execute();

    // Commit the transaction
    $conn->commit();

    $_SESSION['success'] = 'Building and all associated data deleted successfully!';
} catch (Exception $e) {
    // Rollback the transaction if something went wrong
    $conn->rollback();
    $_SESSION['error'] = 'Failed to delete building. Error: ' . $e->getMessage();
}

redirect('buildings.php');
?>