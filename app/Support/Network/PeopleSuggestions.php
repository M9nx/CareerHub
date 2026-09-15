<?php

namespace App\Support\Network;

use App\Enums\ConnectionStatus;
use App\Enums\UserRole;
use App\Models\Connection;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;

class PeopleSuggestions
{
    /**
     * Deterministic suggestions: active network personas who have posted,
     * excluding self and open connection pairs, ordered by name.
     *
     * @return Collection<int, User>
     */
    public function for(User $viewer, int $limit = 8): Collection
    {
        $excludedIds = Connection::query()
            ->whereIn('status', [ConnectionStatus::Pending, ConnectionStatus::Accepted])
            ->where(function ($query) use ($viewer): void {
                $query->where('requester_id', $viewer->id)
                    ->orWhere('addressee_id', $viewer->id);
            })
            ->get(['requester_id', 'addressee_id'])
            ->flatMap(fn (Connection $connection) => [
                (int) $connection->requester_id,
                (int) $connection->addressee_id,
            ])
            ->push((int) $viewer->id)
            ->unique()
            ->values()
            ->all();

        return User::query()
            ->where('is_active', true)
            ->whereIn('role', [UserRole::Employee, UserRole::Employer])
            ->whereKeyNot($excludedIds)
            ->whereIn('id', Post::published()->select('author_id'))
            ->orderBy('name')
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }
}
