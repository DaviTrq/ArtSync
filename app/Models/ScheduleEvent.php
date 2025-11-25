<?php

namespace App\Models;

class ScheduleEvent {
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $title,
        public string $eventDate,
        public ?string $notes,
        public string $color = '#4CAF50',
        public string $location = ''
    ) {}
}
