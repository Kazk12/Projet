<?php

namespace App\Interfaces;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\File;

interface UpdateProfilInterface
{
    public function updateProfil(User $user, string $pseudo, string $email, ?File $photoFile ): void;
}