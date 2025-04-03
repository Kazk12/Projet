<?php

namespace App\Repository;

use App\Entity\UserLikeAnnounce;
use App\Entity\Announce;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserLikeAnnounce>
 */
class UserLikeAnnounceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLikeAnnounce::class);
    }

 /**
     * Récupère les annonces likées par un utilisateur spécifique
     *
     * @param int $userId ID de l'utilisateur
     * @return MyLikedAnnounce[] Tableau d'annonces likées sous forme de DTO
     */
    public function findLikedAnnouncesByUser(int $userId): array
    {
        try {
            return $this->createQueryBuilder('ula')
                ->select('NEW App\\DTO\\MyLikedAnnounce(
                    a.id, 
                    a.imageUrl, 
                    b.title, 
                    b.author, 
                    a.content, 
                    a.rate, 
                    u.pseudo,
                     a.id
                )')
                ->leftJoin('ula.announce', 'a')
                ->leftJoin('a.book', 'b')
                ->leftJoin('a.user', 'u')
                ->where('ula.user = :userId')
                ->setParameter('userId', $userId)
                ->orderBy('a.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            // Gestion des erreurs
            return [];
        }
    }
    /**
     * Récupère les likes d'un utilisateur pour plusieurs annonces en une seule requête
     * 
     * @param array<Announce> $announces
     * @param User $user
     * @return array<int, bool> Tableau avec [announceId => bool]
     */
    public function findUserLikesForAnnounces(array $announces, User $user): array
    {
        if (empty($announces) || !$user) {
            return [];
        }

        $announceIds = array_map(function (Announce $announce) {
            return $announce->getId();
        }, $announces);

        $qb = $this->createQueryBuilder('ula')
            ->select('IDENTITY(ula.announce) as announceId')
            ->where('ula.announce IN (:announces)')
            ->andWhere('ula.user = :user')
            ->setParameter('announces', $announceIds)
            ->setParameter('user', $user);

        $results = $qb->getQuery()->getResult();

        $userLikes = [];
        foreach ($results as $result) {
            $userLikes[(int)$result['announceId']] = true;
        }

        return $userLikes;
    }

    /**
     * Compte le nombre de likes pour plusieurs annonces en une seule requête
     * 
     * @param array<Announce> $announces
     * @return array<int, int> Tableau avec [announceId => count]
     */
    public function countLikesForAnnounces(array $announces): array
    {
        if (empty($announces)) {
            return [];
        }

        $announceIds = array_map(function (Announce $announce) {
            return $announce->getId();
        }, $announces);

        $qb = $this->createQueryBuilder('ula')
            ->select('IDENTITY(ula.announce) as announceId, COUNT(ula.id) as likeCount')
            ->where('ula.announce IN (:announces)')
            ->groupBy('ula.announce')
            ->setParameter('announces', $announceIds);

        $results = $qb->getQuery()->getResult();

        $likeCounts = [];
        foreach ($results as $result) {
            $likeCounts[(int)$result['announceId']] = (int)$result['likeCount'];
        }


        // Initialisation à 0 pour les annonces sans likes
        foreach ($announces as $announce) {
            $announceId = $announce->getId();
            if (!isset($likeCounts[$announceId])) {
                $likeCounts[$announceId] = 0;
            }
        }

        return $likeCounts;
    }
}
