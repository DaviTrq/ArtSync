<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="messages-container">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="<?= $_SESSION['flash_message']['type'] === 'error' ? 'error-message' : 'success-message'; ?>" style="margin-bottom: 20px;">
            <?= htmlspecialchars($_SESSION['flash_message']['message']); ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>
    <div class="card">
        <h3><?= $t['conversations']; ?></h3>
        <?php if (empty($conversations)): ?>
            <p style="color: var(--secondary-text-color); text-align: center; padding: 40px;">
                <?= $t['no_conversations']; ?>
            </p>
        <?php else: ?>
            <div class="conversations-list">
                <?php foreach ($conversations as $conv): ?>
                    <a href="/messages/chat?id=<?= $conv['contact_id']; ?>" class="conversation-item <?= $conv['unread_count'] > 0 ? 'unread' : ''; ?>">
                        <div class="conv-avatar">
                            <?= strtoupper(substr($conv['artist_name'], 0, 1)); ?>
                        </div>
                        <div class="conv-info">
                            <div class="conv-header">
                                <strong><?= htmlspecialchars($conv['artist_name']); ?></strong>
                                <small><?= date('d/m H:i', strtotime($conv['last_time'])); ?></small>
                            </div>
                            <div class="conv-preview">
                                <?= htmlspecialchars(substr($conv['last_message'], 0, 50)); ?><?= strlen($conv['last_message']) > 50 ? '...' : ''; ?>
                            </div>
                        </div>
                        <?php if ($conv['unread_count'] > 0): ?>
                            <span class="unread-badge"><?= $conv['unread_count']; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<style>
.messages-container { max-width: 800px; margin: 0 auto; }
.conversations-list { display: flex; flex-direction: column; gap: 10px; }
.conversation-item {
    display: flex; align-items: center; gap: 15px; padding: 15px;
    background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);
    text-decoration: none; color: var(--primary-text-color);
    transition: 0.2s; position: relative;
}
.conversation-item:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.2); }
.conversation-item.unread { background: rgba(255,255,255,0.08); }
.conv-avatar {
    width: 50px; height: 50px; border-radius: 50%;
    background: linear-gradient(135deg, #c0c0c0, #808080);
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; color: #000; flex-shrink: 0;
}
.conv-info { flex: 1; min-width: 0; }
.conv-header { display: flex; justify-content: space-between; margin-bottom: 5px; }
.conv-header strong { color: var(--primary-text-color); }
.conv-header small { color: var(--secondary-text-color); font-size: 0.8rem; }
.conv-preview { color: var(--secondary-text-color); font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.unread-badge {
    background: #4CAF50; color: white; border-radius: 50%;
    width: 24px; height: 24px; display: flex; align-items: center;
    justify-content: center; font-size: 0.75rem; font-weight: 600;
}
</style>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
