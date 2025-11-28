<?php
namespace App\Repositories\PDO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Config\Database;
use PDO;
class PdoUserRepository implements UserRepositoryInterface {
    private PDO $db;
    public function __construct() {
        $this->db = Database::getInstance();
    }
    public function findByEmail(string $email): ?User {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();
        if ($data) {
            return new User(
                $data['id'],
                $data['artist_name'],
                $data['email'],
                $data['password'],
                (bool)$data['is_admin']
            );
        }
        return null;
    }
    public function findById(int $id): ?User {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        if ($data) {
            return new User(
                $data['id'],
                $data['artist_name'],
                $data['email'],
                $data['password'],
                (bool)$data['is_admin']
            );
        }
        return null;
    }
    public function save(User $user): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO users (artist_name, email, password, is_admin) 
             VALUES (:name, :email, :pass, :admin)"
        );
        return $stmt->execute([
            'name' => $user->artistName,
            'email' => $user->email,
            'pass' => $user->password,
            'admin' => $user->isAdmin ? 1 : 0
        ]);
    }
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];
        if (isset($data['artist_name'])) {
            $fields[] = "artist_name = :artist_name";
            $params['artist_name'] = $data['artist_name'];
        }
        if (isset($data['bio'])) {
            $fields[] = "bio = :bio";
            $params['bio'] = $data['bio'];
        }
        if (empty($fields)) return false;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function getAll(): array {
        $stmt = $this->db->query("SELECT id, artist_name, email, is_admin, created_at FROM users ORDER BY artist_name ASC");
        return $stmt->fetchAll(); 
    }
}
