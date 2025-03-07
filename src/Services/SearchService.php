<?php

namespace App\Services;

use App\DTO\SearchCriteria;
use App\Interfaces\SearchServiceInterface;
use App\Repository\AnnounceRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\QueryBuilder;

class SearchService implements SearchServiceInterface
{
    private AnnounceRepository $announceRepository;
    private GenreRepository $genreRepository;

    public function __construct(
        AnnounceRepository $announceRepository,
        GenreRepository $genreRepository
    ) {
        $this->announceRepository = $announceRepository;
        $this->genreRepository = $genreRepository;
    }

    /**
     * Récupère un QueryBuilder pour la recherche selon les critères
     */
    public function getSearchQueryBuilder(SearchCriteria $criteria): QueryBuilder
    {
        return $this->announceRepository->searchByFilters(
            $criteria->getQuery(),
            $criteria->getGenre(),
            $criteria->getUserId()
        );
    }

    /**
     * Récupère tous les genres pour le filtre
     */
    public function getAllGenres(): array
    {
        return $this->genreRepository->findAll();
    }
}