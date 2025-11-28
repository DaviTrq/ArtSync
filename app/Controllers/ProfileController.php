<?php
namespace App\Controllers;
class ProfileController extends AuthController {
    private $pdo;
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->pdo = new \PDO("mysql:host=localhost;dbname=artsync_db;charset=utf8mb4", 'root', '');
    }
    public function viewProfile() {
        $idUsr = $_GET['id'] ?? $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("SELECT id, artist_name, email, bio, created_at FROM users WHERE id = ?");
        $stmt->execute([$idUsr]);
        $usr = $stmt->fetch(\PDO::FETCH_ASSOC);
        !$usr && header('Location: /dashboard') && exit;
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
        $stmt = $this->pdo->prepare("SELECT id, title, created_at FROM forum_topics WHERE user_id = ? AND is_approved = 1 ORDER BY created_at DESC LIMIT 10");
        $stmt->execute([$idUsr]);
        $topicos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = $this->pdo->prepare("SELECT c.id, c.content, c.created_at, t.id as topic_id, t.title as topic_title FROM forum_comments c JOIN forum_topics t ON c.topic_id = t.id WHERE c.user_id = ? ORDER BY c.created_at DESC LIMIT 10");
        $stmt->execute([$idUsr]);
        $coments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        parent::view('profile/view', [
            'pageTitle' => $usr['artist_name'],
            'currentPage' => 'profile',
            'user' => $usr,
            'userId' => $idUsr,
            'connections' => $conex,
            'followers' => $seguidores,
            'following' => $seguindo,
            'topics' => $topicos,
            'comments' => $coments
        ]);
    }
    public function edit() {
        $stmt = $this->pdo->prepare("SELECT id, artist_name, email, bio FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $usr = $stmt->fetch(\PDO::FETCH_ASSOC);
        parent::view('profile/edit', [
            'pageTitle' => 'Editar Perfil',
            'currentPage' => 'profile',
            'user' => $usr
        ]);
    }
    public function update() {
        $artistName = $_POST['artist_name'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $stmt = $this->pdo->prepare("UPDATE users SET artist_name = ?, bio = ? WHERE id = ?");
        $stmt->execute([$artistName, $bio, $_SESSION['user_id']]);
        header('Location: /profile/view');
        exit;
    }
}
