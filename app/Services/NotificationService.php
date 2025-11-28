<?php
namespace App\Services;
use PDO;
class NotificationService {
    private $pdo;
    public function __construct() {
        $this->pdo = new PDO("mysql:host=localhost;dbname=artsync_db;charset=utf8mb4", 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function generateNotifications($idUsr) {
        $stmt = $this->pdo->prepare("SELECT id, title, event_date, event_time FROM schedule_events WHERE user_id = ? AND enable_notification = 1 AND event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)");
        $stmt->execute([$idUsr]);
        $evts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($evts as $evt) {
            $stmt = $this->pdo->prepare("SELECT id FROM notifications WHERE user_id = ? AND event_id = ?");
            $stmt->execute([$idUsr, $evt['id']]);
            if (!$stmt->fetch()) {
                $dias = (strtotime($evt['event_date']) - strtotime(date('Y-m-d'))) / 86400;
                $msg = $dias == 0 ? "Hoje: {$evt['title']} às " . date('H:i', strtotime($evt['event_time'])) : ($dias == 1 ? "Amanhã: {$evt['title']} às " . date('H:i', strtotime($evt['event_time'])) : "Em " . (int)$dias . " dias: {$evt['title']}");
                $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, event_id, message) VALUES (?, ?, ?)");
                $stmt->execute([$idUsr, $evt['id'], $msg]);
            }
        }
    }
    public function getUnreadNotifications($idUsr) {
        $stmt = $this->pdo->prepare("SELECT n.*, e.event_date, e.event_time FROM notifications n JOIN schedule_events e ON n.event_id = e.id WHERE n.user_id = ? AND n.is_read = 0 ORDER BY e.event_date ASC, e.event_time ASC");
        $stmt->execute([$idUsr]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUnreadCount($idUsr) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$idUsr]);
        return $stmt->fetchColumn();
    }
    public function markAsRead($idNotif) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$idNotif]);
    }
    public function markAllAsRead($idUsr) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$idUsr]);
    }
    public function notifyAdminsNewTopic($idTopico, $titulo, $autor) {
        $stmt = $this->pdo->query("SELECT id FROM users WHERE is_admin = 1");
        $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $msg = "Novo tópico para aprovar: '{$titulo}' por {$autor}";
        foreach ($admins as $idAdmin) {
            $stmt = $this->pdo->prepare("INSERT INTO admin_notifications (user_id, topic_id, message, type) VALUES (?, ?, ?, 'topic_approval')");
            $stmt->execute([$idAdmin, $idTopico, $msg]);
        }
    }
    public function getAdminNotifications($idUsr) {
        $stmt = $this->pdo->prepare("SELECT n.*, t.title as topic_title FROM admin_notifications n LEFT JOIN forum_topics t ON n.topic_id = t.id WHERE n.user_id = ? AND n.is_read = 0 ORDER BY n.created_at DESC");
        $stmt->execute([$idUsr]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAdminNotificationCount($idUsr) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM admin_notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$idUsr]);
        return $stmt->fetchColumn();
    }
    public function markAdminNotificationRead($idNotif) {
        $stmt = $this->pdo->prepare("UPDATE admin_notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$idNotif]);
    }
    public function getConnectionNotifications($idUsr) {
        $stmt = $this->pdo->prepare("SELECT cn.*, u.artist_name as from_user_name FROM connection_notifications cn JOIN users u ON cn.from_user_id = u.id WHERE cn.user_id = ? AND cn.is_read = 0 ORDER BY cn.created_at DESC");
        $stmt->execute([$idUsr]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function markConnectionNotificationRead($idNotif) {
        $stmt = $this->pdo->prepare("UPDATE connection_notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$idNotif]);
    }
}
