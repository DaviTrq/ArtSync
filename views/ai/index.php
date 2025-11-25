<?php require __DIR__ . '/../layouts/header.php'; ?>

<header class="main-header">
    <p><?= $t['ai_mentor_desc']; ?></p>
</header>

<section class="card gemini-style-card">

    <div class="conversation-history">
        <?php if (isset($user_question) && !empty($user_question)): ?>
            <div class="chat-bubble user-bubble">
                <div class="avatar user-avatar">
                    <?php 
                    // Lógica das iniciais
                    $name_parts = explode(' ', $_SESSION['artist_name'] ?? 'Eu');
                    echo htmlspecialchars(strtoupper(substr($name_parts[0], 0, 1))); 
                    ?>
                </div>
                <p><?php echo htmlspecialchars($user_question); ?></p>
            </div>

            <div class="chat-bubble ai-bubble">
                <div class="avatar ai-avatar"><i class="fas fa-sparkles"></i></div>
                <?php if (empty($ai_response)): ?>
                    <div class="spinner-container">
                        <div class="spinner"></div>
                    </div>
                <?php else: ?>
                    <p><?php echo $ai_response; ?></p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="chat-bubble ai-bubble">
                <div class="avatar ai-avatar"><i class="fas fa-robot"></i></div>
                <p><?= $t['ai_greeting']; ?></p>
            </div>
        <?php endif; ?>
    </div>

    <form action="/ai/ask" method="post" id="ai-form" enctype="multipart/form-data" class="user-bubble-form">
        <input type="file" id="ai-file-input" name="media_file" accept="image/*,audio/*" style="display: none;">
        
        <button type="button" id="upload-btn" title="<?= $t['attach_image_audio']; ?>">
            <i class="fas fa-paperclip"></i>
        </button>

        <div class="textarea-wrapper">
            <textarea name="user_question" id="ai-textarea" placeholder="<?= $t['type_question']; ?>" rows="1" required></textarea>
            <span id="file-indicator" style="display:none;"></span>
        </div>
        
        <button type="submit" title="<?= $t['send']; ?>">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>

</section>

<script>
const textarea = document.getElementById('ai-textarea');
const uploadBtn = document.getElementById('upload-btn');
const fileInput = document.getElementById('ai-file-input');
const fileIndicator = document.getElementById('file-indicator');

textarea.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

uploadBtn.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        const file = this.files[0];
        const icon = file.type.startsWith('image/') ? '🖼️' : '🎵';
        fileIndicator.textContent = `${icon} ${file.name}`;
        fileIndicator.style.display = 'block';
        uploadBtn.style.color = '#4CAF50';
    } else {
        fileIndicator.style.display = 'none';
        uploadBtn.style.color = '';
    }
});

const conversationHistory = document.querySelector('.conversation-history');
if (conversationHistory) {
    conversationHistory.scrollTop = conversationHistory.scrollHeight;
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>