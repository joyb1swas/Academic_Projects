<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.*, c.name AS category_name 
    FROM reports r 
    JOIN categories c ON r.category_id = c.id 
    WHERE r.user_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$reports = $stmt->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2>My Reports</h2>
        <a href="report.php" class="btn btn-primary">+ New Report</a>
    </div>

    <?php if (empty($reports)): ?>
        <div class="empty-state">
            <p>You haven't submitted any reports yet.</p>
            <a href="report.php" class="btn btn-primary">Submit Your First Report</a>
        </div>
    <?php else: ?>
        <div class="report-grid">
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
                            <strong>Submitted:</strong> <?= date('M j, Y g:i A', strtotime($report['created_at'])) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
