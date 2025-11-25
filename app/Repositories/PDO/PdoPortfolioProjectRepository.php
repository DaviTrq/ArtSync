<?php

namespace App\Repositories\PDO;

use App\Models\PortfolioProject;
use App\Models\PortfolioMedia;
use Config\Database;
use PDO;

class PdoPortfolioProjectRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM portfolio_projects 
            WHERE user_id = :uid 
            ORDER BY created_at DESC
        ");
        $stmt->execute([':uid' => $userId]);
        
        $projects = [];
        while ($row = $stmt->fetch()) {
            $project = new PortfolioProject(
                $row['id'],
                $row['user_id'],
                $row['title'],
                $row['description'],
                $row['slug'],
                (bool)$row['is_public']
            );
            $project->media = $this->getMediaByProjectId($row['id']);
            $projects[] = $project;
        }
        return $projects;
    }

    public function getBySlug(string $slug): ?PortfolioProject {
        $stmt = $this->pdo->prepare("
            SELECT * FROM portfolio_projects 
            WHERE slug = :slug AND is_public = 1
        ");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        
        if (!$row) return null;
        
        $project = new PortfolioProject(
            $row['id'],
            $row['user_id'],
            $row['title'],
            $row['description'],
            $row['slug'],
            (bool)$row['is_public']
        );
        $project->media = $this->getMediaByProjectId($row['id']);
        return $project;
    }

    public function save(PortfolioProject $project): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO portfolio_projects (user_id, title, description, slug, is_public)
            VALUES (:user_id, :title, :description, :slug, :is_public)
        ");
        $stmt->execute([
            ':user_id' => $project->userId,
            ':title' => $project->title,
            ':description' => $project->description,
            ':slug' => $project->slug,
            ':is_public' => $project->isPublic ? 1 : 0
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function addMedia(int $projectId, string $filePath, string $fileType, string $mimeType, int $fileSize): void {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(MAX(display_order), 0) + 1 as next_order 
            FROM portfolio_media WHERE project_id = :pid
        ");
        $stmt->execute([':pid' => $projectId]);
        $order = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("
            INSERT INTO portfolio_media (project_id, file_path, file_type, mime_type, file_size, display_order)
            VALUES (:pid, :path, :type, :mime, :size, :order)
        ");
        $stmt->execute([
            ':pid' => $projectId,
            ':path' => $filePath,
            ':type' => $fileType,
            ':mime' => $mimeType,
            ':size' => $fileSize,
            ':order' => $order
        ]);
    }

    public function getMediaByProjectId(int $projectId): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM portfolio_media 
            WHERE project_id = :pid 
            ORDER BY display_order ASC
        ");
        $stmt->execute([':pid' => $projectId]);
        
        $media = [];
        while ($row = $stmt->fetch()) {
            $media[] = new PortfolioMedia(
                $row['id'],
                $row['project_id'],
                $row['file_path'],
                $row['file_type'],
                $row['mime_type'],
                $row['file_size'],
                $row['display_order']
            );
        }
        return $media;
    }

    public function delete(int $projectId, int $userId): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM portfolio_projects 
            WHERE id = :id AND user_id = :uid
        ");
        return $stmt->execute([':id' => $projectId, ':uid' => $userId]);
    }

    public function deleteMedia(int $mediaId, int $userId): bool {
        $stmt = $this->pdo->prepare("
            DELETE pm FROM portfolio_media pm
            INNER JOIN portfolio_projects pp ON pm.project_id = pp.id
            WHERE pm.id = :mid AND pp.user_id = :uid
        ");
        return $stmt->execute([':mid' => $mediaId, ':uid' => $userId]);
    }
}
