<?php


namespace App\DTO;

class SearchCriteria
{
    private ?string $query = null;
    private ?string $genre = null;
    private ?int $userId = null;

    public function __construct(?string $query = null, ?string $genre = null, ?int $userId = null)
    {
        $this->query = $query;
        $this->genre = $genre;
        $this->userId = $userId;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setQuery(?string $query): self
    {
        $this->query = $query;
        
        return $this;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;
        
        return $this;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        
        return $this;
    }
}