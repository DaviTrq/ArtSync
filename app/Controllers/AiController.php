<?php
namespace App\Controllers;
use App\Services\AiService;
class AiController extends AuthController {
    private AiService $svc;
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->svc = new AiService();
    }
    public function index(): void {
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $t = $trad[$idioma] ?? $trad['pt-BR'];
        $this->view('ai/index', ['pageTitle' => $t['career_ai'], 'currentPage' => 'ia']);
    }
    public function ask(): void {
        $pergunta = htmlspecialchars(trim($_POST['user_question'] ?? ''), ENT_QUOTES, 'UTF-8');
        $arqConteudo = null;
        $mime = null;
        $nomeArq = null;
        if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['media_file']['tmp_name'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp);
            finfo_close($finfo);
            $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'audio/mpeg', 'audio/wav'];
            if (in_array($mime, $permitidos) && $_FILES['media_file']['size'] <= 10485760) {
                $arqConteudo = file_get_contents($tmp);
                $nomeArq = htmlspecialchars($_FILES['media_file']['name'], ENT_QUOTES, 'UTF-8');
            } else {
                $_SESSION['feedback'] = ['type' => 'error', 'message' => 'Arquivo inválido'];
                $arqConteudo = null;
                $mime = null;
            }
        }
        $resp = $this->svc->getCareerAdvice($pergunta, $arqConteudo, $mime);
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $t = $trad[$idioma] ?? $trad['pt-BR'];
        $this->view('ai/index', [
            'pageTitle' => $t['career_ai'],
            'currentPage' => 'ia',
            'user_question' => $pergunta,
            'uploaded_file_name' => $nomeArq,
            'ai_response' => $resp,
            'feedback' => $_SESSION['feedback'] ?? null
        ]);
        unset($_SESSION['feedback']);
    }
}
