<?php

namespace App\Http\Controllers\Employer;

use App\Actions\TransitionApplicationStatus;
use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(
        Request $request,
        TransitionApplicationStatus $transitionApplicationStatus,
    ): View {
        $applications = Application::query()
            ->whereHas(
                'jobPosting',
                fn ($query) => $query->whereBelongsTo($request->user(), 'employer')
            )
            ->with(['employee', 'jobPosting'])
            ->latest()
            ->get();

        return view('employer.applications.index', [
            'applications' => $applications,
            'statusTransition' => $transitionApplicationStatus,
        ]);
    }

    public function show(Application $application): View
    {
        Gate::authorize('update', $application);

        $application->loadMissing(['employee', 'jobPosting']);

        return view('employer.applications.show', compact('application'));
    }

    public function update(
        Request $request,
        Application $application,
        TransitionApplicationStatus $transitionApplicationStatus,
    ): RedirectResponse {
        Gate::authorize('update', $application);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(ApplicationStatus::class)],
        ]);

        $transitionApplicationStatus->handle(
            $application,
            ApplicationStatus::from($validated['status'])
        );

        return back()->with(
            'success',
            __('Application status updated successfully.')
        );
    }
}
