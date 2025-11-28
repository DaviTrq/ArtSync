<?php
namespace App\Repositories\PDO;
use App\Models\ScheduleEvent;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use Config\Database;
use PDO;
class PdoScheduleRepository implements ScheduleRepositoryInterface {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance();
    }
    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM schedule WHERE user_id = :uid ORDER BY event_date ASC");
        $stmt->execute(['uid' => $userId]);
        $events = [];
        while ($row = $stmt->fetch()) {
            $events[] = new ScheduleEvent(
                $row['id'],
                $row['user_id'],
                $row['event_title'],
                $row['event_date'],
                $row['notes'],
                $row['color'] ?? '#4CAF50',
                $row['location'] ?? '',
                $row['priority'] ?? 'low'
            );
        }
        return $events;
    }
    public function save(ScheduleEvent $event): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO schedule (user_id, event_title, event_date, notes, color, location, priority) 
             VALUES (:uid, :title, :date, :notes, :color, :location, :priority)"
        );
        return $stmt->execute([
            'uid' => $event->userId,
            'title' => $event->title,
            'date' => $event->eventDate,
            'notes' => $event->notes,
            'color' => $event->color ?? '#4CAF50',
            'location' => $event->location ?? '',
            'priority' => $event->priority ?? 'low'
        ]);
    }
    public function update(ScheduleEvent $event): bool {
        $stmt = $this->db->prepare(
            "UPDATE schedule SET event_title = :title, event_date = :date, notes = :notes, color = :color, location = :location, priority = :priority 
             WHERE id = :id AND user_id = :uid"
        );
        return $stmt->execute([
            'id' => $event->id,
            'uid' => $event->userId,
            'title' => $event->title,
            'date' => $event->eventDate,
            'notes' => $event->notes,
            'color' => $event->color ?? '#4CAF50',
            'location' => $event->location ?? '',
            'priority' => $event->priority ?? 'low'
        ]);
    }
    public function delete(int $id, int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM schedule WHERE id = :id AND user_id = :uid");
        return $stmt->execute(['id' => $id, 'uid' => $userId]);
    }
}
