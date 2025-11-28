<?php
namespace App\Controllers;
class MessageController extends AuthController {
    private $pdo;
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->pdo = new \PDO("mysql:host=localhost;dbname=artsync_db;charset=utf8mb4", 'root', '');
    }
    public function index() {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT 
                CASE 
                    WHEN sender_id = ? THEN receiver_id 
                    ELSE sender_id 
                END as contact_id,
                u.artist_name,
                (SELECT message FROM direct_messages 
                 WHERE (sender_id = ? AND receiver_id = contact_id) 
                    OR (sender_id = contact_id AND receiver_id = ?)
                 ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT created_at FROM direct_messages 
                 WHERE (sender_id = ? AND receiver_id = contact_id) 
                    OR (sender_id = contact_id AND receiver_id = ?)
                 ORDER BY created_at DESC LIMIT 1) as last_time,
                (SELECT COUNT(*) FROM direct_messages 
                 WHERE sender_id = contact_id AND receiver_id = ? AND is_read = 0) as unread_count
            FROM direct_messages dm
            JOIN users u ON u.id = CASE 
                WHEN dm.sender_id = ? THEN dm.receiver_id 
                ELSE dm.sender_id 
            END
            WHERE sender_id = ? OR receiver_id = ?
            ORDER BY last_time DESC
        ");
        $uid = $_SESSION['user_id'];
        $stmt->execute([$uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid]);
        $conversations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        parent::view('messages/index', [
            'pageTitle' => 'Mensagens',
            'currentPage' => 'messages',
            'conversations' => $conversations
        ]);
    }
    public function chat() {
        $contactId = $_GET['id'] ?? null;
        if (!$contactId) {
            header('Location: /messages');
            exit;
        }
        $stmt = $this->pdo->prepare("SELECT id, artist_name, message_privacy FROM users WHERE id = ?");
        $stmt->execute([$contactId]);
        $contact = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$contact) {
            header('Location: /messages');
            exit;
        }
        $privacy = $contact['message_privacy'] ?? 'anyone';
        if ($privacy === 'none') {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Este usuário não aceita mensagens'];
            header('Location: /messages');
            exit;
        }
        if ($privacy === 'connections') {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user_connections WHERE ((follower_id = ? AND following_id = ?) OR (follower_id = ? AND following_id = ?)) AND status = 'accepted'");
            $stmt->execute([$_SESSION['user_id'], $contactId, $contactId, $_SESSION['user_id']]);
            $isConnected = $stmt->fetchColumn() > 0;
            if (!$isConnected) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Você precisa estar conectado para enviar mensagens'];
                header('Location: /messages');
                exit;
            }
        }
        $stmt = $this->pdo->prepare("UPDATE direct_messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
        $stmt->execute([$contactId, $_SESSION['user_id']]);
        $stmt = $this->pdo->prepare("
            SELECT dm.*, 
                   s.artist_name as sender_name,
                   r.artist_name as receiver_name
            FROM direct_messages dm
            JOIN users s ON s.id = dm.sender_id
            JOIN users r ON r.id = dm.receiver_id
            WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
            ORDER BY created_at ASC
        ");
        $stmt->execute([$_SESSION['user_id'], $contactId, $contactId, $_SESSION['user_id']]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        parent::view('messages/chat', [
            'pageTitle' => 'Chat - ' . $contact['artist_name'],
            'currentPage' => 'messages',
            'contact' => $contact,
            'messages' => $messages
        ]);
    }
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }
        $receiverId = $_POST['receiver_id'] ?? null;
        $message = trim($_POST['message'] ?? '');
        $fileUrl = null;
        if (!$receiverId) {
            echo json_encode(['success' => false, 'error' => 'Missing data']);
            exit;
        }
        if (!$message && (empty($_FILES['file']) || $_FILES['file']['error'] !== 0)) {
            echo json_encode(['success' => false, 'error' => 'Missing data']);
            exit;
        }
        $stmt = $this->pdo->prepare("SELECT message_privacy FROM users WHERE id = ?");
        $stmt->execute([$receiverId]);
        $privacy = $stmt->fetchColumn() ?: 'anyone';
        if ($privacy === 'none') {
            echo json_encode(['success' => false, 'error' => 'Este usuário não aceita mensagens']);
            exit;
        }
        if ($privacy === 'connections') {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user_connections WHERE ((follower_id = ? AND following_id = ?) OR (follower_id = ? AND following_id = ?)) AND status = 'accepted'");
            $stmt->execute([$_SESSION['user_id'], $receiverId, $receiverId, $_SESSION['user_id']]);
            $isConnected = $stmt->fetchColumn() > 0;
            if (!$isConnected) {
                echo json_encode(['success' => false, 'error' => 'Você precisa estar conectado para enviar mensagens']);
                exit;
            }
        }
        if (!empty($_FILES['file']) && $_FILES['file']['error'] === 0) {
            $file = $_FILES['file'];
            $uploadDir = __DIR__ . '/../../public/uploads/messages/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $allowedExts = ['jpg','jpeg','png','gif','webp','mp3','wav','ogg','webm','mp4','mov','avi','flp','als','ptx','logic','rpp','zip','rar'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExts) && $file['size'] <= 50000000) {
                $filename = uniqid() . '_' . time() . '.' . $ext;
                $uploadPath = $uploadDir . $filename;
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $fileUrl = '/uploads/messages/' . $filename;
                }
            }
        }
        $finalMessage = $message ?: '';
        if ($fileUrl) {
            $finalMessage = trim($finalMessage . ' ' . $fileUrl);
        }
        $stmt = $this->pdo->prepare("INSERT INTO direct_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $receiverId, $finalMessage]);
        echo json_encode(['success' => true]);
        exit;
    }
    public function getUnreadCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM direct_messages WHERE receiver_id = ? AND is_read = 0");
        $stmt->execute([$_SESSION['user_id']]);
        $count = $stmt->fetchColumn();
        echo json_encode(['count' => $count]);
        exit;
    }
    public function getConversations() {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT 
                CASE 
                    WHEN sender_id = ? THEN receiver_id 
                    ELSE sender_id 
                END as contact_id,
                u.artist_name,
                (SELECT message FROM direct_messages 
                 WHERE (sender_id = ? AND receiver_id = contact_id) 
                    OR (sender_id = contact_id AND receiver_id = ?)
                 ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT created_at FROM direct_messages 
                 WHERE (sender_id = ? AND receiver_id = contact_id) 
                    OR (sender_id = contact_id AND receiver_id = ?)
                 ORDER BY created_at DESC LIMIT 1) as last_time,
                (SELECT COUNT(*) FROM direct_messages 
                 WHERE sender_id = contact_id AND receiver_id = ? AND is_read = 0) as unread_count
            FROM direct_messages dm
            JOIN users u ON u.id = CASE 
                WHEN dm.sender_id = ? THEN dm.receiver_id 
                ELSE dm.sender_id 
            END
            WHERE sender_id = ? OR receiver_id = ?
            ORDER BY last_time DESC
        ");
        $uid = $_SESSION['user_id'];
        $stmt->execute([$uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid, $uid]);
        $conversations = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(['conversations' => $conversations]);
        exit;
    }
    public function getChat() {
        $contactId = $_GET['id'] ?? null;
        if (!$contactId) {
            echo json_encode(['messages' => []]);
            exit;
        }
        $stmt = $this->pdo->prepare("UPDATE direct_messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
        $stmt->execute([$contactId, $_SESSION['user_id']]);
        $stmt = $this->pdo->prepare("
            SELECT message, created_at, sender_id = ? as is_sent
            FROM direct_messages
            WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
            ORDER BY created_at ASC
        ");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $contactId, $contactId, $_SESSION['user_id']]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(['messages' => $messages]);
        exit;
    }
}
