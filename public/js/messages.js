let currentChatUserId = null;
let chatInterval = null;
let mediaRecorder = null;
let audioChunks = [];
let attachedFile = null;
function updateMessageCount() {
    fetch('/messages/unread-count')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('messageCount');
            if (data.count > 0) {
                badge.textContent = data.count > 10 ? '10+' : data.count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
}
function loadConversations() {
    fetch('/messages/get-conversations')
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('messagesList');
            if (data.conversations && data.conversations.length > 0) {
                list.innerHTML = data.conversations.map(conv => {
                    let preview = conv.last_message || '';
                    if (preview.includes('/uploads/messages/')) {
                        const ext = preview.split('.').pop().toLowerCase().split(' ')[0];
                        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                            preview = '📷 Imagem';
                        } else if (['mp4', 'webm', 'mov', 'avi'].includes(ext)) {
                            preview = '🎥 Vídeo';
                        } else if (['mp3', 'wav', 'ogg'].includes(ext)) {
                            preview = '🎵 Áudio';
                        } else if (['flp', 'als', 'ptx', 'logic', 'rpp', 'zip', 'rar'].includes(ext)) {
                            preview = '📎 Arquivo';
                        }
                    }
                    return `
                        <div class="message-item ${conv.unread_count > 0 ? 'unread' : ''}" onclick="openChat(${conv.contact_id}, '${conv.artist_name}')">
                            <div class="msg-avatar" data-user-id="${conv.contact_id}">
                                <span>${conv.artist_name.charAt(0).toUpperCase()}</span>
                            </div>
                            <div class="msg-content">
                                <div class="msg-header">
                                    <span class="msg-name">${conv.artist_name}</span>
                                    <span class="msg-time">${formatTime(conv.last_time)}</span>
                                </div>
                                <div class="msg-preview">${preview}</div>
                            </div>
                            ${conv.unread_count > 0 ? `<span class="unread-badge">${conv.unread_count}</span>` : ''}
                        </div>
                    `;
                }).join('');
                document.querySelectorAll('.msg-avatar[data-user-id]').forEach(avatar => {
                    const userId = avatar.dataset.userId;
                    const extensions = ['jpg', 'jpeg', 'png', 'webp'];
                    const tryExt = (i) => {
                        if (i >= extensions.length) return;
                        const url = `/uploads/profile/user_${userId}.${extensions[i]}`;
                        const img = new Image();
                        img.onload = () => {
                            avatar.style.backgroundImage = `url('${url}')`;
                            avatar.style.backgroundSize = 'cover';
                            avatar.style.backgroundPosition = 'center';
                            avatar.querySelector('span').style.display = 'none';
                        };
                        img.onerror = () => tryExt(i + 1);
                        img.src = url;
                    };
                    tryExt(0);
                });
            } else {
                list.innerHTML = '<p style="text-align: center; padding: 20px; color: var(--secondary-text-color);">Nenhuma conversa ainda</p>';
            }
        });
}
function openChat(userId, userName) {
    currentChatUserId = userId;
    document.getElementById('chatContactName').textContent = userName;
    const avatar = document.getElementById('chatAvatar');
    const extensions = ['jpg', 'jpeg', 'png', 'webp'];
    let found = false;
    const tryExtension = (index) => {
        if (index >= extensions.length) {
            avatar.textContent = userName.charAt(0).toUpperCase();
            return;
        }
        const avatarUrl = `/uploads/profile/user_${userId}.${extensions[index]}`;
        const img = new Image();
        img.onload = () => {
            avatar.innerHTML = `<img src="${avatarUrl}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        };
        img.onerror = () => tryExtension(index + 1);
        img.src = avatarUrl;
    };
    tryExtension(0);
    document.getElementById('messagesDropdown').classList.remove('show');
    document.getElementById('chatModal').classList.add('show');
    loadChatMessages();
    if (chatInterval) clearInterval(chatInterval);
    chatInterval = setInterval(loadChatMessages, 3000);
}
function closeChatModal() {
    document.getElementById('chatModal').classList.remove('show');
    document.getElementById('messagesDropdown').classList.add('show');
    if (chatInterval) clearInterval(chatInterval);
    currentChatUserId = null;
    loadConversations();
    updateMessageCount();
}
function loadChatMessages() {
    if (!currentChatUserId) return;
    fetch(`/messages/get-chat?id=${currentChatUserId}`)
        .then(r => r.json())
        .then(data => {
            const area = document.getElementById('chatMessagesArea');
            if (data.messages && data.messages.length > 0) {
                const currentScroll = area.scrollTop;
                const isAtBottom = area.scrollHeight - area.scrollTop <= area.clientHeight + 50;
                area.innerHTML = data.messages.map(msg => {
                    const content = renderMessageContent(msg.message);
                    return `
                        <div class="chat-msg ${msg.is_sent ? 'sent' : 'received'}">
                            <div class="chat-bubble">
                                ${content}
                                <small>${formatTime(msg.created_at)}</small>
                            </div>
                        </div>
                    `;
                }).join('');
                if (isAtBottom || currentScroll === 0) {
                    area.scrollTop = area.scrollHeight;
                }
            } else {
                area.innerHTML = '<p style="text-align: center; color: var(--secondary-text-color);">Nenhuma mensagem ainda</p>';
            }
        });
}
function renderMessageContent(message) {
    const urlRegex = /\/uploads\/messages\/[^\s]+/g;
    const urls = message.match(urlRegex) || [];
    let content = message;
    urls.forEach(url => {
        const ext = url.split('.').pop().toLowerCase().split('?')[0];
        const filename = url.split('/').pop();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            const textBefore = content.split(url)[0];
            const textAfter = content.split(url)[1] || '';
            const cleanText = textBefore.trim();
            const replacement = `${cleanText ? cleanText + '<br>' : ''}<img src="${url}" style="max-width: 200px; max-height: 200px; border-radius: 8px; margin-top: 8px; cursor: pointer; display: block;" onclick="window.open('${url}', '_blank')">`;
            content = replacement + textAfter;
        } else if (['mp4', 'webm', 'mov', 'avi'].includes(ext)) {
            const textBefore = content.split(url)[0];
            const textAfter = content.split(url)[1] || '';
            const cleanText = textBefore.trim();
            const replacement = `${cleanText ? cleanText + '<br>' : ''}<video controls style="max-width: 100%; max-height: 200px; border-radius: 8px; display: block; margin-top: 8px; background: #000;" src="${url}" type="video/${ext}"></video>`;
            content = replacement + textAfter;
        } else if (['mp3', 'wav', 'ogg', 'webm'].includes(ext)) {
            const type = ext === 'mp3' ? 'audio/mpeg' : ext === 'wav' ? 'audio/wav' : ext === 'ogg' ? 'audio/ogg' : 'audio/webm';
            const textBefore = content.split(url)[0];
            const textAfter = content.split(url)[1] || '';
            const cleanText = textBefore.trim();
            const replacement = `${cleanText ? cleanText + '<br>' : ''}<audio controls preload="metadata" style="width: 100%; max-width: 250px; margin-top: 8px;"><source src="${url}" type="${type}">Seu navegador não suporta áudio.</audio>`;
            content = replacement + textAfter;
        } else if (['flp', 'als', 'ptx', 'logic', 'rpp', 'zip', 'rar'].includes(ext)) {
            const textBefore = content.split(url)[0];
            const textAfter = content.split(url)[1] || '';
            const cleanText = textBefore.trim();
            const replacement = `${cleanText ? cleanText + '<br>' : ''}<a href="${url}" download="${filename}" style="display: inline-block; padding: 8px 12px; background: rgba(255,255,255,0.1); border-radius: 6px; margin-top: 8px; text-decoration: none; color: inherit;"><i class="fas fa-download"></i> ${filename}</a>`;
            content = replacement + textAfter;
        }
    });
    return content;
}
function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if ((!message && !attachedFile) || !currentChatUserId) return;
    const formData = new FormData();
    formData.append('receiver_id', currentChatUserId);
    if (message) formData.append('message', message);
    if (attachedFile) formData.append('file', attachedFile);
    fetch('/messages/send', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                input.placeholder = 'Digite sua mensagem...';
                attachedFile = null;
                loadChatMessages();
            } else {
                alert(data.error || 'Erro ao enviar mensagem');
            }
        });
}
function formatTime(datetime) {
    const date = new Date(datetime);
    const now = new Date();
    const diff = now - date;
    const hours = Math.floor(diff / 3600000);
    const lang = document.documentElement.lang || 'pt-BR';
    if (hours < 1) return lang === 'en-US' ? 'Now' : 'Agora';
    if (hours < 24) return `${hours}h`;
    return date.toLocaleDateString(lang, { day: '2-digit', month: '2-digit' });
}
if (document.getElementById('messagesIcon')) {
    document.getElementById('messagesIcon').addEventListener('click', (e) => {
        e.preventDefault();
        const chatModal = document.getElementById('chatModal');
        const dropdown = document.getElementById('messagesDropdown');
        if (chatModal.classList.contains('show')) {
            chatModal.classList.remove('show');
            if (chatInterval) clearInterval(chatInterval);
            currentChatUserId = null;
            return;
        }
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        } else {
            dropdown.classList.add('show');
            loadConversations();
        }
    });
    document.addEventListener('click', (e) => {
        const container = document.querySelector('.messages-dropdown-container');
        const chatModal = document.getElementById('chatModal');
        if (container && !container.contains(e.target) && !chatModal.classList.contains('show')) {
            document.getElementById('messagesDropdown').classList.remove('show');
        }
    });
    document.getElementById('chatInput').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
    document.getElementById('chatFileInput').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            attachedFile = file;
            const input = document.getElementById('chatInput');
            input.placeholder = `📎 ${file.name}`;
        }
    });
    document.getElementById('recordAudioBtn').addEventListener('click', toggleRecording);
    document.getElementById('chatBackBtn').addEventListener('click', closeChatModal);
    updateMessageCount();
    setInterval(updateMessageCount, 30000);
}
function toggleRecording() {
    const btn = document.getElementById('recordAudioBtn');
    if (!mediaRecorder || mediaRecorder.state === 'inactive') {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                let options;
                if (MediaRecorder.isTypeSupported('audio/webm')) {
                    options = { mimeType: 'audio/webm' };
                } else if (MediaRecorder.isTypeSupported('audio/ogg')) {
                    options = { mimeType: 'audio/ogg' };
                }
                mediaRecorder = options ? new MediaRecorder(stream, options) : new MediaRecorder(stream);
                audioChunks = [];
                mediaRecorder.ondataavailable = (e) => {
                    if (e.data.size > 0) audioChunks.push(e.data);
                };
                mediaRecorder.onstop = () => {
                    const mimeType = mediaRecorder.mimeType || 'audio/webm';
                    const audioBlob = new Blob(audioChunks, { type: mimeType });
                    const ext = mimeType.includes('ogg') ? 'ogg' : 'webm';
                    const audioFile = new File([audioBlob], `audio_${Date.now()}.${ext}`, { type: mimeType });
                    attachedFile = audioFile;
                    document.getElementById('chatInput').placeholder = '🎤 Áudio gravado - Clique para enviar';
                    btn.classList.remove('recording');
                    stream.getTracks().forEach(track => track.stop());
                };
                mediaRecorder.start();
                btn.classList.add('recording');
            })
            .catch(err => {
                alert('Erro ao acessar microfone: ' + err.message);
            });
    } else if (mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
    }
}
