<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="forum-view-container">
    <a href="/forum" class="back-link"><i class="fas fa-arrow-left"></i> <?= $t['back']; ?></a>
    
    <div class="reddit-post">
        <div class="post-votes">
            <i class="fas fa-arrow-up"></i>
            <span>0</span>
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="post-content">
            <div class="post-meta">
                <span><?= $t['posted_by']; ?> <strong><?= htmlspecialchars($topico->authorName); ?></strong></span>
                <span>•</span>
                <span><?= $topico->created_at ? date('d/m/Y H:i', strtotime($topico->created_at)) : ''; ?></span>
            </div>
            <h2><?= htmlspecialchars($topico->title); ?></h2>
            <div class="post-body">
                <?= nl2br(htmlspecialchars($topico->content)); ?>
            </div>
            <?php if (!empty($topico->attachments)): ?>
                <div class="post-attachments">
                    <?php foreach ($topico->attachments as $att): ?>
                        <?php 
                        $ext = strtolower(pathinfo($att['file_path'], PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isAudio = in_array($ext, ['mp3', 'wav']);
                        $dawIcons = [
                            'flp' => 'FL Studio',
                            'als' => 'Ableton Live',
                            'ptx' => 'Pro Tools',
                            'logic' => 'Logic Pro',
                            'rpp' => 'Reaper'
                        ];
                        $dawLabel = $dawIcons[$ext] ?? $t['daw_project'];
                        ?>
                        <?php if ($isImage): ?>
                            <img src="<?= htmlspecialchars($att['file_path']); ?>" alt="<?= $t['attachment']; ?>" class="post-image">
                        <?php elseif ($isAudio): ?>
                            <audio src="<?= htmlspecialchars($att['file_path']); ?>" controls class="post-audio"></audio>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($att['file_path']); ?>" download class="daw-file">
                                <div class="daw-icon">
                                    <i class="fas fa-file-audio"></i>
                                </div>
                                <div class="daw-info">
                                    <strong><?= $dawLabel; ?></strong>
                                    <span><?= basename($att['file_path']); ?></span>
                                </div>
                                <i class="fas fa-download"></i>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="comments-section">
        <div class="comment-form-card">
            <textarea id="mainComment" rows="3" placeholder="<?= $t['what_do_you_think']; ?>"></textarea>
            <div id="fileList" style="margin-top: 8px; display: none;"></div>
            <div class="comment-actions">
                <input type="file" id="mainAttachment" accept=".mp3,.wav,.flp,.als,.ptx,.logic,.rpp" multiple style="display:none" onchange="showFiles()">
                <button class="btn-small" onclick="document.getElementById('mainAttachment').click()">
                    <i class="fas fa-paperclip"></i> <?= $t['attach']; ?>
                </button>
                <button class="btn" onclick="submitComment(<?= $topico->id ?>)">
                    <?= $t['comment']; ?>
                </button>
            </div>
            <small style="color: var(--secondary-text-color); margin-top: 8px; display: block;">
                <?= $t['allowed_files']; ?>
            </small>
        </div>

        <div class="comments-list">
            <?php foreach ($comments as $comment): ?>
                <div class="reddit-comment" data-id="<?= $comment->id ?>">
                    <div class="comment-line"></div>
                    <div class="comment-body">
                        <div class="comment-meta">
                            <strong><?= htmlspecialchars($comment->authorName); ?></strong>
                            <span>•</span>
                            <span><?= $comment->created_at ? date('d/m/Y H:i', strtotime($comment->created_at)) : ''; ?></span>
                        </div>
                        <div class="comment-text">
                            <?= nl2br(htmlspecialchars($comment->content)); ?>
                        </div>
                        <?php if (!empty($comment->attachments)): ?>
                            <div class="comment-attachments">
                                <?php foreach ($comment->attachments as $att): ?>
                                    <?php if ($att['file_type'] === 'audio'): ?>
                                        <audio src="<?= htmlspecialchars($att['file_path']); ?>" controls></audio>
                                    <?php else: ?>
                                        <a href="<?= htmlspecialchars($att['file_path']); ?>" target="_blank" class="attachment-link">
                                            <i class="fas fa-file"></i> <?= basename($att['file_path']); ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.forum-view-container { max-width: 900px; margin: 0 auto; }
.back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--secondary-text-color); text-decoration: none; margin-bottom: 20px; }
.back-link:hover { color: var(--primary-text-color); }
.reddit-post { display: flex; gap: 15px; background: rgba(20, 20, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); padding: 20px; margin-bottom: 20px; }
.post-votes { display: flex; flex-direction: column; align-items: center; gap: 5px; color: var(--secondary-text-color); min-width: 40px; }
.post-votes i { cursor: pointer; font-size: 1.3rem; }
.post-votes span { font-weight: 600; }
.post-content { flex: 1; }
.post-meta { display: flex; gap: 8px; align-items: center; color: var(--secondary-text-color); font-size: 0.85rem; margin-bottom: 12px; }
.post-content h2 { color: var(--primary-text-color); margin: 0 0 15px 0; font-size: 1.5rem; font-weight: 500; }
.post-body { color: var(--secondary-text-color); line-height: 1.6; margin-bottom: 15px; }
.post-attachments { display: flex; flex-direction: column; gap: 12px; margin-top: 15px; }
.post-image { max-width: 100%; height: auto; border: 1px solid rgba(255, 255, 255, 0.1); }
.post-audio { width: 100%; }
.daw-file { display: flex; align-items: center; gap: 15px; padding: 15px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); text-decoration: none; color: var(--primary-text-color); }
.daw-file:hover { background: rgba(255, 255, 255, 0.1); }
.daw-icon { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.1); font-size: 1.5rem; color: var(--primary-text-color); }
.daw-info { flex: 1; display: flex; flex-direction: column; gap: 4px; }
.daw-info strong { color: var(--primary-text-color); font-size: 1rem; }
.daw-info span { color: var(--secondary-text-color); font-size: 0.85rem; }
.comments-section { margin-top: 20px; }
.comment-form-card { background: rgba(20, 20, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); padding: 15px; margin-bottom: 20px; }
.comment-form-card textarea { width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--primary-text-color); padding: 12px; font-family: 'Poppins', sans-serif; resize: vertical; }
.comment-actions, .reply-actions { display: flex; gap: 10px; margin-top: 10px; justify-content: flex-end; }
.comments-list { display: flex; flex-direction: column; gap: 15px; }
.reddit-comment { display: flex; gap: 10px; }
.comment-line { width: 2px; background: rgba(255, 255, 255, 0.1); flex-shrink: 0; cursor: pointer; }
.comment-line:hover { background: rgba(255, 255, 255, 0.3); }
.comment-body { flex: 1; }
.comment-meta { display: flex; gap: 8px; align-items: center; color: var(--secondary-text-color); font-size: 0.85rem; margin-bottom: 8px; }
.comment-meta strong { color: var(--primary-text-color); }
.comment-text { color: var(--secondary-text-color); line-height: 1.5; margin-bottom: 10px; }
.comment-attachments { display: flex; flex-direction: column; gap: 8px; margin: 10px 0; }
.attachment-link { display: inline-flex; align-items: center; gap: 8px; color: var(--secondary-text-color); text-decoration: none; padding: 8px 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); }
.attachment-link:hover { background: rgba(255, 255, 255, 0.1); }
.action-btn { background: transparent; border: none; color: var(--secondary-text-color); cursor: pointer; font-size: 0.85rem; padding: 4px 8px; }
.action-btn:hover { color: var(--primary-text-color); }
.reply-form { margin-top: 10px; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255, 255, 255, 0.05); }
.reply-form textarea { width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--primary-text-color); padding: 10px; font-family: 'Poppins', sans-serif; }
</style>

<script>
function showFiles() {
    const fileInput = document.getElementById('mainAttachment');
    const fileList = document.getElementById('fileList');
    
    if (fileInput.files.length > 0) {
        fileList.style.display = 'block';
        fileList.innerHTML = '<small style="color: var(--secondary-text-color);"><i class="fas fa-paperclip"></i> ' + 
            fileInput.files.length + ' <?= $t['files_attached']; ?>: ' + 
            Array.from(fileInput.files).map(f => f.name).join(', ') + '</small>';
    } else {
        fileList.style.display = 'none';
    }
}

function submitComment(topicId) {
    const textarea = document.getElementById('mainComment');
    const fileInput = document.getElementById('mainAttachment');
    
    const formData = new FormData();
    formData.append('topic_id', topicId);
    formData.append('content', textarea.value);
    if (fileInput.files.length > 0) {
        for (let file of fileInput.files) {
            formData.append('attachments[]', file);
        }
    }
    
    fetch('/forum/comment', {
        method: 'POST',
        body: formData
    }).then(() => location.reload());
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
