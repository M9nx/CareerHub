<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\StoreJobPostingRequest;
use App\Http\Requests\Employer\UpdateJobPostingRequest;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class JobPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $jobs = JobPosting::query()
            ->where('employer_id', $request->user()->id)
            ->latest()
            ->get();

        return view('employer.jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', JobPosting::class);

        return view('employer.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobPostingRequest $request): RedirectResponse
    {
        JobPosting::create([
            'employer_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', __('Job posting created successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobPosting $job): View
    {
        Gate::authorize('update', $job);

        return view('employer.jobs.edit', compact('job'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateJobPostingRequest $request,
        JobPosting $job
    ): RedirectResponse {
        Gate::authorize('update', $job);

        $job->update($request->validated());

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', __('Job posting updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPosting $job): RedirectResponse
    {
        Gate::authorize('delete', $job);

        $job->delete();

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', __('Job posting deleted successfully.'));
    }
}
