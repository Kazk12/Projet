<?php

namespace App\Entity;

use App\Repository\AnnounceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AnnounceRepository::class)]
#[Vich\Uploadable]
class Announce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[Vich\UploadableField(mapping: 'announces', fileNameProperty: 'imageUrl')]
    #[Assert\Image()]
    private ?File $thumbnailFile = null;


    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $rate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'announces')]
    private ?User $user = null;

    /**
     * @var Collection<int, UserLikeAnnounce>
     */
    #[ORM\OneToMany(targetEntity: UserLikeAnnounce::class, mappedBy: 'announce')]
    private Collection $userLikeAnnounces;

    #[ORM\ManyToOne(inversedBy: 'announces')]
    private ?Book $book = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'announce')]
    private Collection $comments;

    public function __construct()
    {
        $this->userLikeAnnounces = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __serialize(): array
    {
        return [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
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

    /**
     * @return Collection<int, UserLikeAnnounce>
     */
    public function getUserLikeAnnounces(): Collection
    {
        return $this->userLikeAnnounces;
    }

    public function addUserLikeAnnounce(UserLikeAnnounce $userLikeAnnounce): static
    {
        if (!$this->userLikeAnnounces->contains($userLikeAnnounce)) {
            $this->userLikeAnnounces->add($userLikeAnnounce);
            $userLikeAnnounce->setAnnounce($this);
        }

        return $this;
    }

    public function removeUserLikeAnnounce(UserLikeAnnounce $userLikeAnnounce): static
    {
        if ($this->userLikeAnnounces->removeElement($userLikeAnnounce)) {
            // set the owning side to null (unless already changed)
            if ($userLikeAnnounce->getAnnounce() === $this) {
                $userLikeAnnounce->setAnnounce(null);
            }
        }

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAnnounce($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAnnounce() === $this) {
                $comment->setAnnounce(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of thumbnailFile
     */ 
    public function getThumbnailFile() : ?File
    {
        return $this->thumbnailFile;
    }

    /**
     * Set the value of thumbnailFile
     *
     * @return  self
     */ 
    public function setThumbnailFile(?File $thumbnailFile) : void
    {
        $this->thumbnailFile = $thumbnailFile;

       if(null !== $thumbnailFile){
           $this->updatedAt = new \DateTimeImmutable();
       }
    }

}
