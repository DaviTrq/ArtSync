<?php

namespace App\Controllers;

use App\Models\PortfolioProject;
use App\Repositories\PDO\PdoPortfolioProjectRepository;

class PortfolioController extends AuthController {
    private PdoPortfolioProjectRepository $repoPortfolio;
    private string $dirUpload;

    public function __construct() {
        parent::__construct();
        $this->repoPortfolio = new PdoPortfolioProjectRepository();
        $this->dirUpload = __DIR__ . '/../../public/uploads/portfolio/';
        !is_dir($this->dirUpload) && mkdir($this->dirUpload, 0755, true);
    }

    public function index(): void {
        $this->checkAuth();
        $projetos = $this->repoPortfolio->getByUserId((int)$_SESSION['user_id']);
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $t = $trad[$idioma] ?? $trad['pt-BR'];

        $this->view('portfolio/index', [
            'pageTitle' => $t['portfolio'],
            'currentPage' => 'portfolio',
            'projects' => $projetos,
            'feedback' => $_SESSION['feedback'] ?? null
        ]);
        unset($_SESSION['feedback']);
    }

    public function create(): void {
        $this->checkAuth();
        $_SERVER['REQUEST_METHOD'] !== 'POST' && header('Location: /portfolio') && exit;

        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $t = $trad[$idioma] ?? $trad['pt-BR'];

        $titulo = htmlspecialchars(trim($_POST['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $desc = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');

        if (empty($titulo)) {
            $_SESSION['feedback'] = ['type' => 'error', 'message' => $t['title_required']];
            header('Location: /portfolio');
            exit;
        }

        $slug = $this->gerarSlug($titulo, $_SESSION['user_id']);
        $proj = new PortfolioProject(null, (int)$_SESSION['user_id'], $titulo, $desc, $slug);
        $idProj = $this->repoPortfolio->save($proj);

        isset($_FILES['media_files']) && $this->uploadArquivos($idProj, $_FILES['media_files']);

        $_SESSION['feedback'] = ['type' => 'success', 'message' => $t['project_created']];
        header('Location: /portfolio');
        exit;
    }

    private function uploadArquivos(int $idProj, array $arqs): void {
        $tipos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'audio/mpeg', 'audio/wav', 'video/mp4'];
        for ($i = 0; $i < count($arqs['name']); $i++) {
            if ($arqs['error'][$i] !== UPLOAD_ERR_OK) continue;
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $arqs['tmp_name'][$i]);
            finfo_close($finfo);
            if (!in_array($mime, $tipos) || $arqs['size'][$i] > 10485760) continue;
            $ext = pathinfo($arqs['name'][$i], PATHINFO_EXTENSION);
            $nomeArq = uniqid('media_') . '.' . $ext;
            $caminho = $this->dirUpload . $nomeArq;
            if (move_uploaded_file($arqs['tmp_name'][$i], $caminho)) {
                $tipo = str_starts_with($mime, 'image/') ? 'image' : (str_starts_with($mime, 'audio/') ? 'audio' : 'video');
                $this->repoPortfolio->addMedia($idProj, '/uploads/portfolio/' . $nomeArq, $tipo, $mime, $arqs['size'][$i]);
            }
        }
    }

    public function delete(): void {
        $this->checkAuth();
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $t = $trad[$idioma] ?? $trad['pt-BR'];

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $id <= 0 && header('Location: /portfolio') && exit;
        $this->repoPortfolio->delete($id, (int)$_SESSION['user_id']);
        $_SESSION['feedback'] = ['type' => 'success', 'message' => $t['project_deleted']];
        header('Location: /portfolio');
        exit;
    }

    public function viewPublic(): void {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            http_response_code(404);
            echo '404';
            exit;
        }
        $project = $this->repoPortfolio->getBySlug($slug);
        !$project && http_response_code(404) && exit('404');
        require __DIR__ . '/../../views/portfolio/public.php';
    }

    private function gerarSlug(string $titulo, int $idUsr): string {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
        return 'presskit-' . $slug . '-' . $idUsr . '-' . time();
    }
}
