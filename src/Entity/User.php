<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    #[ORM\Column]
    private bool $isVerified = false;

    

    /**
     * @var Collection<int, Statut>
     */
    #[ORM\OneToMany(targetEntity: Statut::class, mappedBy: 'user')]
    private Collection $statuts;

    /**
     * @var Collection<int, Statut>
     */
    #[ORM\OneToMany(targetEntity: Statut::class, mappedBy: 'otherUser')]
    private Collection $statutsOther;

    /**
     * @var Collection<int, Announce>
     */
    #[ORM\OneToMany(targetEntity: Announce::class, mappedBy: 'user')]
    private Collection $announces;

    /**
     * @var Collection<int, UserLikeAnnounce>
     */
    #[ORM\OneToMany(targetEntity: UserLikeAnnounce::class, mappedBy: 'user')]
    private Collection $userLikeAnnounces;

    /**
     * @var Collection<int, UserLikeGenre>
     */
    #[ORM\OneToMany(targetEntity: UserLikeGenre::class, mappedBy: 'user')]
    private Collection $userLikeGenres;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user')]
    private Collection $comments;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'users')]
    private Collection $conversations;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'author')]
    private Collection $messages;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[Vich\UploadableField(mapping: 'profilePicture', fileNameProperty: 'profilePicture')]
    #[Assert\Image()]
    private ?File $photoFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;


    public function __construct()
    {
        
        $this->statuts = new ArrayCollection();
        $this->statutsOther = new ArrayCollection();
        $this->announces = new ArrayCollection();
        $this->userLikeAnnounces = new ArrayCollection();
        $this->userLikeGenres = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function __serialize(): array
{
    return [
        'id' => $this->id,
        'email' => $this->email,
        'password' => $this->password,
        'pseudo' => $this->pseudo,
        'roles' => $this->roles,
        'isVerified' => $this->isVerified,
        'updatedAt' => $this->updatedAt,
    ];
}
    //    public function __unserialize(array $data): void
    // {
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Statut>
     */
    public function getStatuts(): Collection
    {
        return $this->statuts;
    }

    public function addStatut(Statut $statut): static
    {
        if (!$this->statuts->contains($statut)) {
            $this->statuts->add($statut);
            $statut->setUser($this);
        }

        return $this;
    }

    public function removeStatut(Statut $statut): static
    {
        if ($this->statuts->removeElement($statut)) {
            // set the owning side to null (unless already changed)
            if ($statut->getUser() === $this) {
                $statut->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Statut>
     */
    public function getStatutsOther(): Collection
    {
        return $this->statutsOther;
    }

    public function addStatutsOther(Statut $statutsOther): static
    {
        if (!$this->statutsOther->contains($statutsOther)) {
            $this->statutsOther->add($statutsOther);
            $statutsOther->setOtherUser($this);
        }

        return $this;
    }

    public function removeStatutsOther(Statut $statutsOther): static
    {
        if ($this->statutsOther->removeElement($statutsOther)) {
            // set the owning side to null (unless already changed)
            if ($statutsOther->getOtherUser() === $this) {
                $statutsOther->setOtherUser(null);
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
            $announce->setUser($this);
        }

        return $this;
    }

    public function removeAnnounce(Announce $announce): static
    {
        if ($this->announces->removeElement($announce)) {
            // set the owning side to null (unless already changed)
            if ($announce->getUser() === $this) {
                $announce->setUser(null);
            }
        }

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
            $userLikeAnnounce->setUser($this);
        }

        return $this;
    }

    public function removeUserLikeAnnounce(UserLikeAnnounce $userLikeAnnounce): static
    {
        if ($this->userLikeAnnounces->removeElement($userLikeAnnounce)) {
            // set the owning side to null (unless already changed)
            if ($userLikeAnnounce->getUser() === $this) {
                $userLikeAnnounce->setUser(null);
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
            $userLikeGenre->setUser($this);
        }

        return $this;
    }

    public function removeUserLikeGenre(UserLikeGenre $userLikeGenre): static
    {
        if ($this->userLikeGenres->removeElement($userLikeGenre)) {
            // set the owning side to null (unless already changed)
            if ($userLikeGenre->getUser() === $this) {
                $userLikeGenre->setUser(null);
            }
        }

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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): static
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
            $conversation->addUser($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): static
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * Get the value of photoFile
     */ 
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * Set the value of photoFile
     *
     * @return  self
     */ 
    public function setPhotoFile($photoFile)
    {
        $this->photoFile = $photoFile;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;

        if(null !== $this->photoFile){
            $this->updatedAt = new \DateTimeImmutable();
        }

       
    }
}
