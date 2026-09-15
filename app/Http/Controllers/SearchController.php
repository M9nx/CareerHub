<?php

namespace App\Http\Controllers;

use App\Contracts\SearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request, SearchService $searchService): View
    {
        $query = $request->string('q')->trim()->toString();
        $type = $request->string('type')->toString();

        $results = $searchService->search($query);

        $sections = match ($type) {
            'people' => ['people' => $results->people],
            'jobs' => ['jobs' => $results->jobs],
            'companies' => ['companies' => $results->companies],
            'posts' => ['posts' => $results->posts],
            default => [
                'people' => $results->people,
                'jobs' => $results->jobs,
                'companies' => $results->companies,
                'posts' => $results->posts,
            ],
        };

        return view('search.index', [
            'query' => $query,
            'type' => $type,
            'sections' => $sections,
            'isEmpty' => $query === '' || (
                ($sections['people'] ?? collect())->isEmpty()
                && ($sections['jobs'] ?? collect())->isEmpty()
                && ($sections['companies'] ?? collect())->isEmpty()
                && ($sections['posts'] ?? collect())->isEmpty()
            ),
        ]);
    }
}
