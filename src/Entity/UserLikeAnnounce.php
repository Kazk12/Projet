<?php

namespace App\Entity;

use App\Repository\UserLikeAnnounceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserLikeAnnounceRepository::class)]
class UserLikeAnnounce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userLikeAnnounces')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userLikeAnnounces')]
    private ?Announce $announce = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAnnounce(): ?Announce
    {
        return $this->announce;
    }

    public function setAnnounce(?Announce $announce): static
    {
        $this->announce = $announce;

        return $this;
    }
}
