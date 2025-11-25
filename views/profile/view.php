<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="profile-view-container">
    <div class="card" style="text-align: center; padding: 40px;">
        <?php
        $avatarSrc = '/uploads/profile/default.png';
        $base = __DIR__ . '/../../public/uploads/profile';
        $web = '/uploads/profile';
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $p = "{$base}/user_{$userId}.{$ext}";
            if (file_exists($p)) {
                $avatarSrc = "{$web}/user_{$userId}.{$ext}?t=" . time();
                break;
            }
        }
        ?>
        <div class="profile-avatar-large">
            <?php if ($avatarSrc !== '/uploads/profile/default.png'): ?>
                <img src="<?= htmlspecialchars($avatarSrc); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <?= strtoupper(substr($user['artist_name'], 0, 1)); ?>
            <?php endif; ?>
        </div>
        <h2 style="color: var(--primary-text-color); margin: 20px 0 10px;"><?= htmlspecialchars($user['artist_name']); ?></h2>
        <p style="color: var(--secondary-text-color); margin-bottom: 20px;"><?= htmlspecialchars($user['email']); ?></p>
        
        <?php if (!empty($user['bio'])): ?>
            <p style="color: var(--secondary-text-color); max-width: 600px; margin: 0 auto 30px; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($user['bio'])); ?>
            </p>
        <?php else: ?>
            <p style="color: var(--secondary-text-color); font-style: italic; margin-bottom: 30px;"><?= $t['no_bio']; ?></p>
        <?php endif; ?>
        
        <div style="display: flex; justify-content: center; gap: 40px; margin: 30px 0; padding: 20px; background: rgba(255,255,255,0.05);">
            <div>
                <div style="font-size: 2rem; font-weight: 600; color: var(--primary-text-color);"><?= $connections; ?></div>
                <div style="color: var(--secondary-text-color); font-size: 0.85rem;"><?= $t['connections']; ?></div>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 600; color: var(--primary-text-color);"><?= $followers; ?></div>
                <div style="color: var(--secondary-text-color); font-size: 0.85rem;"><?= $t['followers']; ?></div>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 600; color: var(--primary-text-color);"><?= $following; ?></div>
                <div style="color: var(--secondary-text-color); font-size: 0.85rem;"><?= $t['following']; ?></div>
            </div>
        </div>
        
        <small style="color: var(--secondary-text-color);"><?= $t['member_since']; ?> <?= date('d/m/Y', strtotime($user['created_at'])); ?></small>
    </div>

    <?php if (!empty($topics)): ?>
        <div class="card" style="margin-top: 20px;">
            <h3 style="color: var(--primary-text-color); margin-bottom: 15px;"><?= $t['topics_created']; ?></h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($topics as $topic): ?>
                    <a href="/forum/view?id=<?= $topic['id']; ?>" style="display: flex; justify-content: space-between; padding: 12px; background: rgba(255,255,255,0.05); text-decoration: none; color: var(--primary-text-color); border: 1px solid rgba(255,255,255,0.1);">
                        <span><?= htmlspecialchars($topic['title']); ?></span>
                        <small style="color: var(--secondary-text-color);"><?= date('d/m/Y', strtotime($topic['created_at'])); ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($comments)): ?>
        <div class="card" style="margin-top: 20px;">
            <h3 style="color: var(--primary-text-color); margin-bottom: 15px;"><?= $t['latest_comments']; ?></h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($comments as $comment): ?>
                    <a href="/forum/view?id=<?= $comment['topic_id']; ?>" style="padding: 12px; background: rgba(255,255,255,0.05); text-decoration: none; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="color: var(--secondary-text-color); font-size: 0.85rem; margin-bottom: 5px;"><?= $t['in_topic']; ?> "<?= htmlspecialchars($comment['topic_title']); ?>"</div>
                        <div style="color: var(--primary-text-color);"><?= htmlspecialchars(substr($comment['content'], 0, 100)); ?><?= strlen($comment['content']) > 100 ? '...' : ''; ?></div>
                        <small style="color: var(--secondary-text-color); margin-top: 5px; display: block;"><?= date('d/m/Y H:i', strtotime($comment['created_at'])); ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.profile-view-container { max-width: 800px; margin: 0 auto; }
.profile-avatar-large { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #c0c0c0, #808080); display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 600; color: #000; margin: 0 auto; }
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
