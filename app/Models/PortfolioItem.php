<?php
namespace App\Models;
class PortfolioItem
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $title,
        public string $filePath,
        public ?string $description = null
    ) {}
}
