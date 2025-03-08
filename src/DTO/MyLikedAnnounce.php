<?php

namespace App\DTO;

/**
 * DTO pour représenter une annonce likée par l'utilisateur
 */
class MyLikedAnnounce
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $imageUrl,
        public readonly string $bookTitle,
        public readonly string $bookAuthor,
        public readonly ?string $content,
        public readonly ?int $rate,
        public readonly string $userPseudo,
        public readonly int $announceId 
    ) {
    }
}