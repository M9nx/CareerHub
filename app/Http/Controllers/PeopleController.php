<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PeopleController extends Controller
{
    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        $user->loadMissing([
            'employerProfile',
            'employeeProfile',
            'profileExperiences',
            'profileEducations',
            'profileSkills',
        ]);

        $recentPosts = Post::query()
            ->published()
            ->where('author_id', $user->id)
            ->with('attachments')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $viewer = auth()->user();
        $connection = $viewer->is($user)
            ? null
            : Connection::between($viewer, $user);

        return view('people.show', [
            'person' => $user,
            'recentPosts' => $recentPosts,
            'connection' => $connection,
            'experiences' => $user->profileExperiences,
            'educations' => $user->profileEducations,
            'skills' => $user->profileSkills,
        ]);
    }
}
