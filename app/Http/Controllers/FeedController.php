<?php

namespace App\Http\Controllers;

use App\Support\Timeline\TimelineQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(Request $request): View
    {
        $items = (new TimelineQuery($request))->paginate($request->user());

        return view('feed.index', compact('items'));
    }
}
