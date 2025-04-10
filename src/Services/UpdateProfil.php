<?php

namespace App\Services;

use App\Entity\User;
use App\Interfaces\UpdateProfilInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UpdateProfil implements UpdateProfilInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function updateProfil(User $user, string $pseudo, string $email, ?File $photoFile): void
    {
        if ($photoFile) {
            $user->setPhotoFile($photoFile);
            $user->setUpdatedAt(new \DateTimeImmutable());
        }
        $user->setPseudo($pseudo);
        $user->setEmail($email);


       

        $this->entityManager->flush();
    }
}