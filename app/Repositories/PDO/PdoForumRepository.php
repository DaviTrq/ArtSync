<?php

namespace App\Repositories\PDO;

use App\Repositories\Contracts\ForumRepositoryInterface;
use App\Models\ForumTopic;
use App\Models\ForumComment;
use PDO;

class PdoForumRepository implements ForumRepositoryInterface
{
    private $pdo;

    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'artsync_db';
        $user = 'root';
        $pass = '';
        $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllTopics()
    {
        $stmt = $this->pdo->query("
            SELECT t.*, u.artist_name, 
            (SELECT COUNT(*) FROM forum_comments WHERE topic_id = t.id) as comment_count
            FROM forum_topics t
            JOIN users u ON t.user_id = u.id
            ORDER BY t.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_CLASS, ForumTopic::class);
    }

    public function getApprovedTopics()
    {
        $stmt = $this->pdo->query("
            SELECT t.*, u.artist_name as authorName,
            (SELECT COUNT(*) FROM forum_comments WHERE topic_id = t.id) as commentCount
            FROM forum_topics t
            JOIN users u ON t.user_id = u.id
            WHERE t.is_approved = 1
            ORDER BY t.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_CLASS, ForumTopic::class);
    }

    public function getTopicById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT t.*, u.artist_name as authorName
            FROM forum_topics t
            JOIN users u ON t.user_id = u.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) return null;
        
        $topic = new ForumTopic();
        $topic->id = $data['id'];
        $topic->userId = $data['user_id'];
        $topic->title = $data['title'];
        $topic->content = $data['content'];
        $topic->isApproved = $data['is_approved'];
        $topic->is_approved = $data['is_approved'];
        $topic->createdAt = $data['created_at'];
        $topic->created_at = $data['created_at'];
        $topic->updatedAt = $data['updated_at'];
        $topic->updated_at = $data['updated_at'];
        $topic->authorName = $data['authorName'];
        
        $stmt = $this->pdo->prepare("SELECT * FROM forum_attachments WHERE topic_id = ?");
        $stmt->execute([$id]);
        $topic->attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $topic;
    }

    public function createTopic($userId, $title, $content)
    {
        $stmt = $this->pdo->prepare("INSERT INTO forum_topics (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $title, $content]);
        return $this->pdo->lastInsertId();
    }

    public function createTopicAttachment($topicId, $filePath, $fileType)
    {
        $stmt = $this->pdo->prepare("INSERT INTO forum_attachments (topic_id, file_path, file_type) VALUES (?, ?, ?)");
        return $stmt->execute([$topicId, $filePath, $fileType]);
    }

    public function approveTopic($id)
    {
        $stmt = $this->pdo->prepare("UPDATE forum_topics SET is_approved = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCommentsByTopicId($topicId)
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.artist_name as authorName
            FROM forum_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.topic_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$topicId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $comments = [];
        foreach ($data as $row) {
            $comment = new ForumComment();
            $comment->id = $row['id'];
            $comment->topicId = $row['topic_id'];
            $comment->topic_id = $row['topic_id'];
            $comment->userId = $row['user_id'];
            $comment->user_id = $row['user_id'];
            $comment->content = $row['content'];
            $comment->createdAt = $row['created_at'];
            $comment->created_at = $row['created_at'];
            $comment->authorName = $row['authorName'];
            
            $stmt = $this->pdo->prepare("SELECT * FROM forum_attachments WHERE comment_id = ?");
            $stmt->execute([$comment->id]);
            $comment->attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $comments[] = $comment;
        }
        
        return $comments;
    }

    public function createComment($topicId, $userId, $content)
    {
        $stmt = $this->pdo->prepare("INSERT INTO forum_comments (topic_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$topicId, $userId, $content]);
        return $this->pdo->lastInsertId();
    }

    public function createAttachment($commentId, $filePath, $fileType)
    {
        $stmt = $this->pdo->prepare("INSERT INTO forum_attachments (comment_id, file_path, file_type) VALUES (?, ?, ?)");
        return $stmt->execute([$commentId, $filePath, $fileType]);
    }
}
