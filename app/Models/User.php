<?php
namespace App\Models;
class User {
    public function __construct(
        public ?int $id,
        public string $artistName,
        public string $email,
        public string $password,
        public bool $isAdmin = false
    ) {}
}
