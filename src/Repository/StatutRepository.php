<?php

namespace App\Repository;

use App\DTO\AnnounceFilter;
use App\Entity\Statut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statut>
 */
class StatutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statut::class);
    }


   /**
     * @return array
     */
    public function findFriendsByUser(AnnounceFilter $filter): array
    {
        return $this->createQueryBuilder('s')
            ->select('s', 'u.pseudo')
            ->leftJoin('s.otherUser', 'u')
            ->where('s.user = :userId')
            ->andWhere('s.statut = :friend')
            ->setParameter('userId', $filter->getUserId())
            ->setParameter('friend', 'Friend')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Statut[] Returns an array of Statut objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Statut
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
