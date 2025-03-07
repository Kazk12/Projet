<?php

namespace App\DTO;

use App\Entity\Comment;
use App\Entity\User;

class AnnounceLike
{
    
    public function __construct(
        public int $id,
        public ?int $rate,
        public ?string $content,
        public string $userPseudo,
        public int $likeCount,
        public string $bookTitle,
        public ?string $thumbnailFile,
        public array $comments,
        public User $user
    ) {}
}