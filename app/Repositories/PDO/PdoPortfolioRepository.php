<?php
namespace App\Repositories\PDO;
use PDO;
use PDOException;
use App\Models\PortfolioItem;
class PdoPortfolioRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getByUserId(int $userId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, user_id, title, file_path, description, created_at
                FROM portfolio_items
                WHERE user_id = :uid
                ORDER BY created_at DESC
            ");
            $stmt->execute([':uid' => $userId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($r) {
                return new PortfolioItem(
                    (int)($r['id'] ?? 0),
                    (int)($r['user_id'] ?? 0),
                    (string)($r['title'] ?? ''),
                    (string)($r['file_path'] ?? ''),
                    (string)($r['description'] ?? '')
                );
            }, $rows);
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar itens do portfólio: " . $e->getMessage());
        }
    }
    public function save(int $userId, string $title, string $filePath, ?string $description = null): int
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO portfolio_items (user_id, title, file_path, description)
                VALUES (:user_id, :title, :file_path, :description)
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':title' => $title,
                ':file_path' => $filePath,
                ':description' => $description
            ]);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao salvar item no portfólio: " . $e->getMessage());
        }
    }
    public function delete(int $id, int $userId): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM portfolio_items
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([':id' => $id, ':user_id' => $userId]);
        } catch (PDOException $e) {
            throw new PDOException("Erro ao excluir item do portfólio: " . $e->getMessage());
        }
    }
    public function find(int $id, int $userId): ?PortfolioItem
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, user_id, title, file_path, description
                FROM portfolio_items
                WHERE id = :id AND user_id = :user_id
                LIMIT 1
            ");
            $stmt->execute([':id' => $id, ':user_id' => $userId]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$r) return null;
            return new PortfolioItem(
                (int)($r['id'] ?? 0),
                (int)($r['user_id'] ?? 0),
                (string)($r['title'] ?? ''),
                (string)($r['file_path'] ?? ''),
                (string)($r['description'] ?? '')
            );
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar item do portfólio: " . $e->getMessage());
        }
    }
}
