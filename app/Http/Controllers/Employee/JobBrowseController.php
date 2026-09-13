<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;

class JobBrowseController extends Controller
{
    public function index()
    {
        $jobs = JobPosting::published()
            ->latest()
            ->paginate(10);

        return view('employee.jobs.index', compact('jobs'));
    }

    public function show(JobPosting $jobPosting)
    {
        $jobPosting = JobPosting::published()
            ->findOrFail($jobPosting->id);

        return view('employee.jobs.show', compact('jobPosting'));
    }
}