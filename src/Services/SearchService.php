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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getAllGenres(): array
    {
        return $this->genreRepository->findAll();
    }
}