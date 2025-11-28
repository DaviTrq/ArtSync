<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="settings-page" style="max-width: 900px; margin: 30px auto; padding: 0 20px;">
    <div class="card" style="margin-bottom: 25px;">
        <div id="networkStats" style="display: flex; justify-content: space-around; padding: 20px;">
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 600; color: var(--primary-text-color);" id="connectionsCount">0</div>
                <div style="color: var(--secondary-text-color); font-size: 0.85rem;"><?= $t['connections']; ?></div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 600; color: var(--primary-text-color);" id="followersCount">0</div>
                <div style="color: var(--secondary-text-color); font-size: 0.85rem;"><?= $t['followers']; ?></div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 600; color: var(--primary-text-color);" id="followingCount">0</div>
                <div style="color: var(--secondary-text-color); font-size: 0.85rem;"><?= $t['following']; ?></div>
            </div>
        </div>
    </div>
    <h2 style="color: var(--primary-text-color); margin-bottom: 30px;"><?= $t['settings']; ?></h2>
    <?php if (isset($flash_message)): ?>
        <div class="<?= $flash_message['type'] === 'error' ? 'error-message' : 'success-message'; ?>">
            <?= htmlspecialchars($flash_message['message']); ?>
        </div>
    <?php endif; ?>
    <div class="card" style="margin-bottom: 25px;">
        <h3 style="color: var(--primary-text-color); margin-bottom: 20px;"><?= $t['edit_profile_title']; ?></h3>
        <form action="/settings/update-profile" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <label><?= $t['artist_name']; ?></label>
                <input type="text" name="artist_name" value="<?= htmlspecialchars($_SESSION['artist_name'] ?? ''); ?>" required>
            </div>
            <div class="input-group">
                <label><?= $t['email_no_change']; ?></label>
                <input type="email" value="<?= htmlspecialchars($_SESSION['email'] ?? ''); ?>" disabled style="opacity: 0.5;">
            </div>
            <div class="input-group">
                <label><?= $t['bio']; ?></label>
                <textarea name="bio" rows="4" placeholder="<?= $t['bio_placeholder']; ?>"><?= htmlspecialchars($user_bio ?? ''); ?></textarea>
            </div>
            <div class="input-group">
                <label><?= $t['profile_photo']; ?></label>
                <div class="photo-upload-container">
                    <div class="photo-preview" id="photoPreview">
                        <?php if (file_exists(__DIR__ . '/../../public' . $avatarSrc) && $avatarSrc !== '/uploads/profile/default.png'): ?>
                            <img src="<?= htmlspecialchars($avatarSrc); ?>" alt="Preview">
                        <?php else: 
                            $userName = $_SESSION['artist_name'] ?? $_SESSION['user_name'] ?? 'U';
                            $initial = strtoupper(substr(trim($userName), 0, 1));
                        ?>
                            <span class="preview-initial"><?= htmlspecialchars($initial); ?></span>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="profile_photo" id="profilePhotoInput" accept="image/jpeg,image/png,image/webp,image/jpg" style="display: none;" onchange="previewPhoto(this)">
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn-upload" onclick="document.getElementById('profilePhotoInput').click()">
                            <i class="fas fa-camera"></i> <?= $t['choose_photo'] ?? 'Escolher Foto'; ?>
                        </button>
                        <?php if (file_exists(__DIR__ . '/../../public' . $avatarSrc) && $avatarSrc !== '/uploads/profile/default.png'): ?>
                            <button type="button" class="btn-upload" style="border-color: rgba(220, 53, 69, 0.4); color: #dc3545;" onclick="removePhoto()">
                                <i class="fas fa-trash"></i> Remover
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <small>JPG, PNG ou WEBP. Máximo 5MB.</small>
            </div>
            <button type="submit" class="btn"><?= $t['save_changes']; ?></button>
        </form>
    </div>
    <div class="card" style="margin-bottom: 25px;">
        <h3 style="color: var(--primary-text-color); margin-bottom: 20px;"><?= $t['appearance']; ?></h3>
        <div class="setting-item">
            <div class="setting-info">
                <strong><?= $t['theme']; ?></strong>
                <p><?= $t['theme_desc']; ?></p>
            </div>
            <div class="setting-action">
                <select name="theme" id="themeSelector" onchange="changeTheme(this.value)" class="theme-select">
                    <option value="dark"><?= $t['dark']; ?></option>
                    <option value="light"><?= $t['light']; ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 25px;">
        <h3 style="color: var(--primary-text-color); margin-bottom: 20px;"><?= $t['language']; ?></h3>
        <div class="setting-item">
            <div class="setting-info">
                <strong><?= $t['system_language']; ?></strong>
                <p><?= $t['language_desc']; ?></p>
            </div>
            <div class="setting-action">
                <select name="language" id="languageSelector" onchange="changeLanguage(this.value)" class="theme-select">
                    <option value="pt-BR" <?= $lang === 'pt-BR' ? 'selected' : ''; ?>>Português (Brasil)</option>
                    <option value="en-US" <?= $lang === 'en-US' ? 'selected' : ''; ?>>English (US)</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 25px;">
        <h3 style="color: var(--primary-text-color); margin-bottom: 20px;">Privacidade de Mensagens</h3>
        <div class="setting-item">
            <div class="setting-info">
                <strong>Receber mensagens diretas</strong>
                <p>Controle quem pode enviar mensagens para você</p>
            </div>
            <div class="setting-action">
                <select name="message_privacy" id="messagePrivacySelector" onchange="changeMessagePrivacy(this.value)" class="theme-select">
                    <option value="anyone">De qualquer pessoa</option>
                    <option value="connections">Somente de conectados</option>
                    <option value="none">Não receber mensagens</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 25px;">
        <h3 style="color: var(--primary-text-color); margin-bottom: 20px;"><?= $t['integrations']; ?></h3>
        <div class="setting-item">
            <div class="setting-info">
                <i class="fab fa-spotify" style="font-size: 1.3rem; color: #1DB954; flex-shrink: 0;"></i>
                <div style="min-width: 0;">
                    <strong>Spotify</strong>
                    <p><?= $t['connect_spotify']; ?></p>
                </div>
            </div>
            <div class="setting-action">
                <button class="btn-small" onclick="alert('<?= $t['coming_soon']; ?>')"><?= $t['connect']; ?></button>
            </div>
        </div>
        <div class="setting-item">
            <div class="setting-info">
                <i class="fab fa-deezer" style="font-size: 1.3rem; color: #FF0092; flex-shrink: 0;"></i>
                <div style="min-width: 0;">
                    <strong>Deezer</strong>
                    <p><?= $t['connect_deezer']; ?></p>
                </div>
            </div>
            <div class="setting-action">
                <button class="btn-small" onclick="alert('<?= $t['coming_soon']; ?>')"><?= $t['connect']; ?></button>
            </div>
        </div>
        <div class="setting-item">
            <div class="setting-info">
                <i class="fab fa-apple" style="font-size: 1.3rem; color: #FA243C; flex-shrink: 0;"></i>
                <div style="min-width: 0;">
                    <strong>Apple Music</strong>
                    <p><?= $t['connect_apple']; ?></p>
                </div>
            </div>
            <div class="setting-action">
                <button class="btn-small" onclick="alert('<?= $t['coming_soon']; ?>')"><?= $t['connect']; ?></button>
            </div>
        </div>
        <div class="setting-item">
            <div class="setting-info">
                <i class="fab fa-youtube" style="font-size: 1.3rem; color: #FF0000; flex-shrink: 0;"></i>
                <div style="min-width: 0;">
                    <strong>YouTube</strong>
                    <p><?= $t['connect_youtube']; ?></p>
                </div>
            </div>
            <div class="setting-action">
                <button class="btn-small" onclick="alert('<?= $t['coming_soon']; ?>')"><?= $t['connect']; ?></button>
            </div>
        </div>
        <div class="setting-item">
            <div class="setting-info">
                <i class="fab fa-x-twitter" style="font-size: 1.3rem; color: #fff; flex-shrink: 0;"></i>
                <div style="min-width: 0;">
                    <strong>X (Twitter)</strong>
                    <p><?= $t['connect_x']; ?></p>
                </div>
            </div>
            <div class="setting-action">
                <button class="btn-small" onclick="alert('<?= $t['coming_soon']; ?>')"><?= $t['connect']; ?></button>
            </div>
        </div>
    </div>
    <div class="card danger-zone" style="margin-bottom: 25px;">
        <h3 style="color: #dc3545; margin-bottom: 20px;"><?= $t['danger_zone']; ?></h3>
        <div class="setting-item" style="align-items: flex-start;">
            <div class="setting-info" style="flex-direction: column; align-items: flex-start; gap: 5px;">
                <strong style="color: #dc3545;"><?= $t['delete_account']; ?></strong>
                <p><?= $t['delete_account_desc']; ?></p>
            </div>
            <div class="setting-action">
                <form action="/settings/delete-account" method="post" onsubmit="return confirm('<?= $t['delete_account_desc']; ?>')">
                    <button type="submit" class="btn-danger" style="white-space: nowrap;"><?= $t['delete_account']; ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
.error-message {
    padding: 12px 20px;
    background: rgba(220, 53, 69, 0.2);
    border: 1px solid rgba(220, 53, 69, 0.5);
    border-radius: 8px;
    color: #ff6b6b;
    margin-bottom: 20px;
    font-size: 0.9rem;
}
.success-message {
    padding: 12px 20px;
    background: rgba(40, 167, 69, 0.2);
    border: 1px solid rgba(40, 167, 69, 0.5);
    border-radius: 8px;
    color: #51cf66;
    margin-bottom: 20px;
    font-size: 0.9rem;
}
.settings-page .setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    gap: 15px;
}
.settings-page .setting-item:last-child {
    border-bottom: none;
}
.settings-page .setting-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
    overflow: hidden;
}
.settings-page .setting-info strong {
    display: block;
    color: var(--primary-text-color);
    margin-bottom: 4px;
    font-size: 0.95rem;
}
.settings-page .setting-info p {
    color: var(--secondary-text-color);
    font-size: 0.85rem;
    margin: 0;
    word-wrap: break-word;
    overflow-wrap: break-word;
}
.settings-page .setting-action {
    flex-shrink: 0;
    margin-left: auto;
}
.settings-page .setting-action button,
.settings-page .setting-action select {
    white-space: nowrap;
}
.settings-page .danger-zone {
    border-color: rgba(220, 53, 69, 0.3);
    background: rgba(220, 53, 69, 0.05);
}
.settings-page .theme-select {
    padding: 8px 12px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 6px;
    color: var(--primary-text-color);
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
}
.settings-page .theme-select option {
    background: var(--glass-bg);
    color: var(--primary-text-color);
}
.settings-page .photo-upload-container {
    display: flex;
    align-items: center;
    gap: 20px;
    margin: 15px 0;
}
.settings-page .photo-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #c0c0c0, #808080);
    cursor: pointer;
    flex-shrink: 0;
}
.settings-page .photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.settings-page .preview-initial {
    font-size: 3rem;
    font-weight: 600;
    color: #000;
    user-select: none;
}
.settings-page .btn-upload {
    padding: 10px 20px;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: var(--primary-text-color);
    cursor: pointer;
    transition: 0.3s;
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    gap: 8px;
}
.settings-page .btn-upload:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.4);
}
.light-theme .card {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(0, 0, 0, 0.1);
}
.light-theme .input-group input,
.light-theme .input-group textarea {
    background: rgba(255, 255, 255, 0.5);
    border-color: rgba(0, 0, 0, 0.2);
    color: var(--primary-text-color);
}
.light-theme .input-group label,
.light-theme .input-group small {
    color: var(--secondary-text-color);
}
.light-theme .btn-upload {
    border-color: rgba(0, 0, 0, 0.2);
    color: var(--primary-text-color);
}
.light-theme .btn-upload:hover {
    background: rgba(0, 0, 0, 0.1);
    border-color: rgba(0, 0, 0, 0.4);
}
.light-theme form .btn {
    border-color: rgba(0, 0, 0, 0.2);
    color: var(--primary-text-color);
}
.light-theme form .btn:hover {
    background: rgba(0, 0, 0, 0.1);
    border-color: rgba(0, 0, 0, 0.4);
}
.light-theme .theme-select {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(0, 0, 0, 0.2);
    color: var(--primary-text-color);
}
.light-theme .theme-select option {
    background: #fff;
    color: #000;
}
.light-theme .setting-item {
    border-bottom-color: rgba(0, 0, 0, 0.1);
}
.light-theme .setting-action .btn-small {
    border-color: rgba(0, 0, 0, 0.2);
    color: var(--primary-text-color);
}
.light-theme .setting-action .btn-small:hover {
    background: rgba(0, 0, 0, 0.1);
    border-color: rgba(0, 0, 0, 0.4);
}
.light-theme .btn-danger {
    border-color: #dc3545;
    color: #dc3545;
}
.light-theme .btn-danger:hover {
    background: #dc3545;
    color: #fff;
}
.light-theme .danger-zone {
    background: rgba(220, 53, 69, 0.08);
    border-color: rgba(220, 53, 69, 0.3);
}
.light-theme .photo-preview {
    border-color: rgba(0, 0, 0, 0.2);
}
/* Garantir que sidebar não seja afetado */
.sidebar nav a i {
    font-size: 2rem !important;
    min-width: 50px !important;
}
</style>
<script>
function changeTheme(theme) {
    if (theme === 'light') {
        document.documentElement.classList.add('light-theme');
        localStorage.setItem('theme', 'light');
    } else {
        document.documentElement.classList.remove('light-theme');
        localStorage.setItem('theme', 'dark');
    }
}
function changeLanguage(lang) {
    localStorage.setItem('language', lang);
    fetch('/settings/change-language', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({language: lang})
    }).then(() => location.reload());
}
const savedTheme = localStorage.getItem('theme');
if (savedTheme) {
    document.getElementById('themeSelector').value = savedTheme;
    changeTheme(savedTheme);
}
const currentLang = '<?= $lang ?>';
document.getElementById('languageSelector').value = currentLang;
fetch('/network/stats')
    .then(r => r.json())
    .then(data => {
        document.getElementById('connectionsCount').textContent = data.connections;
        document.getElementById('followersCount').textContent = data.followers;
        document.getElementById('followingCount').textContent = data.following;
    });
fetch('/settings/get-message-privacy')
    .then(r => r.json())
    .then(data => {
        if (data.privacy) {
            document.getElementById('messagePrivacySelector').value = data.privacy;
        }
    });
function changeMessagePrivacy(privacy) {
    fetch('/settings/update-message-privacy', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({privacy: privacy})
    }).then(r => r.json()).then(data => {
        if (data.success) {
            alert('Preferência atualizada com sucesso!');
        }
    });
}
</script>
<script>
function previewPhoto(input) {
    const photoPreview = document.getElementById('photoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            photoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function removePhoto() {
    if (confirm('Remover foto de perfil?')) {
        fetch('/settings/remove-photo', {
            method: 'POST'
        }).then(r => r.json()).then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
