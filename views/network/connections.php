<?php require __DIR__ . '/../layouts/header.php'; ?>
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <a href="/profile/view?id=<?= $_GET['id'] ?? $_SESSION['user_id']; ?>" style="color: var(--secondary-text-color); text-decoration: none; display: inline-block; margin-bottom: 15px;">
            <i class="fas fa-arrow-left"></i> <?= $t['back']; ?>
        </a>
        <h2 style="color: var(--primary-text-color); margin-bottom: 20px;">
            <?php
            if ($type === 'connections') echo $t['connections'];
            elseif ($type === 'followers') echo $t['followers'];
            else echo $t['following'];
            ?> - <?= htmlspecialchars($userName); ?>
        </h2>
        <?php if (empty($users)): ?>
            <p style="text-align: center; color: var(--secondary-text-color); padding: 40px;">
                <?php
                if ($type === 'connections') echo 'Nenhuma conexão ainda';
                elseif ($type === 'followers') echo 'Nenhum seguidor ainda';
                else echo 'Não está seguindo ninguém';
                ?>
            </p>
        <?php else: ?>
            <div style="display: grid; gap: 15px;">
                <?php foreach ($users as $user): ?>
                    <?php
                    $avatarSrc = '/uploads/profile/default.png';
                    $base = __DIR__ . '/../../public/uploads/profile';
                    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                        $p = "{$base}/user_{$user['id']}.{$ext}";
                        if (file_exists($p)) {
                            $avatarSrc = "/uploads/profile/user_{$user['id']}.{$ext}?t=" . time();
                            break;
                        }
                    }
                    ?>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #c0c0c0, #808080); display: flex; align-items: center; justify-content: center; font-weight: 600; color: #000; overflow: hidden;">
                            <?php if ($avatarSrc !== '/uploads/profile/default.png'): ?>
                                <img src="<?= htmlspecialchars($avatarSrc); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?= strtoupper(substr($user['artist_name'], 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1;">
                            <div style="color: var(--primary-text-color); font-weight: 500;"><?= htmlspecialchars($user['artist_name']); ?></div>
                            <div style="color: var(--secondary-text-color); font-size: 0.85rem;"><?= htmlspecialchars($user['email']); ?></div>
                        </div>
                        <a href="/profile/view?id=<?= $user['id']; ?>" class="btn-small"><?= $t['view_profile']; ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
