<?php
/** @var array $user */
/** @var array|null $feedback */
$avatarPath = '/uploads/profile/default.png';
$userId = (int) ($_SESSION['user_id'] ?? 0);

$base = __DIR__ . '/../../public/uploads/profile';
$web  = '/uploads/profile';
$found = null;
foreach (['jpg','jpeg','png','webp'] as $ext) {
    $p = "{$base}/user_{$userId}.{$ext}";
    if (file_exists($p)) { $found = "{$web}/user_{$userId}.{$ext}"; break; }
}
if ($found) {
    $avatarPath = $found . '?t=' . time();
} elseif (!empty($_SESSION['user_profile'])) {
    $avatarPath = $_SESSION['user_profile'];
}
?>

<style>
    .profile-container { max-width: 860px; margin: 0 auto; padding-bottom: 50px; }
    
    /* Grid do Formulário */
    .profile-edit-grid { display: grid; grid-template-columns: 280px 1fr; gap: 40px; }
    @media (max-width: 840px) { .profile-edit-grid { grid-template-columns: 1fr; } }

    /* Avatar */
    .profile-photo { display: flex; flex-direction: column; align-items: center; }
    .avatar-preview {
        width: 200px; height: 200px; border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.1);
        overflow: hidden; background: rgba(255, 255, 255, 0.05);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); margin-bottom: 20px;
    }
    .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }

    /* Lista de Configurações */
    .settings-list { display: flex; flex-direction: column; gap: 15px; }
    .setting-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px; background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px; transition: all 0.3s ease;
    }
    .setting-item:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateX(5px);
    }
    .setting-info { display: flex; align-items: center; gap: 15px; }
    
    .setting-icon {
        width: 40px; height: 40px; border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.2rem;
    }
    
    .setting-text h4 { color: #fff; margin-bottom: 4px; font-size: 1rem; font-weight: 500; margin-top: 0; }
    .setting-text p { color: #bbb; font-size: 0.85rem; margin: 0; }

    /* Botão Pequeno */
    .btn-small {
        padding: 8px 20px; font-size: 0.85rem; border-radius: 6px;
        background: transparent; border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff; cursor: pointer; transition: 0.3s; text-decoration: none;
        display: inline-block;
    }
    .btn-small:hover { background: #fff; color: #000; }

    /* Zona de Perigo */
    .danger-zone:hover { background: rgba(220, 53, 69, 0.1); border-color: #dc3545; }
    .danger-zone .setting-icon { color: #dc3545; background: rgba(220, 53, 69, 0.1); }
    .danger-zone .btn-small { border-color: #dc3545; color: #dc3545; }
    .danger-zone .btn-small:hover { background: #dc3545; color: white; }
</style>

<div class="profile-container">
    
    <section class="card">
        <h3>Editar Perfil</h3>
        <?php if (!empty($feedback)): ?>
            <div class="<?= $feedback['type'] === 'success' ? 'success-message' : 'error-message' ?>" style="margin:15px 0;">
                <?= htmlspecialchars($feedback['message']); ?>
            </div>
        <?php endif; ?>

        <form action="/profile/update" method="POST" enctype="multipart/form-data">
            <div class="profile-edit-grid">
                <div class="profile-photo">
                    <div class="avatar-preview">
                        <img id="avatarPreview" src="<?= htmlspecialchars($avatarPath); ?>" alt="Avatar">
                    </div>
                    <label class="btn btn-small" for="avatarInput" style="cursor:pointer; width:auto; margin-top: 15px;">Alterar Foto</label>
                    <input id="avatarInput" type="file" name="avatar" accept="image/*" style="display:none;">
                </div>

                <div class="profile-fields">
                    <div class="input-group">
                        <label>Nome Artístico</label>
                        <input type="text" name="artist_name" value="<?= htmlspecialchars($user['artist_name'] ?? ''); ?>" required>
                    </div>

                    <div class="input-group">
                        <label>E-mail de Acesso</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Nova Senha</label>
                        <input type="password" name="password" placeholder="Preencha apenas se quiser alterar">
                    </div>

                    <button class="btn" type="submit">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </section>

    <section class="card" style="margin-top: 30px;">
        <h3>Configurações</h3>
        
        <div class="settings-list">
            
            <div class="setting-item">
                <div class="setting-info">
                    <div class="setting-icon"><i class="fas fa-globe"></i></div>
                    <div class="setting-text">
                        <h4>Idioma do Sistema</h4>
                        <p>O idioma atual é Português (Brasil).</p>
                    </div>
                </div>
                <div class="setting-action">
                    <button type="button" class="btn-small" id="btnChangeLang">Mudar para Inglês</button>
                </div>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <div class="setting-icon"><i class="fas fa-question-circle"></i></div>
                    <div class="setting-text">
                        <h4>Central de Ajuda</h4>
                        <p>Dúvidas sobre como usar a IA ou o Dashboard?</p>
                    </div>
                </div>
                <div class="setting-action">
                    <a href="/help" class="btn-small">Acessar Ajuda</a>
                </div>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <div class="setting-icon"><i class="fas fa-adjust"></i></div>
                    <div class="setting-text">
                        <h4>Aparência</h4>
                        <p>Alternar entre tema Claro e Escuro.</p>
                    </div>
                </div>
                <div class="setting-action">
                    <button type="button" class="btn-small" id="btnChangeTheme">Alternar</button>
                </div>
            </div>

            <div class="setting-item danger-zone">
                <div class="setting-info">
                    <div class="setting-icon"><i class="fas fa-sign-out-alt"></i></div>
                    <div class="setting-text">
                        <h4 style="color: #dc3545;">Encerrar Sessão</h4>
                        <p>Desconectar sua conta deste dispositivo.</p>
                    </div>
                </div>
                <div class="setting-action">
                    <a href="/logout" class="btn-small">Sair</a>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Preview da Imagem
    const input = document.getElementById('avatarInput');
    const img   = document.getElementById('avatarPreview');
    if (input && img) {
        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);
        });
    }
});
</script>