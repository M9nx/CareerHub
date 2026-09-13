<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(): View
    {
        $posts = Post::published()
            ->with('author')
            ->latest()
            ->get();

        return view('feed.index', compact('posts'));
    }
}
