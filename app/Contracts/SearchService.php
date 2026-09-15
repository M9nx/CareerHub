<?php

namespace App\Contracts;

use App\Support\Search\SearchResults;

interface SearchService
{
    public function search(string $query, int $limit = 8): SearchResults;
}
