<?php
namespace App\Controllers;
use App\Models\ScheduleEvent;
use App\Repositories\PDO\PdoScheduleRepository;
class ScheduleController extends AuthController {
    private PdoScheduleRepository $repoAgenda;
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->repoAgenda = new PdoScheduleRepository();
    }
    private function redir(): void {
        !headers_sent() && header('Location: /schedule');
        exit;
    }
    private $t;
    public function index(): void {
        $eventos = $this->repoAgenda->getByUserId($_SESSION['user_id']);
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $this->t = $trad[$idioma] ?? $trad['pt-BR'];
        $t = $this->t;
        $this->view('schedule/index', [
            'pageTitle' => $t['schedule'],
            'currentPage' => 'schedule',
            'events' => $eventos,
            'feedback' => $_SESSION['feedback'] ?? null
        ]);
        unset($_SESSION['feedback']);
    }
    public function create(): void {
        $titulo = htmlspecialchars(trim($_POST['event_title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $data = trim($_POST['event_date'] ?? '');
        $hora = trim($_POST['event_time'] ?? '00:00');
        $local = htmlspecialchars(trim($_POST['event_location'] ?? ''), ENT_QUOTES, 'UTF-8');
        $notas = htmlspecialchars(trim($_POST['notes'] ?? ''), ENT_QUOTES, 'UTF-8');
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $this->t = $trad[$idioma] ?? $trad['pt-BR'];
        if ($titulo === '' || $data === '') {
            $this->setFb('error', $this->t['title_date_required']);
            $this->redir();
        }
        $dataHora = $data . ' ' . $hora . ':00';
        if (!strtotime($dataHora)) {
            $this->setFb('error', $this->t['invalid_datetime']);
            $this->redir();
        }
        $cor = $_POST['event_color'] ?? '#4CAF50';
        $priority = $_POST['priority'] ?? 'low';
        $evt = new ScheduleEvent(null, $_SESSION['user_id'], $titulo, $dataHora, $notas);
        $evt->location = $local !== '' ? $local : '';
        $evt->color = $cor;
        $evt->priority = $priority;
        try {
            $ok = $this->repoAgenda->save($evt);
            $this->setFb($ok ? 'success' : 'error', $ok ? $this->t['event_created'] : $this->t['create_error']);
        } catch (\Throwable $e) {
            $this->setFb('error', $this->t['create_error'] . ': ' . $e->getMessage());
        }
        $this->redir();
    }
    public function update(): void {
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $this->t = $trad[$idioma] ?? $trad['pt-BR'];
        $id = (int)($_POST['event_id'] ?? 0);
        $titulo = htmlspecialchars(trim($_POST['event_title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $data = trim($_POST['event_date'] ?? '');
        $hora = trim($_POST['event_time'] ?? '00:00');
        $local = htmlspecialchars(trim($_POST['event_location'] ?? ''), ENT_QUOTES, 'UTF-8');
        $notas = htmlspecialchars(trim($_POST['notes'] ?? ''), ENT_QUOTES, 'UTF-8');
        $cor = $_POST['event_color'] ?? '#4CAF50';
        $priority = $_POST['priority'] ?? 'low';
        if ($id && $titulo && $data) {
            $dataHora = $data . ' ' . $hora . ':00';
            $evt = new ScheduleEvent($id, $_SESSION['user_id'], $titulo, $dataHora, $notas);
            $evt->location = $local;
            $evt->color = $cor;
            $evt->priority = $priority;
            $this->repoAgenda->update($evt);
            $this->setFb('success', $this->t['event_updated']);
        } else {
            $this->setFb('error', $this->t['invalid_data']);
        }
        $this->redir();
    }
    public function delete(): void {
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $this->t = $trad[$idioma] ?? $trad['pt-BR'];
        $id = $_GET['id'] ?? null;
        $id && ctype_digit($id) && $this->repoAgenda->delete((int)$id, $_SESSION['user_id']) && $this->setFb('success', $this->t['event_deleted']);
        $this->redir();
    }
    private function setFb(string $tipo, string $msg): void {
        $_SESSION['feedback'] = ['type' => $tipo, 'message' => $msg];
    }
}
