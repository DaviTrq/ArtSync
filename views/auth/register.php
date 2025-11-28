<?php
$pageTitle = 'Cadastro';
$currentPage = 'register';
require __DIR__ . '/../layouts/header.php';
?>
<div class="form-container">
    <a href="/" class="logo" style="display: block; text-align: center; margin-bottom: 25px;">
        <img src="/images/artsync.png" alt="Art Sync Logo" style="height: 55px; width: auto;">
    </a>
    <h2>Crie sua conta gratuita</h2>
    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form action="/register" method="POST">
        <?php use App\Security\CSRF; echo CSRF::getTokenField(); ?>
        <div class="input-group">
            <label for="artist_name">Nome Artístico</label>
            <input type="text" id="artist_name" name="artist_name" required>
        </div>
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="input-group">
            <label for="password">Senha (mín. 6 caracteres)</label>
            <input type="password" id="password" name="password" minlength="6" required>
        </div>
        <div class="input-group">
            <label for="confirm_password">Confirmar Senha</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn">Cadastrar</button>
    </form>
    <div class="switch-form">
        <p>Já tem uma conta? <a href="/login">Faça login</a></p>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
