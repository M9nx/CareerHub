<?php

namespace App\Http\Controllers\Employer;

use App\Actions\TransitionApplicationStatus;
use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applications = Application::query()
            ->whereHas('jobPosting', function ($query) use ($request) {
                $query->where('employer_id', $request->user()->id);
            })
            ->latest()
            ->get();

        return view('employer.applications.index', compact('applications'));
    }

    public function show(Application $application): View
    {
        Gate::authorize('update', $application);

        return view('employer.applications.show', compact('application'));
    }

    public function update(
        Request $request,
        Application $application,
        TransitionApplicationStatus $transitionApplicationStatus,
    ): RedirectResponse {
        Gate::authorize('update', $application);

        $request->validate([
            'status' => ['required'],
        ]);

        $transitionApplicationStatus->handle(
            $application,
            ApplicationStatus::from($request->string('status')->value())
        );

        return back()->with(
            'success',
            __('Application status updated successfully.')
        );
    }
}