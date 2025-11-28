<?php
namespace App\Models;
class PortfolioProject {
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $title,
        public ?string $description,
        public string $slug,
        public bool $isPublic = true,
        public array $media = [],
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}
}
