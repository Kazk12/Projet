<?php

namespace App\Services;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\User;

class UserService
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Retourne l'utilisateur courant ou null si non connecté.
     */
    public function getCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return null;
        }
        $user = $token->getUser();
        return $user instanceof User ? $user : null;
    }
}
