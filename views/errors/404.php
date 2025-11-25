<?php
$pageTitle = 'Página Não Encontrada';
$currentPage = 'error';
require __DIR__ . '/../layouts/header.php';
?>

<div class="error-container" style="text-align: center; padding: 50px;">
    <h1>404 - Página Não Encontrada</h1>
    <p>A página que você está procurando não existe.</p>
    <a href="/" class="btn">Voltar ao Início</a>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>