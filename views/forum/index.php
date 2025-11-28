<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="forum-header">
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="<?= $t['search_topics']; ?>" onkeyup="filterTopics()">
    </div>
    <button class="btn" onclick="openModal('createTopicModal')">
        <i class="fas fa-plus"></i> <?= $t['create_topic']; ?>
    </button>
</div>
<div class="forum-container">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="<?= $_SESSION['flash_message']['type'] ?>-message">
            <?= htmlspecialchars($_SESSION['flash_message']['message']); ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>
    <div class="topics-list" id="topicsList">
        <?php foreach ($topicos as $topic): ?>
            <a href="/forum/view?id=<?= $topic->id ?>" class="reddit-topic" data-title="<?= htmlspecialchars(strtolower($topic->title)); ?>">
                <div class="topic-votes">
                    <i class="fas fa-arrow-up"></i>
                    <span>0</span>
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="topic-content">
                    <h3><?= htmlspecialchars($topic->title); ?></h3>
                    <p><?= htmlspecialchars(substr($topic->content, 0, 200)); ?>...</p>
                    <div class="topic-meta">
                        <span><?= $t['by']; ?> <strong><?= htmlspecialchars($topic->authorName); ?></strong></span>
                        <span>•</span>
                        <span><?= $topic->created_at ? date('d/m/Y H:i', strtotime($topic->created_at)) : ''; ?></span>
                        <span>•</span>
                        <span><i class="fas fa-comment"></i> <?= $topic->commentCount ?? 0 ?> <?= $t['comments']; ?></span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<div id="createTopicModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('createTopicModal')">&times;</span>
        <h2><?= $t['new_topic']; ?></h2>
        <form action="/forum/create" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label><?= $t['title']; ?> *</label>
                <input type="text" name="title" required maxlength="255">
            </div>
            <div class="input-group">
                <label><?= $t['content']; ?> *</label>
                <textarea name="content" rows="6" required></textarea>
            </div>
            <div class="input-group">
                <label><i class="fas fa-paperclip"></i> <?= $t['attach_files']; ?></label>
                <input type="file" name="attachments[]" multiple accept="image/*,.mp3,.wav,.flp,.als,.ptx,.logic,.rpp">
                <small style="color: var(--secondary-text-color);"><?= $t['media_types']; ?></small>
            </div>
            <small style="color: var(--secondary-text-color); display: block; margin-bottom: 15px;"><?= $t['topic_review']; ?></small>
            <button type="submit" class="btn"><?= $t['create_topic']; ?></button>
        </form>
    </div>
</div>
<style>
.forum-header { display: flex; gap: 15px; align-items: center; margin-bottom: 25px; max-width: 900px; margin-left: auto; margin-right: auto; }
.search-bar { flex: 1; display: flex; align-items: center; gap: 10px; background: rgba(20, 20, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); padding: 12px 18px; }
.search-bar i { color: var(--secondary-text-color); }
.search-bar input { flex: 1; background: transparent; border: none; color: var(--primary-text-color); font-family: 'Poppins', sans-serif; outline: none; }
.forum-container { max-width: 900px; margin: 0 auto; }
.topics-list { display: flex; flex-direction: column; gap: 10px; }
.reddit-topic { display: flex; gap: 15px; background: rgba(20, 20, 25, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); padding: 15px; text-decoration: none; transition: 0.2s; }
.reddit-topic:hover { background: rgba(30, 30, 35, 0.7); border-color: rgba(255, 255, 255, 0.2); }
.topic-votes { display: flex; flex-direction: column; align-items: center; gap: 5px; color: var(--secondary-text-color); min-width: 40px; }
.topic-votes i { cursor: pointer; font-size: 1.2rem; }
.topic-votes i:hover { color: var(--primary-text-color); }
.topic-votes span { font-weight: 500; }
.topic-content { flex: 1; }
.topic-content h3 { color: var(--primary-text-color); margin: 0 0 8px 0; font-size: 1.1rem; font-weight: 500; }
.topic-content p { color: var(--secondary-text-color); margin: 0 0 10px 0; font-size: 0.9rem; }
.topic-meta { display: flex; gap: 8px; align-items: center; color: var(--secondary-text-color); font-size: 0.8rem; }
.topic-meta strong { color: var(--primary-text-color); }
.modal { position: fixed; z-index: 1000; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(5px); align-items: center; justify-content: center; }
.modal[style*="display: flex"] { display: flex !important; }
.modal-content { background: rgba(20, 20, 25, 0.95); backdrop-filter: blur(15px); padding: 30px; border: 1px solid rgba(255, 255, 255, 0.1); width: 90%; max-width: 600px; max-height: 85vh; overflow-y: auto; position: relative; }
.modal-content h2 { color: var(--primary-text-color); margin-bottom: 20px; }
.modal-content .close { position: absolute; top: 15px; right: 20px; color: var(--secondary-text-color); font-size: 28px; font-weight: bold; cursor: pointer; }
.modal-content .close:hover { color: var(--primary-text-color); }
</style>
<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.classList.add('modal-open');
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.classList.remove('modal-open');
}
function filterTopics() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const topics = document.querySelectorAll('.reddit-topic');
    topics.forEach(topic => {
        const title = topic.getAttribute('data-title');
        topic.style.display = title.includes(input) ? 'flex' : 'none';
    });
}
window.onclick = (e) => {
    if (e.target.id === 'createTopicModal') closeModal('createTopicModal');
};
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
