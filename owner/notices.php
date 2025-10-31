<?php
require_once '../config.php';

if (!isLoggedIn('owner')) {
    redirect('login.php');
}

$owner_id = $_SESSION['owner_id'];
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$notice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $building_id = (int)$_POST['building_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $notice_type = $conn->real_escape_string($_POST['notice_type']);

    if ($action == 'add') {
        $conn->query("INSERT INTO notices (building_id, title, content, notice_type, created_by) VALUES ($building_id, '$title', '$content', '$notice_type', $owner_id)");
        redirect('notices.php');
    } elseif ($action == 'edit') {
        $conn->query("UPDATE notices SET building_id = $building_id, title = '$title', content = '$content', notice_type = '$notice_type' WHERE id = $notice_id AND created_by = $owner_id");
        redirect('notices.php');
    }
}

// Handle delete action
if ($action == 'delete' && $notice_id > 0) {
    $conn->query("DELETE FROM notices WHERE id = $notice_id AND created_by = $owner_id");
    redirect('notices.php');
}

// Fetch data for forms
$notice = null;
if ($action == 'edit' && $notice_id > 0) {
    $notice_query = $conn->query("SELECT * FROM notices WHERE id = $notice_id AND created_by = $owner_id");
    $notice = $notice_query->fetch_assoc();
}

$buildings = $conn->query("SELECT * FROM buildings WHERE owner_id = $owner_id");

// Fetch notices for the list
$notices = $conn->query("SELECT n.*, b.building_name FROM notices n JOIN buildings b ON n.building_id = b.id WHERE n.created_by = $owner_id ORDER BY n.created_at DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Notices</title>
	<link rel="icon" type="image/png" href="../icon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #2776ab;
            color: white;
            position: fixed;
            width: 250px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #2980b9;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-3">
            <h4><i class="fas fa-user-tie"></i> Owner Panel</h4>
            <hr>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link" href="buildings.php">
                <i class="fas fa-building"></i> My Buildings
            </a>
            <a class="nav-link" href="tenants.php">
                <i class="fas fa-users"></i> Tenants
            </a>
            <a class="nav-link active" href="notices.php">
                <i class="fas fa-bell"></i> Notices
            </a>
            <a class="nav-link" href="rules.php">
                <i class="fas fa-gavel"></i> Rules
            </a>
            <a class="nav-link" href="payments.php">
                <i class="fas fa-money-bill"></i> Payments
            </a>
            <a class="nav-link" href="profile.php">
                <i class="fas fa-user"></i> Profile
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <?php if ($action == 'list'): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Notices</h2>
            <a href="notices.php?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Notice
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if ($notices->num_rows > 0): ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Building</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($notice = $notices->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($notice['building_name']); ?></td>
                            <td><?php echo htmlspecialchars($notice['title']); ?></td>
                            <td><span class="badge bg-info"><?php echo ucfirst($notice['notice_type']); ?></span></td>
                            <td><?php echo date('F j, Y, g:i a', strtotime($notice['created_at'])); ?></td>
                            <td>
                                <a href="notices.php?action=edit&id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="notices.php?action=delete&id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this notice?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                    <p>No notices found. <a href="notices.php?action=add">Add your first notice</a></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php elseif ($action == 'add' || $action == 'edit'): ?>
        <h2><?php echo ucfirst($action); ?> Notice</h2>
        <div class="card">
            <div class="card-body">
                <form action="notices.php?action=<?php echo $action; ?><?php if ($notice_id > 0) echo '&id='.$notice_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="building_id" class="form-label">Building</label>
                        <select class="form-select" id="building_id" name="building_id" required>
                            <option value="">Select Building</option>
                            <?php while ($building = $buildings->fetch_assoc()): ?>
                            <option value="<?php echo $building['id']; ?>" <?php if ($notice && $notice['building_id'] == $building['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($building['building_name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $notice ? htmlspecialchars($notice['title']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required><?php echo $notice ? htmlspecialchars($notice['content']) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notice_type" class="form-label">Notice Type</label>
                        <select class="form-select" id="notice_type" name="notice_type" required>
                            <option value="general" <?php if ($notice && $notice['notice_type'] == 'general') echo 'selected'; ?>>General</option>
                            <option value="urgent" <?php if ($notice && $notice['notice_type'] == 'urgent') echo 'selected'; ?>>Urgent</option>
                            <option value="maintenance" <?php if ($notice && $notice['notice_type'] == 'maintenance') echo 'selected'; ?>>Maintenance</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="notices.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
