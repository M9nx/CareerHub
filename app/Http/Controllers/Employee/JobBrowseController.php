<?php

namespace App\Http\Controllers\Employee;

use App\Enums\JobPostingStatus;
use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobBrowseController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = JobPosting::published()
            ->when($request->search, fn ($query) => $query->where('title', 'like', '%'.$request->search.'%'))
            ->with(['employer.employerProfile'])
            ->latest()
            ->paginate(10);

        return view('employee.jobs.index', compact('jobs'));
    }

    public function show(JobPosting $jobPosting): View
    {
        abort_unless(
            $jobPosting->status === JobPostingStatus::Published && $jobPosting->is_active,
            404
        );

        $jobPosting->loadMissing(['employer.employerProfile']);

        return view('employee.jobs.show', compact('jobPosting'));
    }
}
