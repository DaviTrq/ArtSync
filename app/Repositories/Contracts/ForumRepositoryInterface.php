<?php
namespace App\Repositories\Contracts;
interface ForumRepositoryInterface
{
    public function getAllTopics();
    public function getApprovedTopics();
    public function getTopicById($id);
    public function createTopic($userId, $title, $content);
    public function approveTopic($id);
    public function getCommentsByTopicId($topicId);
    public function createComment($topicId, $userId, $content);
    public function createAttachment($commentId, $filePath, $fileType);
}
