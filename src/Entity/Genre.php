<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, TypeBook>
     */
    #[ORM\OneToMany(targetEntity: TypeBook::class, mappedBy: 'genre')]
    private Collection $typeBooks;

    /**
     * @var Collection<int, UserLikeGenre>
     */
    #[ORM\OneToMany(targetEntity: UserLikeGenre::class, mappedBy: 'genre')]
    private Collection $userLikeGenres;

    public function __construct()
    {
        $this->typeBooks = new ArrayCollection();
        $this->userLikeGenres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, TypeBook>
     */
    public function getTypeBooks(): Collection
    {
        return $this->typeBooks;
    }

    public function addTypeBook(TypeBook $typeBook): static
    {
        if (!$this->typeBooks->contains($typeBook)) {
            $this->typeBooks->add($typeBook);
            $typeBook->setGenre($this);
        }

        return $this;
    }

    public function removeTypeBook(TypeBook $typeBook): static
    {
        if ($this->typeBooks->removeElement($typeBook)) {
            // set the owning side to null (unless already changed)
            if ($typeBook->getGenre() === $this) {
                $typeBook->setGenre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserLikeGenre>
     */
    public function getUserLikeGenres(): Collection
    {
        return $this->userLikeGenres;
    }

    public function addUserLikeGenre(UserLikeGenre $userLikeGenre): static
    {
        if (!$this->userLikeGenres->contains($userLikeGenre)) {
            $this->userLikeGenres->add($userLikeGenre);
            $userLikeGenre->setGenre($this);
        }

        return $this;
    }

    public function removeUserLikeGenre(UserLikeGenre $userLikeGenre): static
    {
        if ($this->userLikeGenres->removeElement($userLikeGenre)) {
            // set the owning side to null (unless already changed)
            if ($userLikeGenre->getGenre() === $this) {
                $userLikeGenre->setGenre(null);
            }
        }

        return $this;
    }
}
