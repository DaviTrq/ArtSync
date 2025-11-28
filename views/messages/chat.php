<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="chat-container">
    <div class="card">
        <div class="chat-header">
            <a href="/messages" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <div class="chat-contact">
                <div class="contact-avatar">
                    <?= strtoupper(substr($contact['artist_name'], 0, 1)); ?>
                </div>
                <strong><?= htmlspecialchars($contact['artist_name']); ?></strong>
            </div>
        </div>
        <div class="chat-messages" id="chatMessages">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?= $msg['sender_id'] == $_SESSION['user_id'] ? 'sent' : 'received'; ?>">
                    <div class="message-bubble">
                        <?= nl2br(htmlspecialchars($msg['message'])); ?>
                        <small><?= date('H:i', strtotime($msg['created_at'])); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <form class="chat-input" id="chatForm">
            <input type="hidden" name="receiver_id" value="<?= $contact['id']; ?>">
            <input type="text" name="message" id="messageInput" placeholder="Digite sua mensagem..." required autocomplete="off">
            <button type="submit" class="btn"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>
<style>
.chat-container { max-width: 800px; margin: 0 auto; }
.chat-header {
    display: flex; align-items: center; gap: 15px; padding: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px;
}
.back-btn {
    color: var(--primary-text-color); font-size: 1.2rem;
    text-decoration: none; padding: 8px;
}
.chat-contact { display: flex; align-items: center; gap: 12px; }
.contact-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #c0c0c0, #808080);
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; color: #000;
}
.chat-messages {
    min-height: 400px; max-height: 500px; overflow-y: auto;
    padding: 20px; display: flex; flex-direction: column; gap: 12px;
}
.message { display: flex; }
.message.sent { justify-content: flex-end; }
.message.received { justify-content: flex-start; }
.message-bubble {
    max-width: 70%; padding: 12px 16px; border-radius: 18px;
    position: relative; word-wrap: break-word;
}
.message.sent .message-bubble {
    background: #4CAF50; color: white;
    border-bottom-right-radius: 4px;
}
.message.received .message-bubble {
    background: rgba(255,255,255,0.1); color: var(--primary-text-color);
    border-bottom-left-radius: 4px;
}
.message-bubble small {
    display: block; margin-top: 5px; font-size: 0.7rem;
    opacity: 0.8;
}
.chat-input {
    display: flex; gap: 10px; padding: 15px;
    border-top: 1px solid rgba(255,255,255,0.1);
}
.chat-input input {
    flex: 1; padding: 12px 18px; background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1); color: var(--primary-text-color);
    font-family: 'Poppins', sans-serif;
}
.chat-input button { padding: 12px 24px; }
</style>
<script>
const chatMessages = document.getElementById('chatMessages');
chatMessages.scrollTop = chatMessages.scrollHeight;
document.getElementById('chatForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const response = await fetch('/messages/send', {
        method: 'POST',
        body: formData
    });
    const result = await response.json();
    if (result.success) {
        location.reload();
    } else {
        alert(result.error || 'Erro ao enviar mensagem');
    }
});
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
