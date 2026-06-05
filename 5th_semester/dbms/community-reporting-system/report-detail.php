<?php
require_once 'config/database.php';

$report_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT r.*, u.username, c.name AS category_name 
    FROM reports r 
    JOIN users u ON r.user_id = u.id 
    JOIN categories c ON r.category_id = c.id 
    WHERE r.id = ?
");
$stmt->execute([$report_id]);
$report = $stmt->fetch();

if (!$report) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.report_id = ? 
    ORDER BY c.created_at ASC
");
$stmt->execute([$report_id]);
$comments = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (report_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$report_id, $_SESSION['user_id'], $comment]);
        header("Location: report-detail.php?id=$report_id");
        exit;
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="report-detail">
    <div class="detail-header">
        <h2><?= htmlspecialchars($report['title']) ?></h2>
        <span class="badge badge-<?= $report['status'] ?>"><?= ucfirst(str_replace('_', ' ', $report['status'])) ?></span>
    </div>

    <div class="detail-meta">
        <p><strong>Category:</strong> <?= htmlspecialchars($report['category_name']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($report['location']) ?></p>
        <p><strong>Reported by:</strong> <?= htmlspecialchars($report['username']) ?></p>
        <p><strong>Submitted:</strong> <?= date('F j, Y g:i A', strtotime($report['created_at'])) ?></p>
        <?php if ($report['updated_at'] !== $report['created_at']): ?>
            <p><strong>Last updated:</strong> <?= date('F j, Y g:i A', strtotime($report['updated_at'])) ?></p>
        <?php endif; ?>
    </div>

    <?php if ($report['image']): ?>
        <div class="detail-image">
            <img src="uploads/<?= htmlspecialchars($report['image']) ?>" alt="Report image">
        </div>
    <?php endif; ?>

    <div class="detail-description">
        <h3>Description</h3>
        <p><?= nl2br(htmlspecialchars($report['description'])) ?></p>
    </div>

    <div class="comments-section">
        <h3>Comments (<?= count($comments) ?>)</h3>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" class="comment-form">
                <div class="form-group">
                    <textarea name="comment" rows="3" placeholder="Add a comment..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Login</a> to post a comment.</p>
        <?php endif; ?>

        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <p class="no-comments">No comments yet.</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <strong><?= htmlspecialchars($comment['username']) ?></strong>
                            <span class="comment-date"><?= date('M j, Y g:i A', strtotime($comment['created_at'])) ?></span>
                        </div>
                        <p class="comment-body"><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <a href="javascript:history.back()" class="btn btn-secondary">&larr; Back</a>
</div>

<?php require_once 'includes/footer.php'; ?>
