<?php

namespace App\Models;

class PortfolioMedia {
    public function __construct(
        public ?int $id,
        public int $projectId,
        public string $filePath,
        public string $fileType,
        public ?string $mimeType,
        public ?int $fileSize,
        public int $displayOrder = 0
    ) {}
}
