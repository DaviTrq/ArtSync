<?php
namespace App\Controllers;
use App\Repositories\PDO\PdoUserRepository;
class SettingsController {
    private $repo;
    public function __construct() {
        $this->repo = new PdoUserRepository();
    }
    public function index() {
        $this->reqLogin();
        $idUsr = $_SESSION['user_id'];
        $bio = '';
        $email = $_SESSION['email'] ?? '';
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $t = $trad[$idioma] ?? $trad['pt-BR'];
        $dados = ['user_bio' => $bio, 'user_email' => $email, 'flash_message' => $_SESSION['flash_message'] ?? null, 'currentPage' => 'settings', 'pageTitle' => $t['settings']];
        unset($_SESSION['flash_message']);
        extract($dados);
        $currentPage = 'settings';
        $pageTitle = $t['settings'];
        $caminho = __DIR__ . '/../../views/settings/index.php';
        file_exists($caminho) ? require $caminho : die('View não encontrada');
    }
    public function updateNotifications() {
        $this->reqLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idioma = $_SESSION['lang'] ?? 'pt-BR';
            $trad = require __DIR__ . '/../../config/lang.php';
            $t = $trad[$idioma] ?? $trad['pt-BR'];
            $idUsr = $_SESSION['user_id'];
            $notif = isset($_POST['notifications']) ? 1 : 0;
            method_exists($this->repo, 'update') && $this->repo->update($idUsr, ['wants_notifications' => $notif]);
            $_SESSION['flash_message'] = $t['preferences_saved'];
        }
        header('Location: /settings');
        exit;
    }
    public function updateProfile() {
        $this->reqLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idioma = $_SESSION['lang'] ?? 'pt-BR';
            $trad = require __DIR__ . '/../../config/lang.php';
            $t = $trad[$idioma] ?? $trad['pt-BR'];
            $idUsr = $_SESSION['user_id'];
            $nome = htmlspecialchars(trim($_POST['artist_name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $bio = htmlspecialchars(trim($_POST['bio'] ?? ''), ENT_QUOTES, 'UTF-8');
            $dados = ['artist_name' => $nome, 'bio' => $bio];
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $arq = $_FILES['profile_photo'];
                $tipos = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $arq['tmp_name']);
                finfo_close($finfo);
                if (in_array($mime, $tipos) && $arq['size'] <= 5242880) {
                    $dirUp = __DIR__ . '/../../public/uploads/profile/';
                    !is_dir($dirUp) && mkdir($dirUp, 0755, true);
                    foreach (['jpg', 'jpeg', 'png', 'webp'] as $oldExt) {
                        $old = $dirUp . "user_{$idUsr}.{$oldExt}";
                        file_exists($old) && unlink($old);
                    }
                    $ext = strtolower(pathinfo($arq['name'], PATHINFO_EXTENSION));
                    !in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && $ext = 'jpg';
                    $nomeArq = "user_{$idUsr}.{$ext}";
                    $caminho = $dirUp . $nomeArq;
                    if (move_uploaded_file($arq['tmp_name'], $caminho)) {
                        $_SESSION['user_profile'] = "/uploads/profile/{$nomeArq}";
                        !isset($_SESSION['flash_message']) && $_SESSION['flash_message'] = ['type' => 'success', 'message' => $t['profile_updated']];
                    } else {
                        $_SESSION['flash_message'] = ['type' => 'error', 'message' => $t['photo_save_error']];
                    }
                } else {
                    $_SESSION['flash_message'] = ['type' => 'error', 'message' => $t['invalid_file']];
                }
            }
            if (method_exists($this->repo, 'update')) {
                $this->repo->update($idUsr, $dados);
                $_SESSION['artist_name'] = $nome;
            }
            !isset($_SESSION['flash_message']) && $_SESSION['flash_message'] = ['type' => 'success', 'message' => $t['profile_updated']];
        }
        header('Location: /settings');
        exit;
    }
    public function deleteAccount() {
        $this->reqLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsr = $_SESSION['user_id'];
            method_exists($this->repo, 'delete') && $this->repo->delete($idUsr);
            $this->sair();
            return;
        }
        header('Location: /settings');
        exit;
    }
    public function changeLanguage() {
        $this->reqLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = json_decode(file_get_contents('php://input'), true);
            $idioma = $dados['language'] ?? 'pt-BR';
            if (in_array($idioma, ['pt-BR', 'en-US'])) {
                $_SESSION['lang'] = $idioma;
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            exit;
        }
        header('Location: /settings');
        exit;
    }
    public function removePhoto() {
        $this->reqLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsr = $_SESSION['user_id'];
            $dirUp = __DIR__ . '/../../public/uploads/profile/';
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                $arq = $dirUp . "user_{$idUsr}.{$ext}";
                file_exists($arq) && unlink($arq);
            }
            unset($_SESSION['user_profile']);
            echo json_encode(['success' => true]);
            exit;
        }
        header('Location: /settings');
        exit;
    }
    public function getMessagePrivacy() {
        $this->reqLogin();
        $pdo = new \PDO("mysql:host=localhost;dbname=artsync_db;charset=utf8mb4", 'root', '');
        $stmt = $pdo->prepare("SELECT message_privacy FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $privacy = $stmt->fetchColumn() ?: 'anyone';
        echo json_encode(['privacy' => $privacy]);
        exit;
    }
    public function updateMessagePrivacy() {
        $this->reqLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = json_decode(file_get_contents('php://input'), true);
            $privacy = $dados['privacy'] ?? 'anyone';
            if (in_array($privacy, ['anyone', 'connections', 'none'])) {
                $pdo = new \PDO("mysql:host=localhost;dbname=artsync_db;charset=utf8mb4", 'root', '');
                $stmt = $pdo->prepare("UPDATE users SET message_privacy = ? WHERE id = ?");
                $stmt->execute([$privacy, $_SESSION['user_id']]);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            exit;
        }
    }
    private function reqLogin() {
        session_status() === PHP_SESSION_NONE && session_start();
        !isset($_SESSION['user_id']) && header('Location: /login') && exit;
    }
    private function sair() {
        session_status() === PHP_SESSION_NONE && session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
