<?php
namespace App\Controllers;
use App\Repositories\PDO\PdoUserRepository;
class AdminController extends AuthController {
    protected PdoUserRepository $repo;
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $idioma = $_SESSION['lang'] ?? 'pt-BR';
            $trad = require __DIR__ . '/../../config/lang.php';
            $t = $trad[$idioma] ?? $trad['pt-BR'];
            $_SESSION['feedback'] = ['type' => 'error', 'message' => $t['access_denied']];
            header('Location: /dashboard');
            exit;
        }
        $this->repo = new PdoUserRepository();
    }
    public function index(): void {
        $usuarios = $this->repo->getAll();
        require_once __DIR__ . '/../../app/Repositories/PDO/PdoForumRepository.php';
        $forumRepo = new \App\Repositories\PDO\PdoForumRepository();
        $topicos = $forumRepo->getAllTopics();
        $fb = null;
        if (isset($_SESSION['feedback'])) {
            $fb = $_SESSION['feedback'];
            unset($_SESSION['feedback']);
        }
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $t = $trad[$idioma] ?? $trad['pt-BR'];
        $this->view('admin/index', [
            'pageTitle' => $t['admin_panel'],
            'currentPage' => 'admin',
            'users' => $usuarios,
            'topics' => $topicos,
            'feedback' => $fb
        ]);
    }
    public function deleteUser(): void {
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $t = $trad[$idioma] ?? $trad['pt-BR'];
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $_SESSION['feedback'] = ['type' => 'error', 'message' => $t['invalid_id']];
            header('Location: /admin');
            exit;
        }
        if ($id === $_SESSION['user_id']) {
            $_SESSION['feedback'] = ['type' => 'error', 'message' => $t['cannot_delete_own']];
        } else {
            $this->repo->delete($id);
            $_SESSION['feedback'] = ['type' => 'success', 'message' => $t['user_deleted']];
        }
        header('Location: /admin');
        exit;
    }
}
