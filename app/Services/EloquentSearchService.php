<?php

namespace App\Services;

use App\Contracts\SearchService;
use App\Enums\UserRole;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\User;
use App\Support\Search\SearchResults;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentSearchService implements SearchService
{
    public function search(string $query, int $limit = 8): SearchResults
    {
        $term = trim($query);

        if ($term === '') {
            return new SearchResults(
                people: collect(),
                jobs: collect(),
                companies: collect(),
                posts: collect(),
            );
        }

        $like = '%'.$this->escapeLike($term).'%';

        return new SearchResults(
            people: $this->people($like, $limit),
            jobs: $this->jobs($like, $limit),
            companies: $this->companies($like, $limit),
            posts: $this->posts($like, $limit),
        );
    }

    /**
     * @return Collection<int, User>
     */
    private function people(string $like, int $limit)
    {
        return User::query()
            ->where('is_active', true)
            ->whereIn('role', [UserRole::Employee, UserRole::Employer])
            ->where(function (Builder $query) use ($like): void {
                $this->whereLike($query, 'name', $like)
                    ->orWhere(function (Builder $inner) use ($like): void {
                        $this->whereLike($inner, 'headline', $like);
                    })
                    ->orWhere(function (Builder $inner) use ($like): void {
                        $this->whereLike($inner, 'location', $like);
                    });
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, JobPosting>
     */
    private function jobs(string $like, int $limit)
    {
        return JobPosting::query()
            ->published()
            ->with(['employer.employerProfile'])
            ->where(function (Builder $query) use ($like): void {
                $this->whereLike($query, 'title', $like)
                    ->orWhere(function (Builder $inner) use ($like): void {
                        $this->whereLike($inner, 'description', $like);
                    })
                    ->orWhere(function (Builder $inner) use ($like): void {
                        $this->whereLike($inner, 'location', $like);
                    });
            })
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, EmployerProfile>
     */
    private function companies(string $like, int $limit)
    {
        return EmployerProfile::query()
            ->with('user')
            ->whereHas('user', function (Builder $query): void {
                $query->where('is_active', true)
                    ->where('role', UserRole::Employer);
            })
            ->where(function (Builder $query) use ($like): void {
                $this->whereLike($query, 'company_name', $like)
                    ->orWhere(function (Builder $inner) use ($like): void {
                        $this->whereLike($inner, 'industry', $like);
                    })
                    ->orWhere(function (Builder $inner) use ($like): void {
                        $this->whereLike($inner, 'location', $like);
                    });
            })
            ->orderBy('company_name')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Post>
     */
    private function posts(string $like, int $limit)
    {
        return Post::query()
            ->published()
            ->with('author')
            ->where(function (Builder $query) use ($like): void {
                $this->whereLike($query, 'title', $like)
                    ->orWhere(function (Builder $inner) use ($like): void {
                        $this->whereLike($inner, 'body', $like);
                    });
            })
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    private function whereLike(Builder $query, string $column, string $like): Builder
    {
        return $query->whereRaw(
            $column.' LIKE ? ESCAPE ?',
            [$like, '\\'],
        );
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }
}
