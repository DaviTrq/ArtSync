<?php

namespace App\Controllers;

use App\Repositories\PDO\PdoForumRepository;

class ForumController {
    private $repo;

    public function __construct() {
        $this->repo = new PdoForumRepository();
    }

    public function index() {
        $this->reqLogin();
        $topicos = $this->repo->getApprovedTopics();
        $currentPage = 'forum';
        $pageTitle = 'Fórum';
        require __DIR__ . '/../../views/forum/index.php';
    }

    public function view() {
        $this->reqLogin();
        $idTopico = $_GET['id'] ?? null;
        !$idTopico && header('Location: /forum') && exit;
        $topico = $this->repo->getTopicById($idTopico);
        (!$topico || (!$topico->isApproved && !$_SESSION['is_admin'])) && header('Location: /forum') && exit;
        $comments = $this->repo->getCommentsByTopicId($idTopico);
        $currentPage = 'forum';
        $pageTitle = $topico->title;
        require __DIR__ . '/../../views/forum/view.php';
    }

    public function create() {
        $this->reqLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = htmlspecialchars(trim($_POST['title'] ?? ''), ENT_QUOTES, 'UTF-8');
            $conteudo = htmlspecialchars(trim($_POST['content'] ?? ''), ENT_QUOTES, 'UTF-8');
            if ($titulo && $conteudo) {
                $idTopico = $this->repo->createTopic($_SESSION['user_id'], $titulo, $conteudo);
                if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['tmp_name'])) {
                    $dirUp = __DIR__ . '/../../public/uploads/forum/';
                    !is_dir($dirUp) && mkdir($dirUp, 0755, true);
                    foreach ($_FILES['attachments']['tmp_name'] as $k => $tmp) {
                        if ($_FILES['attachments']['error'][$k] === UPLOAD_ERR_OK) {
                            $nomeArq = time() . '_' . $k . '_' . basename($_FILES['attachments']['name'][$k]);
                            $caminho = $dirUp . $nomeArq;
                            if (move_uploaded_file($tmp, $caminho)) {
                                $ext = strtolower(pathinfo($nomeArq, PATHINFO_EXTENSION));
                                $tipo = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : (in_array($ext, ['mp3', 'wav']) ? 'audio' : 'link');
                                $this->repo->createTopicAttachment($idTopico, '/uploads/forum/' . $nomeArq, $tipo);
                            }
                        }
                    }
                }
                require_once __DIR__ . '/../../app/Services/NotificationService.php';
                $notifSvc = new \App\Services\NotificationService();
                $notifSvc->notifyAdminsNewTopic($idTopico, $titulo, $_SESSION['artist_name'] ?? 'Usuário');
                $idioma = $_SESSION['lang'] ?? 'pt-BR';
                $trad = require __DIR__ . '/../../config/lang.php';
                $t = $trad[$idioma] ?? $trad['pt-BR'];
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => $t['topic_created']];
            }
        }
        header('Location: /forum');
        exit;
    }

    public function comment() {
        $this->reqLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idTopico = $_POST['topic_id'] ?? null;
            $conteudo = htmlspecialchars(trim($_POST['content'] ?? ''), ENT_QUOTES, 'UTF-8');
            if ($idTopico && $conteudo) {
                $idComent = $this->repo->createComment($idTopico, $_SESSION['user_id'], $conteudo);
                if (isset($_FILES['attachments'])) {
                    $dirUp = __DIR__ . '/../../public/uploads/forum/';
                    !is_dir($dirUp) && mkdir($dirUp, 0755, true);
                    foreach ($_FILES['attachments']['tmp_name'] as $k => $tmp) {
                        if ($_FILES['attachments']['error'][$k] === UPLOAD_ERR_OK) {
                            $nomeArq = time() . '_' . basename($_FILES['attachments']['name'][$k]);
                            $caminho = $dirUp . $nomeArq;
                            if (move_uploaded_file($tmp, $caminho)) {
                                $mime = mime_content_type($caminho);
                                $tipo = strpos($mime, 'image') !== false ? 'image' : (strpos($mime, 'audio') !== false ? 'audio' : 'link');
                                $this->repo->createAttachment($idComent, '/uploads/forum/' . $nomeArq, $tipo);
                            }
                        }
                    }
                }
            }
            header('Location: /forum/view?id=' . $idTopico);
            exit;
        }
    }

    public function approve() {
        $this->reqAdmin();
        $idTopico = $_GET['id'] ?? null;
        if ($idTopico) {
            $this->repo->approveTopic($idTopico);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function delete() {
        $this->reqAdmin();
        $idTopico = $_GET['id'] ?? null;
        if ($idTopico) {
            $this->repo->deleteTopic($idTopico);
            $idioma = $_SESSION['lang'] ?? 'pt-BR';
            $trad = require __DIR__ . '/../../config/lang.php';
            $t = $trad[$idioma] ?? $trad['pt-BR'];
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => $t['topic_deleted'] ?? 'Tópico deletado'];
        }
        header('Location: /admin');
        exit;
    }

    private function reqLogin() {
        session_status() === PHP_SESSION_NONE && session_start();
        !isset($_SESSION['user_id']) && header('Location: /login') && exit;
    }

    private function reqAdmin() {
        $this->reqLogin();
        empty($_SESSION['is_admin']) && header('Location: /dashboard') && exit;
    }
}
