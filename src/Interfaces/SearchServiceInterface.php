<?php


namespace App\Interfaces;

use App\DTO\SearchCriteria;
use Doctrine\ORM\QueryBuilder;

interface SearchServiceInterface
{
    public function getSearchQueryBuilder(SearchCriteria $criteria): QueryBuilder;
    
    public function getAllGenres(): array;
}