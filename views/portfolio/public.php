<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($project->title) ?> - Press Kit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/presskit.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        window.presskitSlug = '<?= htmlspecialchars($project->slug) ?>';
        window.artistName = '<?= htmlspecialchars($_SESSION['artist_name'] ?? 'Artista') ?>';
        window.mediaGallery = <?= json_encode(array_values(array_map(fn($m) => ['path' => $m->filePath, 'type' => $m->fileType], $project->media))) ?>;
    </script>
    <script src="/js/presskit.js"></script>
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light' ? 'light-theme' : '' ?>">
    <div class="presskit">
        <div class="hero">
            <div class="hero-badge">Press Kit</div>
            <h1><?= htmlspecialchars($project->title) ?></h1>
            <?php if ($project->description): ?>
                <p class="hero-subtitle"><?= nl2br(htmlspecialchars($project->description)) ?></p>
            <?php endif; ?>
            <div class="hero-meta">
                <span><i class="fas fa-calendar"></i> <?= date('Y', strtotime($project->createdAt)) ?></span>
                <span><i class="fas fa-images"></i> <?= count($project->media) ?> <?= $t['files'] ?></span>
            </div>
        </div>
        <?php if ($project->description): ?>
        <div class="section">
            <h2 class="section-title"><?= $t['about_project'] ?></h2>
            <p class="about-text"><?= nl2br(htmlspecialchars($project->description)) ?></p>
        </div>
        <?php endif; ?>
        <div class="section">
            <h2 class="section-title"><?= $t['media_gallery'] ?></h2>
            <div class="gallery">
                <?php foreach ($project->media as $media): ?>
                    <div class="gallery-item">
                        <?php if ($media->fileType === 'image'): ?>
                            <img src="<?= htmlspecialchars($media->filePath) ?>" alt="<?= htmlspecialchars($project->title) ?>">
                            <div class="media-overlay">
                                <button onclick="openImageModal('<?= htmlspecialchars($media->filePath) ?>')" class="media-btn" title="Visualizar"><i class="fas fa-eye"></i></button>
                                <a href="<?= htmlspecialchars($media->filePath) ?>" download class="media-btn" title="Download"><i class="fas fa-download"></i></a>
                            </div>
                        <?php elseif ($media->fileType === 'video'): ?>
                            <video src="<?= htmlspecialchars($media->filePath) ?>" controls></video>
                            <div class="media-overlay">
                                <button onclick="openImageModal('<?= htmlspecialchars($media->filePath) ?>')" class="media-btn" title="Visualizar"><i class="fas fa-eye"></i></button>
                                <a href="<?= htmlspecialchars($media->filePath) ?>" download class="media-btn" title="Download"><i class="fas fa-download"></i></a>
                            </div>
                        <?php else: ?>
                            <audio src="<?= htmlspecialchars($media->filePath) ?>" controls></audio>
                            <div class="media-overlay">
                                <button onclick="openImageModal('<?= htmlspecialchars($media->filePath) ?>')" class="media-btn" title="Ouvir"><i class="fas fa-play"></i></button>
                                <a href="<?= htmlspecialchars($media->filePath) ?>" download class="media-btn" title="Download"><i class="fas fa-download"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="section">
            <h2 class="section-title"><?= $t['statistics'] ?></h2>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?= count(array_filter($project->media, fn($m) => $m->fileType === 'image')) ?></div>
                    <div class="stat-label"><?= $t['images'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count(array_filter($project->media, fn($m) => $m->fileType === 'video')) ?></div>
                    <div class="stat-label"><?= $t['videos'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count(array_filter($project->media, fn($m) => $m->fileType === 'audio')) ?></div>
                    <div class="stat-label"><?= $t['audios'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($project->media) ?></div>
                    <div class="stat-label"><?= $t['total'] ?></div>
                </div>
            </div>
        </div>
        <div class="section">
            <h2 class="section-title"><?= $t['contact'] ?></h2>
            <div class="contact-grid">
                <div class="contact-item">
                    <i class="fas fa-user"></i>
                    <div class="contact-label"><?= $t['artists'] ?></div>
                    <div class="contact-value" id="artists-display"><?= htmlspecialchars($_SESSION['artist_name'] ?? 'Artista') ?></div>
                    <button onclick="addArtist()" class="btn-action"><i class="fas fa-user-plus"></i> <?= $t['add_artist'] ?></button>
                </div>
                <div class="contact-item">
                    <i class="fas fa-link"></i>
                    <div class="contact-label"><?= $t['presskit_link'] ?></div>
                    <div class="contact-value" style="font-size: 0.9rem;"><?= htmlspecialchars($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?></div>
                    <button onclick="copyLink()" class="btn-action" id="copyBtn">
                        <i class="fas fa-copy"></i> <?= $t['copy_link_btn'] ?>
                    </button>
                </div>
                <div class="contact-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="contact-label"><?= $t['created_on'] ?></div>
                    <div class="contact-value"><?= date('d/m/Y', strtotime($project->createdAt)) ?></div>
                    <?php if ($project->updatedAt && $project->updatedAt !== $project->createdAt): ?>
                    <div class="contact-label" style="margin-top: 15px;"><?= $t['modified_on'] ?></div>
                    <div class="contact-value"><?= date('d/m/Y', strtotime($project->updatedAt)) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="share-actions">
            <a href="https://www.instagram.com/" target="_blank" class="btn-share btn-instagram">
                <i class="fab fa-instagram"></i> <?= $t['share_instagram'] ?>
            </a>
            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($t['share_message'] . $project->title) ?>&url=<?= urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="btn-share btn-twitter">
                <i class="fab fa-x-twitter"></i> <?= $t['share_x'] ?>
            </a>
        </div>
        <div class="export-actions">
            <button onclick="exportPDF()" class="download-all-btn btn-pdf"><i class="fas fa-file-pdf"></i> <?= $t['export_pdf'] ?></button>
        </div>
        <div class="footer">
            <p><?= $t['presskit_footer'] ?></p>
            <p style="margin-top: 10px; font-size: 0.85rem;">© <?= date('Y') ?> <?= $t['all_rights'] ?></p>
        </div>
    </div>
    <div class="watermark-logo" style="position: fixed !important; top: 20px; left: 20px; z-index: 9999;">
        <img src="/images/artsync.png" alt="ArtSync" style="width: 120px; height: 120px; object-fit: contain;">
    </div>
    <div id="imageModal" class="image-modal" onclick="event.target === this && closeImageModal()">
        <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
        <button id="prevBtn" class="image-modal-nav image-modal-prev" onclick="event.stopPropagation(); navigateImage(-1)">&lt;</button>
        <div id="modalMediaContainer"></div>
        <button id="nextBtn" class="image-modal-nav image-modal-next" onclick="event.stopPropagation(); navigateImage(1)">&gt;</button>
    </div>
</body>
</html>
