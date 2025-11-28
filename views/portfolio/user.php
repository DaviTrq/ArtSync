<?php
$currentPage = 'portfolio';
require __DIR__ . '/../layouts/header.php';
?>
<link rel="stylesheet" href="/css/portfolio-user.css">
<div class="portfolio-container">
    <div style="margin-bottom: 30px;">
        <h2 style="color: var(--primary-text-color); margin-bottom: 10px;">
            <i class="fas fa-briefcase"></i> <?= htmlspecialchars($user['artist_name']); ?>
        </h2>
        <p style="margin: 0; color: var(--secondary-text-color);"><?= $t['portfolio']; ?></p>
    </div>
    <?php if (empty($projects)): ?>
        <div style="text-align: center; padding: 50px; color: var(--secondary-text-color);">
            <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
            <p>Este usuário ainda não possui projetos no portfólio.</p>
        </div>
    <?php else: ?>
        <div class="projects-grid">
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <div class="project-media-preview">
                        <?php if (!empty($project->media)): ?>
                            <?php $firstMedia = $project->media[0]; ?>
                            <?php if ($firstMedia->fileType === 'image'): ?>
                                <img src="<?= htmlspecialchars($firstMedia->filePath) ?>"
                                    alt="<?= htmlspecialchars($project->title) ?>">
                            <?php elseif ($firstMedia->fileType === 'video'): ?>
                                <video src="<?= htmlspecialchars($firstMedia->filePath) ?>" controls></video>
                            <?php else: ?>
                                <audio src="<?= htmlspecialchars($firstMedia->filePath) ?>" controls></audio>
                            <?php endif; ?>
                            <span class="media-count"><?= count($project->media) ?> <?= $t['media_count']; ?></span>
                        <?php else: ?>
                            <div class="no-media"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="project-info">
                        <h3><?= htmlspecialchars($project->title) ?></h3>
                        <p><?= htmlspecialchars(substr($project->description ?? '', 0, 100)) ?></p>
                        <div class="project-actions">
                            <a href="/portfolio/view?slug=<?= urlencode($project->slug) ?>" target="_blank" class="btn-small">
                                <i class="fas fa-eye"></i> <?= $t['view']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
