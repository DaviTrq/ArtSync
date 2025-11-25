<?php

namespace App\Controllers;

class SearchController extends AuthController {
    private $pdo;

    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->pdo = new \PDO("mysql:host=localhost;dbname=artsync_db;charset=utf8mb4", 'root', '');
    }

    public function index() {
        $q = $_GET['q'] ?? '';
        $res = ['users' => [], 'topics' => [], 'features' => []];
        if ($q) {
            $stmt = $this->pdo->prepare("SELECT id, artist_name, email FROM users WHERE artist_name LIKE ? LIMIT 5");
            $stmt->execute(['%' . $q . '%']);
            $res['users'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt = $this->pdo->prepare("SELECT t.id, t.title, u.artist_name FROM forum_topics t JOIN users u ON t.user_id = u.id WHERE t.is_approved = 1 AND t.title LIKE ? LIMIT 5");
            $stmt->execute(['%' . $q . '%']);
            $res['topics'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $funcs = [
                ['name' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'fa-home'],
                ['name' => 'Portfólio', 'url' => '/portfolio', 'icon' => 'fa-user-circle'],
                ['name' => 'Agenda', 'url' => '/schedule', 'icon' => 'fa-calendar-alt'],
                ['name' => 'Fórum', 'url' => '/forum', 'icon' => 'fa-comments'],
                ['name' => 'Rede', 'url' => '/network', 'icon' => 'fa-compass'],
                ['name' => 'IA Carreira', 'url' => '/ai', 'icon' => 'fa-robot'],
                ['name' => 'Configurações', 'url' => '/settings', 'icon' => 'fa-cog']
            ];
            foreach ($funcs as $f) {
                stripos($f['name'], $q) !== false && $res['features'][] = $f;
            }
        }
        $this->view('search/index', ['pageTitle' => 'Buscar', 'currentPage' => 'search', 'query' => $q, 'results' => $res]);
    }
}
