<?php
namespace App\DTO;

class AnnounceMine
{
    public function __construct(
        private int $id,
        private string $imageUrl,
        private int $rate,
        private string $content,
        private \DateTimeImmutable $createdAt,
    )
    {
        
    }
}