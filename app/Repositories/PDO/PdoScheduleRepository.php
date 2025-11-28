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
        $stmt = $this->db->prepare("SELECT * FROM schedule_events WHERE user_id = :uid ORDER BY event_date ASC");
        $stmt->execute(['uid' => $userId]);
        $events = [];
        while ($row = $stmt->fetch()) {
            $events[] = new ScheduleEvent(
                $row['id'],
                $row['user_id'],
                $row['title'],
                $row['event_date'],
                $row['description'] ?? '',
                $row['color'] ?? '#4CAF50',
                $row['location'] ?? '',
                'low'
            );
        }
        return $events;
    }
    public function save(ScheduleEvent $event): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO schedule_events (user_id, title, event_date, description, color, location) 
             VALUES (:uid, :title, :date, :notes, :color, :location)"
        );
        return $stmt->execute([
            'uid' => $event->userId,
            'title' => $event->title,
            'date' => $event->eventDate,
            'notes' => $event->notes,
            'color' => $event->color ?? '#4CAF50',
            'location' => $event->location ?? ''
        ]);
    }
    public function update(ScheduleEvent $event): bool {
        $stmt = $this->db->prepare(
            "UPDATE schedule_events SET title = :title, event_date = :date, description = :notes, color = :color, location = :location 
             WHERE id = :id AND user_id = :uid"
        );
        return $stmt->execute([
            'id' => $event->id,
            'uid' => $event->userId,
            'title' => $event->title,
            'date' => $event->eventDate,
            'notes' => $event->notes,
            'color' => $event->color ?? '#4CAF50',
            'location' => $event->location ?? ''
        ]);
    }
    public function delete(int $id, int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM schedule_events WHERE id = :id AND user_id = :uid");
        return $stmt->execute(['id' => $id, 'uid' => $userId]);
    }
}
