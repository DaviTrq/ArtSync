<?php
namespace App\Controllers;
class NetworkController extends AuthController {
    private $pdo;
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->pdo = new \PDO("mysql:host=localhost;dbname=artsync_db;charset=utf8mb4", 'root', '');
    }
    public function index() {
        $busca = $_GET['search'] ?? '';
        $usuarios = [];
        if ($busca) {
            $stmt = $this->pdo->prepare("SELECT id, artist_name, email FROM users WHERE artist_name LIKE ? AND id != ? LIMIT 20");
            $stmt->execute(['%' . $busca . '%', $_SESSION['user_id']]);
            $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($usuarios as &$usr) {
                $stmt = $this->pdo->prepare("SELECT status FROM user_connections WHERE follower_id = ? AND following_id = ?");
                $stmt->execute([$_SESSION['user_id'], $usr['id']]);
                $usr['connection_status'] = $stmt->fetchColumn() ?: null;
            }
        }
        $this->view('network/index', [
            'pageTitle' => 'Rede',
            'currentPage' => 'network',
            'users' => $usuarios,
            'search' => $busca
        ]);
    }
    public function connect() {
        $idUsr = $_POST['user_id'] ?? null;
        if (!$idUsr) {
            echo json_encode(['success' => false]);
            exit;
        }
        $stmt = $this->pdo->prepare("INSERT INTO user_connections (follower_id, following_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $idUsr]);
        $stmt = $this->pdo->prepare("SELECT artist_name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $nomeReq = $stmt->fetchColumn();
        $msg = "{$nomeReq} quer se conectar com você";
        $stmt = $this->pdo->prepare("INSERT INTO connection_notifications (user_id, from_user_id, message, type) VALUES (?, ?, ?, 'connection_request')");
        $stmt->execute([$idUsr, $_SESSION['user_id'], $msg]);
        echo json_encode(['success' => true]);
        exit;
    }
    public function accept() {
        $idConex = $_POST['connection_id'] ?? null;
        !$idConex && exit(json_encode(['success' => false]));
        $stmt = $this->pdo->prepare("UPDATE user_connections SET status = 'accepted' WHERE id = ? AND following_id = ?");
        $stmt->execute([$idConex, $_SESSION['user_id']]);
        echo json_encode(['success' => true]);
        exit;
    }
    public function reject() {
        $idConex = $_POST['connection_id'] ?? null;
        !$idConex && exit(json_encode(['success' => false]));
        $stmt = $this->pdo->prepare("UPDATE user_connections SET status = 'following' WHERE id = ? AND following_id = ?");
        $stmt->execute([$idConex, $_SESSION['user_id']]);
        echo json_encode(['success' => true]);
        exit;
    }
    public function acceptRequest() {
        $idUsr = $_POST['user_id'] ?? null;
        if (!$idUsr) exit(json_encode(['success' => false]));
        $stmt = $this->pdo->prepare("UPDATE user_connections SET status = 'accepted' WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$idUsr, $_SESSION['user_id']]);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user_connections WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$_SESSION['user_id'], $idUsr]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $this->pdo->prepare("INSERT INTO user_connections (follower_id, following_id, status) VALUES (?, ?, 'accepted')");
            $stmt->execute([$_SESSION['user_id'], $idUsr]);
        }
        $stmt = $this->pdo->prepare("DELETE FROM connection_notifications WHERE user_id = ? AND from_user_id = ?");
        $stmt->execute([$_SESSION['user_id'], $idUsr]);
        echo json_encode(['success' => true]);
        exit;
    }
    public function rejectRequest() {
        $idUsr = $_POST['user_id'] ?? null;
        if (!$idUsr) exit(json_encode(['success' => false]));
        $stmt = $this->pdo->prepare("DELETE FROM user_connections WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$idUsr, $_SESSION['user_id']]);
        $stmt = $this->pdo->prepare("DELETE FROM connection_notifications WHERE user_id = ? AND from_user_id = ?");
        $stmt->execute([$_SESSION['user_id'], $idUsr]);
        echo json_encode(['success' => true]);
        exit;
    }
    public function stats() {
        $idUsr = $_GET['user_id'] ?? $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user_connections WHERE following_id = ? AND status = 'accepted'");
        $stmt->execute([$idUsr]);
        $conex = $stmt->fetchColumn();
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user_connections WHERE follower_id = ? AND status = 'accepted'");
        $stmt->execute([$idUsr]);
        $conex += $stmt->fetchColumn();
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user_connections WHERE following_id = ? AND status = 'following'");
        $stmt->execute([$idUsr]);
        $seguidores = $stmt->fetchColumn();
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user_connections WHERE follower_id = ? AND status IN ('pending', 'following')");
        $stmt->execute([$idUsr]);
        $seguindo = $stmt->fetchColumn();
        echo json_encode(['connections' => $conex, 'followers' => $seguidores, 'following' => $seguindo]);
        exit;
    }
    public function list() {
        $stmt = $this->pdo->prepare("SELECT u.id, u.artist_name, u.email, u.profile_photo FROM users u INNER JOIN user_connections uc ON (u.id = uc.follower_id OR u.id = uc.following_id) WHERE (uc.follower_id = ? OR uc.following_id = ?) AND uc.status = 'accepted' AND u.id != ?");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
        $conex = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = $this->pdo->prepare("SELECT u.id, u.artist_name, u.email, u.profile_photo FROM users u INNER JOIN user_connections uc ON u.id = uc.follower_id WHERE uc.following_id = ? AND uc.status = 'following'");
        $stmt->execute([$_SESSION['user_id']]);
        $seguidores = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = $this->pdo->prepare("SELECT u.id, u.artist_name, u.email, u.profile_photo FROM users u INNER JOIN user_connections uc ON u.id = uc.following_id WHERE uc.follower_id = ? AND uc.status IN ('pending', 'following')");
        $stmt->execute([$_SESSION['user_id']]);
        $seguindo = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(['connections' => $conex, 'followers' => $seguidores, 'following' => $seguindo]);
        exit;
    }
    public function checkConnection() {
        $idUsr = $_GET['user_id'] ?? null;
        if (!$idUsr) {
            echo json_encode(['status' => 'none']);
            exit;
        }
        $stmt = $this->pdo->prepare("SELECT status FROM user_connections WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$_SESSION['user_id'], $idUsr]);
        $status = $stmt->fetchColumn();
        echo json_encode(['status' => $status ?: 'none']);
        exit;
    }
    public function remove() {
        $idUsr = $_POST['user_id'] ?? null;
        if (!$idUsr) {
            echo json_encode(['success' => false]);
            exit;
        }
        $stmt = $this->pdo->prepare("DELETE FROM user_connections WHERE (follower_id = ? AND following_id = ?) OR (follower_id = ? AND following_id = ?)");
        $stmt->execute([$_SESSION['user_id'], $idUsr, $idUsr, $_SESSION['user_id']]);
        echo json_encode(['success' => true]);
        exit;
    }
    public function viewConnections() {
        $idUsr = (int)($_GET['id'] ?? $_SESSION['user_id']);
        $tipo = $_GET['type'] ?? 'connections';
        $stmt = $this->pdo->prepare("SELECT artist_name FROM users WHERE id = ?");
        $stmt->execute([$idUsr]);
        $nomeUsr = $stmt->fetchColumn() ?: 'Usuário';
        $usuarios = [];
        if ($tipo === 'connections') {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT u.id, u.artist_name, u.email 
                FROM users u 
                INNER JOIN user_connections uc ON (u.id = uc.follower_id OR u.id = uc.following_id) 
                WHERE (uc.follower_id = ? OR uc.following_id = ?) 
                AND uc.status = 'accepted' 
                AND u.id != ?
            ");
            $stmt->execute([$idUsr, $idUsr, $idUsr]);
        } elseif ($tipo === 'followers') {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.artist_name, u.email 
                FROM users u 
                INNER JOIN user_connections uc ON u.id = uc.follower_id 
                WHERE uc.following_id = ? AND uc.status = 'following'
            ");
            $stmt->execute([$idUsr]);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.artist_name, u.email 
                FROM users u 
                INNER JOIN user_connections uc ON u.id = uc.following_id 
                WHERE uc.follower_id = ? AND uc.status IN ('pending', 'following')
            ");
            $stmt->execute([$idUsr]);
        }
        $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        parent::view('network/connections', [
            'pageTitle' => $nomeUsr,
            'currentPage' => 'network',
            'users' => $usuarios,
            'type' => $tipo,
            'userName' => $nomeUsr
        ]);
    }
}
