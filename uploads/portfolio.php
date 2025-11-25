<?php
require_once '../includes/session_check.php';
require_once '../includes/db_connect.php';

$pageTitle = "Portfólio";
$currentPage = "portfolio";
$user_id = $_SESSION['user_id'];
$feedback_message = '';
$feedback_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["media_file"])) {
    $title = trim($_POST['title']);

    if (empty($title)) {
        $feedback_message = "O título é obrigatório.";
        $feedback_type = 'error';
    } elseif (empty($_FILES["media_file"]["name"])) {
        $feedback_message = "Você precisa selecionar um arquivo.";
        $feedback_type = 'error';
    } else {
        $target_dir = "./";

        $file_extension = strtolower(pathinfo($_FILES["media_file"]["name"], PATHINFO_EXTENSION));
        $unique_file_name = uniqid('media_', true) . '.' . $file_extension;
        $target_file = $target_dir . $unique_file_name;

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES["media_file"]["tmp_name"], $target_file)) {
                $db_file_path = "uploads/" . $unique_file_name;

                $sql = "INSERT INTO portfolio (user_id, title, description, file_path, media_type) VALUES (:user_id, :title, :description, :file_path, 'image')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'user_id' => $user_id,
                    'title' => $title,
                    'description' => trim($_POST['description']),
                    'file_path' => $db_file_path,
                ]);
                $feedback_message = "Mídia adicionada com sucesso!";
                $feedback_type = 'success';
            } else {
                $feedback_message = "Ocorreu um erro ao fazer o upload do arquivo.";
                $feedback_type = 'error';
            }
        } else {
            $feedback_message = "Formato de arquivo não permitido. Use JPG, JPEG, PNG ou GIF.";
            $feedback_type = 'error';
        }
    }
}

$sql_select = "SELECT id, title, description, file_path FROM portfolio WHERE user_id = :user_id ORDER BY uploaded_at DESC";
$stmt_select = $pdo->prepare($sql_select);
$stmt_select->execute(['user_id' => $user_id]);
$portfolio_items = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtSync - <?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-text-color: #ffffff;
            --secondary-text-color: #bbbbbb;
            --background-color: #05050a;
            --glass-bg: rgba(26, 27, 31, 0.7);
            --glass-bg-light: rgba(255, 255, 255, 0.05);
            --border-color: rgba(255, 255, 255, 0.1);
            --accent-color: #ffffff;
            --success-color: #28a745;
            --error-color: #dc3545;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            font-weight: 300;
            color: var(--secondary-text-color);
            background-color: var(--background-color);
            overflow-x: hidden;
        }

        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background: rgba(10, 10, 15, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-right: 1px solid var(--border-color);
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .sidebar .logo img {
            height: 50px;
        }

        .sidebar nav {
            flex-grow: 1;
        }

        .sidebar nav ul {
            list-style: none;
        }

        .sidebar nav li a {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--secondary-text-color);
            text-decoration: none;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            background: var(--glass-bg-light);
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar nav li a:hover {
            background: var(--glass-bg);
            border-color: var(--border-color);
            color: var(--primary-text-color);
        }

        .sidebar nav li a.active {
            background: var(--accent-color);
            color: #000;
            font-weight: 500;
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--secondary-text-color);
            text-decoration: none;
            padding: 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-footer a:hover {
            color: var(--primary-text-color);
            background: rgba(220, 53, 69, 0.2);
        }

        .sidebar-footer i {
            width: 1.2em;
            text-align: center;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
            max-height: 100vh;
        }

        .main-header {
            padding-bottom: 40px;
            color: var(--primary-text-color);
        }

        .main-header h2 {
            font-weight: 500;
            font-size: 2em;
            margin-bottom: 8px;
        }

        .main-header p {
            font-size: 1.1em;
            color: var(--secondary-text-color);
            font-weight: 300;
        }

        .card {
            background: var(--glass-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 30px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            margin-bottom: 30px;
        }

        .card h3 {
            font-weight: 500;
            margin-bottom: 25px;
            color: var(--primary-text-color);
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 400;
        }

        .input-group input[type="text"],
        .input-group textarea {
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px;
            color: var(--primary-text-color);
            font-family: 'Poppins', sans-serif;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .input-group input[type="file"] {
            display: none;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 30px;
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background-color: var(--glass-bg-light);
            border-color: var(--accent-color);
        }

        .file-upload-label i {
            font-size: 1.5em;
        }

        .btn {
            display: inline-block;
            background-color: var(--accent-color);
            color: #000;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .error-message,
        .success-message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error-message {
            background-color: rgba(220, 53, 69, 0.2);
            color: #f8d7da;
            border: 1px solid var(--error-color);
        }

        .success-message {
            background-color: rgba(40, 167, 69, 0.2);
            color: #d4edda;
            border: 1px solid var(--success-color);
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .portfolio-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1 / 1;
            background-color: #111;
        }

        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease, filter 0.4s ease;
        }

        .portfolio-item:hover img {
            transform: scale(1.1);
            filter: brightness(0.5);
        }

        .item-info {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            color: white;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s ease;
        }

        .portfolio-item:hover .item-info {
            opacity: 1;
            transform: translateY(0);
        }

        .item-info h4 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .item-info p {
            font-size: 0.9em;
            margin-bottom: 15px;
            color: var(--secondary-text-color);
        }

        .item-actions .delete {
            color: #fff;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.8em;
            background-color: rgba(220, 53, 69, 0.7);
            transition: background-color 0.3s ease;
        }

        .item-actions .delete:hover {
            background-color: rgba(220, 53, 69, 1);
        }
    </style>
</head>

<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div>
                <div class="logo"><img src="../images/artsync.png" alt="Art Sync Logo"></div>
                <nav>
                    <ul>
                        <li><a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li><a href="portfolio.php" class="active"><i class="fas fa-user-circle"></i> Portfólio</a></li>
                        <li><a href="../schedule.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                        <li><a href="../career_ai.php"><i class="fas fa-robot"></i> IA de Carreira</a></li>
                    </ul>
                </nav>
            </div>
            <div class="sidebar-footer">
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h2>Meu Portfólio</h2>
                <p>Adicione e gerencie suas melhores fotos e vídeos.</p>
            </header>

            <section class="card">
                <h3>Adicionar Nova Mídia</h3>
                <form action="portfolio.php" method="post" enctype="multipart/form-data">
                    <?php if ($feedback_message): ?>
                        <div class="<?php echo $feedback_type === 'error' ? 'error-message' : 'success-message'; ?>">
                            <?php echo htmlspecialchars($feedback_message); ?>
                        </div>
                    <?php endif; ?>
                    <div class="input-group"><label for="title">Título da Mídia</label><input type="text" id="title"
                            name="title" required></div>
                    <div class="input-group"><label for="description">Descrição</label><textarea id="description"
                            name="description" rows="3"></textarea></div>
                    <div class="input-group">
                        <label for="media_file">Arquivo de Mídia</label>
                        <label for="media_file" class="file-upload-label">
                            <i class="fas fa-upload"></i>
                            <span>Clique para escolher um arquivo</span>
                        </label>
                        <input type="file" id="media_file" name="media_file" required accept="image/*">
                    </div>
                    <button type="submit" class="btn">Adicionar ao Portfólio</button>
                </form>
            </section>

            <section class="card">
                <h3>Minhas Mídias</h3>
                <div class="portfolio-grid">
                    <?php if (empty($portfolio_items)): ?>
                        <p>Você ainda não adicionou nenhuma mídia ao seu portfólio.</p>
                    <?php else: ?>
                        <?php foreach ($portfolio_items as $item): ?>
                            <div class="portfolio-item">
                                <img src="<?php echo htmlspecialchars('../' . $item['file_path']); ?>"
                                    alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                                    <div class="item-actions">
                                        <a href="delete_media.php?id=<?php echo $item['id']; ?>" class="delete"
                                            onclick="return confirm('Tem certeza?');">Excluir</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>

</html>