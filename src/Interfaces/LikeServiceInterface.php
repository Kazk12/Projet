<?php
// filepath: c:\Users\Dev404\Desktop\Symfony\Projet\src\Interfaces\LikeServiceInterface.php

namespace App\Interfaces;

use App\Entity\Announce;
use App\Entity\User;

/**
 * Interface pour le service de gestion des likes
 */
interface LikeServiceInterface
{
    /**
     * Vérifie si l'utilisateur a liké l'annonce
     */
    public function hasUserLikedAnnounce(Announce $announce, ?User $user): bool;
    
    
    /**
     * Récupère les informations de like pour plusieurs annonces
     * 
     * @param array<Announce> $announces
     * @return array<int, array{hasLiked: bool, count: int}>
     */
    public function getLikeInfoForAnnounces(array $announces, ?User $user): array;
}