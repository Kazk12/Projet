<?php

namespace App\Repository;

use App\DTO\AnnounceFilter;
use App\Entity\Statut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Statut>
 */
class StatutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Statut::class);
    }


  /**
     * Récupère les utilisateurs amis de l'utilisateur courant
     * 
     * @param AnnounceFilter 
     * @return array 
     */
    public function findFriendsByUser(): array
    {
        /**
         * @var User $user
         */
        $user = $this->security->getUser();
        return $this->createQueryBuilder('s')
            ->select( 'u.pseudo', 'u.id')
            ->leftJoin('s.otherUser', 'u')
            ->where('s.user = :userId')
            ->andWhere('s.statut = :friend')
            ->setParameter('userId', $user->getId())
            ->setParameter('friend', 'Friend')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Récupère les utilisateurs bloqués par l'utilisateur courant
     * 
     * @param AnnounceFilter 
     * @return array 
     */
    public function findBlockedByUser(): array
    {
        /**
         * @var User $user
         */
        $user = $this->security->getUser();
        return $this->createQueryBuilder('s')
            ->select('s', 'u.pseudo')
            ->leftJoin('s.otherUser', 'u')
            ->where('s.user = :userId')
            ->andWhere('s.statut = :blocked')
            ->setParameter('userId', $user->getId())
            ->setParameter('blocked', 'Blocked')
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
