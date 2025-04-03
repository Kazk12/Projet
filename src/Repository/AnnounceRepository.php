<?php

namespace App\Repository;

use App\DTO\AnnounceFilter;
use App\Entity\Announce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Announce>
 */
class AnnounceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
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
 * Récupère les annonces filtrées selon le statut de relation entre utilisateurs
 * 
 
 * @return array<Announce>
 */
public function findByUserStatus(): array
{
    $user = $this->security->getUser();

        if (null === $user) {
        // Sans ID utilisateur, retournons toutes les annonces
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    try {
        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.userLikeAnnounces', 'currentUserLike', 'WITH', 'currentUserLike.user = :currentUser')
            ->leftJoin('u.statutsOther', 'so', 'WITH', 'so.user = :userId')
            ->where('so.statut IS NULL OR so.statut != :blocked')
            ->setParameter('userId', $user->getId())
            ->setParameter('currentUser', $user)
            ->setParameter('blocked', 'Blocked')
            ->orderBy('so.statut', 'DESC')
            ->addOrderBy('a.createdAt', 'DESC');
        
        return $queryBuilder->getQuery()->getResult();
    } catch (\Exception $e) {
        
        return [];
    }
}


    public function searchByFilters(?string $query = null, ?string $genre = null, ?int $userId = null)
{
    $queryBuilder = $this->createQueryBuilder('a')
        ->leftJoin('a.book', 'b')
        ->leftJoin('b.typeBooks', 'tb')
        ->leftJoin('tb.genre', 'g')
        ->orderBy('a.createdAt', 'DESC');
    
    // Filtrer par statut utilisateur
    if ($userId) {
        $queryBuilder
            ->leftJoin('a.user', 'u')
            ->leftJoin('u.statutsOther', 'so', 'WITH', 'so.user = :userId')
            ->andWhere('so.statut != :blocked OR so.statut IS NULL')
            ->setParameter('userId', $userId)
            ->setParameter('blocked', 'Blocked');
    }
    
    // Recherche par titre ou auteur
    if ($query) {
        $queryBuilder
            ->andWhere('LOWER(b.title) LIKE LOWER(:query) OR LOWER(b.author) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%');
    }
    
    // Filtrer par genre
    if ($genre) {
        $queryBuilder
            ->andWhere('g.name = :genre')
            ->setParameter('genre', $genre);
    }
    
    return $queryBuilder;

}

    public function countByUserNumberOfAnnounces(int $userId): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }



    



}