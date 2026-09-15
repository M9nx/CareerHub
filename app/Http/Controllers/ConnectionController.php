<?php

namespace App\Http\Controllers;

use App\Actions\AcceptConnection;
use App\Actions\RejectConnection;
use App\Actions\RemoveConnection;
use App\Actions\SendConnectionRequest;
use App\Actions\WithdrawConnection;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConnectionController extends Controller
{
    public function store(Request $request, User $user, SendConnectionRequest $sendConnectionRequest): RedirectResponse
    {
        Gate::authorize('create', Connection::class);

        $sendConnectionRequest->handle($request->user(), $user);

        return back()->with('success', __('Connection request sent.'));
    }

    public function accept(Connection $connection, AcceptConnection $acceptConnection): RedirectResponse
    {
        Gate::authorize('accept', $connection);

        $acceptConnection->handle($connection);

        return back()->with('success', __('Connection accepted.'));
    }

    public function reject(Connection $connection, RejectConnection $rejectConnection): RedirectResponse
    {
        Gate::authorize('reject', $connection);

        $rejectConnection->handle($connection);

        return back()->with('success', __('Connection ignored.'));
    }

    public function withdraw(Connection $connection, WithdrawConnection $withdrawConnection): RedirectResponse
    {
        Gate::authorize('withdraw', $connection);

        $withdrawConnection->handle($connection);

        return back()->with('success', __('Connection request withdrawn.'));
    }

    public function destroy(Connection $connection, RemoveConnection $removeConnection): RedirectResponse
    {
        Gate::authorize('delete', $connection);

        $removeConnection->handle($connection);

        return back()->with('success', __('Connection removed.'));
    }
}
