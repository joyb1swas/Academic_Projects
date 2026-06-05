<?php
require_once 'config/database.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$where = "WHERE 1=1";
$params = [];
if ($status_filter) {
    $where .= " AND r.status = ?";
    $params[] = $status_filter;
}
if ($category_filter) {
    $where .= " AND r.category_id = ?";
    $params[] = $category_filter;
}

$count_sql = "SELECT COUNT(*) FROM reports r $where";
$total = $pdo->prepare($count_sql);
$total->execute($params);
$total_reports = $total->fetchColumn();
$total_pages = ceil($total_reports / $per_page);

$sql = "SELECT r.*, u.username, c.name AS category_name 
        FROM reports r 
        JOIN users u ON r.user_id = u.id 
        JOIN categories c ON r.category_id = c.id 
        $where 
        ORDER BY r.created_at DESC 
        LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<div class="hero">
    <h1>Community Problem Reporting System</h1>
    <p>Report issues in your community and help make your neighborhood better.</p>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="report.php" class="btn btn-primary btn-lg">Submit a Report</a>
    <?php else: ?>
        <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
        <a href="login.php" class="btn btn-secondary btn-lg">Login</a>
    <?php endif; ?>
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
        <a href="index.php" class="btn btn-secondary">Clear</a>
    </form>
</div>

<div class="report-grid">
    <?php if (empty($reports)): ?>
        <div class="empty-state full-width">
            <p>No reports found. Be the first to report an issue!</p>
        </div>
    <?php else: ?>
        <?php foreach ($reports as $report): ?>
            <div class="report-card">
                <?php if ($report['image']): ?>
                    <img src="uploads/<?= htmlspecialchars($report['image']) ?>" alt="Report image" class="report-card-img">
                <?php endif; ?>
                <div class="report-card-body">
                    <h3><a href="report-detail.php?id=<?= $report['id'] ?>"><?= htmlspecialchars($report['title']) ?></a></h3>
                    <span class="badge badge-<?= $report['status'] ?>"><?= ucfirst(str_replace('_', ' ', $report['status'])) ?></span>
                    <p class="meta">
                        <strong>Category:</strong> <?= htmlspecialchars($report['category_name']) ?><br>
                        <strong>Location:</strong> <?= htmlspecialchars($report['location']) ?><br>
                        <strong>By:</strong> <?= htmlspecialchars($report['username']) ?><br>
                        <strong>Date:</strong> <?= date('M j, Y', strtotime($report['created_at'])) ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&category=<?= $category_filter ?>" 
               class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-secondary' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
