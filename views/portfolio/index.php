<?php
$currentPage = 'portfolio';
require __DIR__ . '/../layouts/header.php';
$pageTitle = $t['my_portfolio'];
?>

<div class="portfolio-container">
    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="margin: 0; color: var(--secondary-text-color);"><?= $t['organize_share']; ?></p>
            <div style="display: flex; gap: 15px;">
                <button class="btn-help" onclick="openModal('helpModal')" title="<?= $t['how_to_use']; ?>">
                    <i class="fas fa-question-circle"></i>
                </button>
                <button class="btn" onclick="openModal('createModal')">
                    <i class="fas fa-plus"></i><span>  <?= $t['new_project']; ?></span>
                </button>
            </div>
        </div>
    </div>

    <?php if (isset($feedback)): ?>
        <div class="<?= $feedback['type'] ?>-message"><?= htmlspecialchars($feedback['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

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
                            <i class="fas fa-eye"></i> <?= $t['presskit']; ?>
                        </a>
                        <a href="<?= '/portfolio/view?slug=' . urlencode($project->slug) ?>" class="btn-small"
                            onclick="navigator.clipboard.writeText(window.location.origin + this.href); alert('<?= $t['link_copied']; ?>'); return false;">
                            <i class="fas fa-link"></i> <?= $t['copy_link']; ?>
                        </a>
                        <a href="/portfolio/delete?id=<?= $project->id ?>" class="btn-small btn-danger"
                            onclick="return confirm('<?= $t['delete_project']; ?>')">
                            <i class="fas fa-trash"></i> <?= $t['delete']; ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Criar Projeto -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('createModal')">&times;</span>
        <h2><?= $t['new_project']; ?></h2>
        <form action="/portfolio/create" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label for="title"><?= $t['project_title']; ?> *</label>
                <input type="text" id="title" name="title" required maxlength="255">
            </div>
            <div class="input-group">
                <label for="description"><?= $t['description']; ?></label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            <div class="input-group">
                <label for="media_files"><?= $t['medias']; ?> *</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="media_files" class="btn-small" style="margin: 0; cursor: pointer;"><?= $t['choose_files']; ?></label>
                    <span id="fileLabel" style="color: var(--secondary-text-color); font-size: 0.9rem;"><?= $t['no_file_chosen']; ?></span>
                </div>
                <input type="file" id="media_files" name="media_files[]" multiple accept="image/*,audio/*,video/mp4"
                    required style="display: none;" onchange="updateFileLabel(this)">
                <small><?= $t['file_formats']; ?></small>
            </div>
            <button type="submit" class="btn"><?= $t['create_project']; ?></button>
        </form>
    </div>
</div>

<!-- Modal Ajuda -->
<div id="helpModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('helpModal')">&times;</span>
        <h2><i class="fas fa-question-circle"></i> <?= $t['how_to_portfolio']; ?></h2>

        <div class="help-section">
            <h3><?= $t['create_project_step']; ?></h3>
            <p><?= $t['create_project_desc']; ?></p>
            <ul>
                <li><strong><?= $t['title_field']; ?></strong> <?= $t['title_example']; ?></li>
                <li><strong><?= $t['description_field']; ?></strong> <?= $t['description_example']; ?></li>
                <li><strong><?= $t['medias_field']; ?></strong> <?= $t['medias_example']; ?></li>
            </ul>
        </div>

        <div class="help-section">
            <h3><?= $t['share_portfolio_step']; ?></h3>
            <p><?= $t['share_portfolio_desc']; ?></p>
            <ul>
                <li><?= $t['copy_send_clients']; ?></li>
                <li><?= $t['share_social']; ?></li>
                <li><?= $t['add_resume']; ?></li>
            </ul>
        </div>

        <div class="help-section">
            <h3><?= $t['download_media_step']; ?></h3>
            <p><?= $t['download_media_desc']; ?></p>
        </div>

        <div class="help-example">
            <h3><i class="fas fa-lightbulb"></i> <?= $t['practical_example']; ?></h3>
            <div class="example-card">
                <strong><?= $t['project_label']; ?></strong> <?= $t['project_example']; ?><br>
                <strong><?= $t['description_label']; ?></strong> <?= $t['description_example_text']; ?><br>
                <strong><?= $t['medias_label']; ?></strong> <?= $t['medias_example_text']; ?><br>
                <strong><?= $t['result_label']; ?></strong> <?= $t['result_example']; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .portfolio-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0;
    }

    .portfolio-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .portfolio-header h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-text-color);
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .header-actions .btn {
        font-family: 'Poppins', sans-serif;
        font-weight: 300;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.9em;
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: var(--primary-text-color);
        transition: all 0.3s ease;
    }

    .header-actions .btn:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .header-actions .btn i {
        font-size: 1em;
        margin-right: 2px;
    }

    .header-actions .btn span {
        font-weight: 300;
    }

    .btn-help {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: var(--primary-text-color);
        width: 45px;
        height: 45px;
        border-radius: 0;
        cursor: pointer;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }

    .btn-help:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .help-section {
        margin: 25px 0;
        padding: 20px;
        background: rgba(255, 255, 255, 0.03);
        border-left: 3px solid var(--glow-color);
        border-radius: 8px;
    }

    .help-section h3 {
        color: var(--glow-color);
        margin-bottom: 10px;
    }

    .help-section ul {
        margin-left: 20px;
        color: var(--secondary-text-color);
    }

    .help-section li {
        margin: 8px 0;
    }

    .help-example {
        margin-top: 30px;
        padding: 20px;
        background: rgba(0, 255, 200, 0.05);
        border: 1px solid rgba(0, 255, 200, 0.2);
        border-radius: 12px;
    }

    .example-card {
        margin-top: 15px;
        padding: 15px;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 8px;
        line-height: 1.8;
        color: var(--secondary-text-color);
    }

    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }

    .project-card {
        background: rgba(20, 20, 25, 0.6);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .project-card:hover {
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.5);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-5px);
    }

    .project-media-preview {
        position: relative;
        height: 200px;
        background: rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .project-media-preview img,
    .project-media-preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .project-media-preview audio {
        width: 90%;
    }

    .media-count {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .no-media {
        font-size: 3rem;
        color: var(--secondary-text-color);
    }

    .project-info {
        padding: 20px;
    }

    .project-info h3 {
        color: var(--primary-text-color);
        margin-bottom: 10px;
    }

    .project-info p {
        color: var(--secondary-text-color);
        margin-bottom: 15px;
    }

    .project-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
        overflow-y: auto;
    }

    .modal[style*="display: block"] {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        padding: 30px;
        border: 1px solid var(--border-color);
        border-radius: 0;
        width: 90%;
        max-width: 600px;
        max-height: 85vh;
        overflow-y: auto;
        position: relative;
        margin: auto;
        box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5);
    }

    .modal-content::-webkit-scrollbar {
        width: 10px;
    }

    .modal-content::-webkit-scrollbar-track {
        background: rgba(10, 10, 15, 0.8);
        border-radius: 10px;
    }

    .modal-content::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.15));
        border-radius: 10px;
        border: 2px solid rgba(10, 10, 15, 0.8);
    }

    .modal-content::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.3));
    }

    .close {
        color: var(--secondary-text-color);
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: var(--primary-text-color);
    }
</style>

<script>
    function updateFileLabel(input) {
        const label = document.getElementById('fileLabel');
        if (input.files.length > 0) {
            label.textContent = input.files.length + ' <?= $t['files_selected']; ?>';
        } else {
            label.textContent = '<?= $t['no_file_chosen']; ?>';
        }
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    window.onclick = function (event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>