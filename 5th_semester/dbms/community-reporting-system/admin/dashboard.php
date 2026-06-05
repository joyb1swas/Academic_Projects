<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $report_id = (int)$_GET['id'];
    if ($_GET['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
        $stmt->execute([$report_id]);
    } elseif ($_GET['action'] === 'delete_comment' && isset($_GET['comment_id'])) {
        $comment_id = (int)$_GET['comment_id'];
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$comment_id]);
    }
    header('Location: dashboard.php');
    exit;
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "SELECT r.*, u.username, c.name AS category_name 
        FROM reports r 
        JOIN users u ON r.user_id = u.id 
        JOIN categories c ON r.category_id = c.id 
        WHERE 1=1";
$params = [];

if ($status_filter) {
    $sql .= " AND r.status = ?";
    $params[] = $status_filter;
}
if ($category_filter) {
    $sql .= " AND r.category_id = ?";
    $params[] = $category_filter;
}
$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $report_id = (int)$_POST['report_id'];
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE reports SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $report_id]);
    header('Location: dashboard.php');
    exit;
}

$counts = $pdo->query("
    SELECT status, COUNT(*) as count FROM reports GROUP BY status
")->fetchAll();
$total_reports = 0;
$status_counts = ['pending' => 0, 'in_progress' => 0, 'resolved' => 0];
foreach ($counts as $c) {
    $status_counts[$c['status']] = $c['count'];
    $total_reports += $c['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Community Reports</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="../index.php" class="logo">📋 Community Reports</a>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="dashboard.php">Admin Panel</a></li>
                <li><a href="../report.php">Submit Report</a></li>
                <li><a href="../dashboard.php">My Reports</a></li>
                <li><a href="../logout.php">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
            </ul>
        </div>
    </nav>
    <div class="container main-content">

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h2>Admin Dashboard</h2>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3><?= $total_reports ?></h3>
            <p>Total Reports</p>
        </div>
        <div class="stat-card stat-pending">
            <h3><?= $status_counts['pending'] ?></h3>
            <p>Pending</p>
        </div>
        <div class="stat-card stat-progress">
            <h3><?= $status_counts['in_progress'] ?></h3>
            <p>In Progress</p>
        </div>
        <div class="stat-card stat-resolved">
            <h3><?= $status_counts['resolved'] ?></h3>
            <p>Resolved</p>
        </div>
    </div>

    <div class="filters">
        <form method="GET" class="filter-form">
            <select name="status">
                <option value="">All Statuses</option>
                <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="in_progress" <?= $status_filter === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="resolved" <?= $status_filter === 'resolved' ? 'selected' : '' ?>>Resolved</option>
            </select>
            <select name="category">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category_filter === $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <div class="admin-report-list">
        <?php if (empty($reports)): ?>
            <p>No reports found.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>User</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td>#<?= $report['id'] ?></td>
                            <td><a href="../report-detail.php?id=<?= $report['id'] ?>"><?= htmlspecialchars($report['title']) ?></a></td>
                            <td><?= htmlspecialchars($report['username']) ?></td>
                            <td><?= htmlspecialchars($report['category_name']) ?></td>
                            <td><?= htmlspecialchars($report['location']) ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?= $report['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="in_progress" <?= $report['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                        <option value="resolved" <?= $report['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td><?= date('M j, Y', strtotime($report['created_at'])) ?></td>
                            <td>
                                <a href="../report-detail.php?id=<?= $report['id'] ?>" class="btn btn-sm">View</a>
                                <a href="?action=delete&id=<?= $report['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this report?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
