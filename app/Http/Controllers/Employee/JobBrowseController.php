<?php

namespace App\Http\Controllers\Employee;

use App\Enums\JobPostingStatus;
use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\View\View;

class JobBrowseController extends Controller
{
    public function index(): View
    {
        $jobs = JobPosting::published()
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
