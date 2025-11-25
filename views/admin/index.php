<?php require __DIR__ . '/../layouts/header.php'; ?>

<header class="main-header">
    <h2><?= $t['manage_users']; ?></h2>
    <p><?= $t['manage_users_desc']; ?></p>
</header>

<?php if (isset($feedback)): ?>
    <div class="<?= $feedback['type'] === 'error' ? 'error-message' : 'success-message'; ?>">
        <?= htmlspecialchars($feedback['message']); ?>
    </div>
<?php endif; ?>

<div class="admin-container">
    <div class="card">
        <h3><?= $t['forum_topics']; ?></h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th><?= $t['id']; ?></th>
                    <th><?= $t['title']; ?></th>
                    <th><?= $t['author']; ?></th>
                    <th><?= $t['date']; ?></th>
                    <th><?= $t['status']; ?></th>
                    <th><?= $t['actions']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topics as $topic): ?>
                    <tr id="topic-<?= $topic->id ?>">
                        <td><?= $topic->id; ?></td>
                        <td><?= htmlspecialchars($topic->title); ?></td>
                        <td><?= htmlspecialchars($topic->artist_name ?? 'N/A'); ?></td>
                        <td><?= $topic->created_at ? date('d/m/Y H:i', strtotime($topic->created_at)) : ''; ?></td>
                        <td>
                            <?php if ($topic->is_approved): ?>
                                <span style="color: #28a745;">✓ <?= $t['approved']; ?></span>
                            <?php else: ?>
                                <span style="color: #ffc107;">⏳ <?= $t['pending']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/forum/view?id=<?= $topic->id; ?>" class="btn-small" target="_blank">
                                <i class="fas fa-eye"></i> <?= $t['view']; ?>
                            </a>
                            <?php if (!$topic->is_approved): ?>
                                <button class="btn-small" onclick="approveTopic(<?= $topic->id; ?>)" style="background: rgba(40, 167, 69, 0.2); border-color: #28a745; color: #28a745;">
                                    <i class="fas fa-check"></i> <?= $t['approve']; ?>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card" style="margin-top: 30px;">
        <h3><?= $t['registered_users']; ?></h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th><?= $t['id']; ?></th>
                    <th><?= $t['name']; ?></th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th><?= $t['registration']; ?></th>
                    <th><?= $t['actions']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td><?= htmlspecialchars($user['artist_name']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= $user['is_admin'] ? '✓' : '-'; ?></td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                <a href="/admin/delete?id=<?= $user['id']; ?>" 
                                   class="btn-small" 
                                   onclick="return confirm('<?= $t['delete_user']; ?> <?= htmlspecialchars($user['artist_name']); ?>?')">
                                    <i class="fas fa-trash"></i> <?= $t['delete']; ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-container { max-width: 1200px; margin: 0 auto; }
.admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
.admin-table th { color: var(--primary-text-color); font-weight: 500; background: rgba(255, 255, 255, 0.05); }
.admin-table td { color: var(--secondary-text-color); }
.admin-table tr:hover { background: rgba(255, 255, 255, 0.03); }
</style>

<script>
function approveTopic(topicId) {
    if (!confirm('<?= $t['approve_topic']; ?>')) return;
    
    fetch('/forum/approve?id=' + topicId)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById('topic-' + topicId);
                row.querySelector('td:nth-child(5)').innerHTML = '<span style="color: #28a745;">✓ Aprovado</span>';
                row.querySelector('td:nth-child(6) button').remove();
            }
        });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
