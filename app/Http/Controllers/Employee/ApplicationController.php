<?php

namespace App\Http\Controllers\Employee;

use App\Actions\CancelApplication;
use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreApplicationRequest;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('create', Application::class);

        $applications = Application::query()
            ->whereBelongsTo($request->user(), 'employee')
            ->with('jobPosting')
            ->latest()
            ->get();

        return view('employee.applications.index', compact('applications'));
    }

    public function store(StoreApplicationRequest $request): RedirectResponse
    {
        Application::create([
            'employee_id' => $request->user()->id,
            'job_posting_id' => $request->validated('job_posting_id'),
            'cover_letter' => $request->validated('cover_letter'),
            'status' => ApplicationStatus::Submitted,
        ]);

        return redirect()
            ->route('employee.applications.index')
            ->with('success', __('Application submitted successfully.'));
    }

    public function cancel(
        Application $application,
        CancelApplication $cancelApplication,
    ): RedirectResponse {
        Gate::authorize('cancel', $application);

        $cancelApplication->handle($application);

        return redirect()
            ->route('employee.applications.index')
            ->with('success', __('Application cancelled successfully.'));
    }

    public function show(Application $application): View
    {
        Gate::authorize('view', $application);

        $application->loadMissing('jobPosting');

        return view('employee.applications.show', compact('application'));
    }
}
