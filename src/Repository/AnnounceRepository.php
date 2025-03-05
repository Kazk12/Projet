<?php

namespace App\Repository;

use App\DTO\AnnounceFilter;
use App\Entity\Announce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Announce>
 */
class AnnounceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announce::class);
    }

   /**
     * @return AnnounceMine[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


   /**
     * @return Announce[]
     */
    public function findByUserStatus(AnnounceFilter $filter): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Announce a
            LEFT JOIN App\Entity\Statut s WITH s.otherUser = a.user AND s.user = :userId
            WHERE s.statut IS NULL OR s.statut != :blocked
            ORDER BY 
                s.statut DESC,
                a.createdAt DESC'
        )->setParameters([
            'userId' => $filter->getUserId(),
            'blocked' => "Blocked"
        ]);

        return $query->getResult();
    }

    

    //    /**
    //     * @return Announce[] Returns an array of Announce objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Announce
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
