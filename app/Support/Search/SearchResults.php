<?php

namespace App\Support\Search;

use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;

readonly class SearchResults
{
    /**
     * @param  Collection<int, User>  $people
     * @param  Collection<int, JobPosting>  $jobs
     * @param  Collection<int, EmployerProfile>  $companies
     * @param  Collection<int, Post>  $posts
     */
    public function __construct(
        public Collection $people,
        public Collection $jobs,
        public Collection $companies,
        public Collection $posts,
    ) {}

    public function isEmpty(): bool
    {
        return $this->people->isEmpty()
            && $this->jobs->isEmpty()
            && $this->companies->isEmpty()
            && $this->posts->isEmpty();
    }
}
