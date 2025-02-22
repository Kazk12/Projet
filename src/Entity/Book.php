<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, TypeBook>
     */
    #[ORM\OneToMany(targetEntity: TypeBook::class, mappedBy: 'book')]
    private Collection $typeBooks;

    /**
     * @var Collection<int, Announce>
     */
    #[ORM\OneToMany(targetEntity: Announce::class, mappedBy: 'book')]
    private Collection $announces;

    public function __construct()
    {
        $this->typeBooks = new ArrayCollection();
        $this->announces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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
            $typeBook->setBook($this);
        }

        return $this;
    }

    public function removeTypeBook(TypeBook $typeBook): static
    {
        if ($this->typeBooks->removeElement($typeBook)) {
            // set the owning side to null (unless already changed)
            if ($typeBook->getBook() === $this) {
                $typeBook->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Announce>
     */
    public function getAnnounces(): Collection
    {
        return $this->announces;
    }

    public function addAnnounce(Announce $announce): static
    {
        if (!$this->announces->contains($announce)) {
            $this->announces->add($announce);
            $announce->setBook($this);
        }

        return $this;
    }

    public function removeAnnounce(Announce $announce): static
    {
        if ($this->announces->removeElement($announce)) {
            // set the owning side to null (unless already changed)
            if ($announce->getBook() === $this) {
                $announce->setBook(null);
            }
        }

        return $this;
    }
}
