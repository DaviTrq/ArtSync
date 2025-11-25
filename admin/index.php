<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../vendor/PHPMailer.php';
require '../vendor/SMTP.php';
require '../vendor/Exception.php';

var_dump($_SESSION);

require_once '../includes/db_connect.php'; 
require_once '../includes/session_check.php';
require_once '../includes/admin_check.php';

$feedback_message = '';
$feedback_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $artist_name = trim($_POST['artist_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($artist_name) || empty($email) || empty($password)) {
        $feedback_message = "Todos os campos são obrigatórios.";
        $feedback_type = 'error';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $feedback_message = "Este email já está cadastrado.";
            $feedback_type = 'error';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (artist_name, email, password, is_admin) VALUES (:name, :email, :pass, :admin)";
            $stmt_insert = $pdo->prepare($sql);
            if ($stmt_insert->execute(['name' => $artist_name, 'email' => $email, 'pass' => $hashed_password, 'admin' => $is_admin])) {
                $feedback_message = "Usuário cadastrado com sucesso!";
                $feedback_type = 'success';
            } else {
                $feedback_message = "Erro ao cadastrar usuário.";
                $feedback_type = 'error';
            }
        }
    }
}

$all_users = $pdo->query("SELECT id, artist_name, email, is_admin, created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gerenciar Usuários</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <header class="admin-header">
        <h1>ArtSync - Painel Administrativo</h1>
        <nav>
            <a href="index.php" class="active">Gerenciar Usuários</a>
            <a href="../dashboard.php">Voltar para o Site</a>
            <a href="../logout.php">Sair</a>
        </nav>
    </header>

    <main class="admin-container">
        <div class="admin-card">
            <h2>Adicionar Novo Usuário</h2>
            <?php if ($feedback_message): ?>
                <div class="<?php echo $feedback_type === 'error' ? 'error-message' : 'success-message'; ?>">
                    <?php echo $feedback_message; ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <input type="hidden" name="add_user" value="1">
                <div class="form-row">
                    <div class="input-group">
                        <label for="artist_name">Nome Artístico</label>
                        <input type="text" id="artist_name" name="artist_name" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label for="password">Senha Provisória</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="input-group-checkbox">
                        <input type="checkbox" id="is_admin" name="is_admin" value="1">
                        <label for="is_admin">Tornar Administrador?</label>
                    </div>
                </div>
                <button type="submit" class="btn">Adicionar Usuário</button>
            </form>
        </div>

        <div class="admin-card">
            <h2>Usuários Cadastrados</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome Artístico</th>
                            <th>Email</th>
                            <th>Admin?</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['artist_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['is_admin'] ? 'Sim' : 'Não'; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="#" title="Editar">✏️</a>
                                    <a href="#" title="Excluir">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>
