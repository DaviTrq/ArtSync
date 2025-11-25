<?php

namespace App\Models;

class ForumComment {
    public $id;
    public $topicId;
    public $topic_id;
    public $userId;
    public $user_id;
    public $content;
    public $createdAt;
    public $created_at;
    public $authorName;
    public $attachments = [];
}
