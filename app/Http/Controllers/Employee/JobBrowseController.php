<?php

namespace App\Http\Controllers\Employee;

use App\Enums\EmploymentType;
use App\Enums\JobPostingStatus;
use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobBrowseController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $location = $request->string('location')->trim()->toString();
        $employmentType = $request->string('employment_type')->trim()->toString();

        $jobs = JobPosting::published()
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%'.addcslashes($search, '%_\\').'%';

                $query->where(function ($inner) use ($like): void {
                    $inner->whereRaw('title LIKE ? ESCAPE ?', [$like, '\\'])
                        ->orWhereRaw('description LIKE ? ESCAPE ?', [$like, '\\']);
                });
            })
            ->when($location !== '', function ($query) use ($location): void {
                $query->whereRaw(
                    'location LIKE ? ESCAPE ?',
                    ['%'.addcslashes($location, '%_\\').'%', '\\'],
                );
            })
            ->when(
                $employmentType !== '' && EmploymentType::tryFrom($employmentType) !== null,
                function ($query) use ($employmentType): void {
                    $query->where('employment_type', $employmentType);
                },
            )
            ->with(['employer.employerProfile'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $selectedId = $request->integer('selected') ?: null;
        $selected = null;

        if ($selectedId !== null) {
            $selected = $jobs->getCollection()->firstWhere('id', $selectedId);

            if ($selected === null) {
                $selected = JobPosting::published()
                    ->with(['employer.employerProfile'])
                    ->find($selectedId);
            }
        }

        if ($selected === null && $jobs->isNotEmpty()) {
            $selected = $jobs->first();
        }

        return view('employee.jobs.index', [
            'jobs' => $jobs,
            'selected' => $selected,
            'employmentTypes' => EmploymentType::cases(),
        ]);
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
