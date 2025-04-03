<?php
// filepath: c:\Users\Dev404\Desktop\Symfony\Projet\src\Services\LikeService.php

namespace App\Services;

use App\Entity\Announce;
use App\Entity\User;
use App\Interfaces\LikeServiceInterface;
use App\Repository\UserLikeAnnounceRepository;

/**
 * Service pour gérer les informations des likes
 */
class LikeService implements LikeServiceInterface
{
    private UserLikeAnnounceRepository $userLikeAnnounceRepository;

    public function __construct(UserLikeAnnounceRepository $userLikeAnnounceRepository)
    {
        $this->userLikeAnnounceRepository = $userLikeAnnounceRepository;
    }


     /**
     * Vérifie si l'utilisateur a liké l'annonce
     * Utilise findUserLikesForAnnounces avec un tableau contenant une seule annonce
     * pour rester cohérent avec l'approche d'optimisation des requêtes
     */
    public function hasUserLikedAnnounce(Announce $announce, ?User $user): bool
    {
        if (!$user) {
            return false;
        }
        
        $likes = $this->userLikeAnnounceRepository->findUserLikesForAnnounces([$announce], $user);
        return isset($likes[$announce->getId()]);
    }

    /**
     * Récupère les informations de like pour plusieurs annonces
     * 
     * @param array<Announce> $announces
     * @return array<int, array{hasLiked: bool, count: int}>
     */
    public function getLikeInfoForAnnounces(array $announces, ?User $user): array
    {
        if (empty($announces)) {
            return [];
        }

        try {
            // Récupération du nombre de likes pour chaque annonce
            $likeCounts = $this->userLikeAnnounceRepository->countLikesForAnnounces($announces);
            
            // Récupération des likes de l'utilisateur si connecté
            $userLikes = $user ? $this->userLikeAnnounceRepository->findUserLikesForAnnounces($announces, $user) : [];
            
            $likeInfo = [];
            foreach ($announces as $announce) {
                $announceId = $announce->getId();
                $likeInfo[$announceId] = [
                    'hasLiked' => isset($userLikes[$announceId]),
                    'count' => $likeCounts[$announceId] ?? 0
                ];
            }
            
            return $likeInfo;
        } catch (\Exception $e) {
            // Gestion des erreurs selon les bonnes pratiques
            // Retourne un tableau vide pour éviter de casser l'affichage
            return array_fill_keys(
                array_map(fn(Announce $announce) => $announce->getId(), $announces),
                ['hasLiked' => false, 'count' => 0]
            );
        }
    }
}