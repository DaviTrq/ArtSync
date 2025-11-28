<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="network-container">
    <div class="search-header">
        <h2><?= $t['find_people']; ?></h2>
        <form method="GET" action="/network" class="search-form">
            <input type="text" name="search" placeholder="<?= $t['search_artists']; ?>" value="<?= htmlspecialchars($search); ?>" required>
            <button type="submit" class="btn"><i class="fas fa-search"></i> <?= $t['search']; ?></button>
        </form>
    </div>
    <div class="users-grid">
        <?php if (empty($users) && $search): ?>
            <p style="color: var(--secondary-text-color); text-align: center;">Nenhum usuário encontrado.</p>
        <?php elseif (empty($search)): ?>
            <p style="color: var(--secondary-text-color); text-align: center;">Digite um nome para buscar artistas.</p>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <div class="user-card">
                    <div class="user-avatar-large">
                        <?= strtoupper(substr($user['artist_name'], 0, 1)); ?>
                    </div>
                    <h3><?= htmlspecialchars($user['artist_name']); ?></h3>
                    <p><?= htmlspecialchars($user['email']); ?></p>
                    <div style="display: flex; gap: 10px; justify-content: center; flex-direction: column; align-items: center;">
                        <a href="/profile/view?id=<?= $user['id']; ?>" class="btn-small">
                            <?= $t['view_profile']; ?>
                        </a>
                        <?php if ($user['connection_status'] === 'accepted'): ?>
                            <button class="btn-small" disabled style="opacity: 0.5;">✓ <?= $t['connected']; ?></button>
                        <?php elseif ($user['connection_status'] === 'pending'): ?>
                            <button class="btn-small" disabled style="opacity: 0.5;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <?= $t['pending_connection']; ?>
                            </button>
                        <?php elseif ($user['connection_status'] === 'following'): ?>
                            <button class="btn-small" disabled style="opacity: 0.5;"><?= $t['following']; ?></button>
                        <?php else: ?>
                            <button class="btn" onclick="connect(<?= $user['id']; ?>, this)">
                                <i class="fas fa-user-plus"></i> <?= $t['connect_btn']; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<style>
.network-container { max-width: 1200px; margin: 0 auto; }
.search-header { margin-bottom: 30px; }
.search-header h2 { color: var(--primary-text-color); margin-bottom: 15px; }
.search-form { display: flex; gap: 10px; }
.search-form input { flex: 1; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: var(--primary-text-color); font-family: 'Poppins', sans-serif; }
.users-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
.user-card { background: rgba(20, 20, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); padding: 25px; text-align: center; }
.user-avatar-large { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #c0c0c0, #808080); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; color: #000; margin: 0 auto 15px; }
.user-card h3 { color: var(--primary-text-color); margin: 10px 0 5px; font-size: 1.1rem; }
.user-card p { color: var(--secondary-text-color); font-size: 0.85rem; margin-bottom: 15px; }
</style>
<script>
function connect(userId, btn) {
    fetch('/network/connect', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'user_id=' + userId
    }).then(r => r.json()).then(data => {
        if (data.success) {
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.innerHTML = '⏳ Pendente';
        }
    });
}
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
