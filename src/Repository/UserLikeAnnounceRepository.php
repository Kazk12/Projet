<?php

namespace App\Repository;

use App\Entity\UserLikeAnnounce;
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

    //    /**
    //     * @return UserLikeAnnounce[] Returns an array of UserLikeAnnounce objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserLikeAnnounce
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
