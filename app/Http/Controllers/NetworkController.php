<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Support\Network\PeopleSuggestions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NetworkController extends Controller
{
    public function index(Request $request, PeopleSuggestions $peopleSuggestions): View
    {
        $viewer = $request->user();

        $incoming = Connection::query()
            ->pending()
            ->where('addressee_id', $viewer->id)
            ->with('requester')
            ->latest()
            ->get();

        $outgoing = Connection::query()
            ->pending()
            ->where('requester_id', $viewer->id)
            ->with('addressee')
            ->latest()
            ->get();

        $connections = Connection::query()
            ->accepted()
            ->where(function ($query) use ($viewer): void {
                $query->where('requester_id', $viewer->id)
                    ->orWhere('addressee_id', $viewer->id);
            })
            ->with(['requester', 'addressee'])
            ->latest('updated_at')
            ->get()
            ->map(fn (Connection $connection) => [
                'connection' => $connection,
                'person' => $connection->otherParty($viewer),
            ]);

        $suggestions = $peopleSuggestions->for($viewer);

        return view('network.index', [
            'incoming' => $incoming,
            'outgoing' => $outgoing,
            'connections' => $connections,
            'suggestions' => $suggestions,
            'pendingCount' => $incoming->count(),
        ]);
    }
}
