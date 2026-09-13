<?php

use App\Enums\JobPostingStatus;
use App\Models\ActivityLog;
use App\Models\JobPosting;
use App\Services\ActivityLogger;

test('creating a job posting writes a created activity log', function () {
    $employer = actingAsEmployer();

    $this->post(route('employer.jobs.store'), [
        'title' => 'Laravel Developer',
        'description' => 'Build and maintain Laravel applications.',
        'status' => JobPostingStatus::Draft->value,
    ])->assertRedirect(route('employer.jobs.index'));

    $job = JobPosting::query()->where('title', 'Laravel Developer')->first();

    $this->assertDatabaseHas('activity_logs', [
        'log_name' => 'job_posting.created',
        'subject_type' => $job->getMorphClass(),
        'subject_id' => $job->id,
        'causer_id' => $employer->id,
    ]);

    $log = ActivityLog::query()
        ->where('log_name', 'job_posting.created')
        ->where('subject_id', $job->id)
        ->first();

    expect($log->properties)->toMatchArray([
        'event' => 'created',
        'status' => JobPostingStatus::Draft->value,
        'title' => 'Laravel Developer',
        'employer_id' => $employer->id,
    ]);
});

test('publishing a job posting writes a published activity log', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->draft()->create([
        'title' => 'Published Career Role',
        'description' => 'A published employer job.',
    ]);

    $this->patch(route('employer.jobs.update', $job), [
        'title' => $job->title,
        'description' => $job->description,
        'status' => JobPostingStatus::Published->value,
    ])->assertRedirect(route('employer.jobs.index'));

    $this->assertDatabaseHas('activity_logs', [
        'log_name' => 'job_posting.published',
        'subject_type' => $job->getMorphClass(),
        'subject_id' => $job->id,
        'causer_id' => $employer->id,
    ]);

    $log = ActivityLog::query()
        ->where('log_name', 'job_posting.published')
        ->where('subject_id', $job->id)
        ->first();

    expect($log->properties)->toMatchArray([
        'event' => 'published',
        'status' => JobPostingStatus::Published->value,
        'from_status' => JobPostingStatus::Draft->value,
        'title' => 'Published Career Role',
        'employer_id' => $employer->id,
    ]);
});

test('closing a job posting writes a closed activity log', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Closed Career Role',
        'description' => 'A closed employer job.',
    ]);

    $this->patch(route('employer.jobs.update', $job), [
        'title' => $job->title,
        'description' => $job->description,
        'status' => JobPostingStatus::Closed->value,
    ])->assertRedirect(route('employer.jobs.index'));

    $this->assertDatabaseHas('activity_logs', [
        'log_name' => 'job_posting.closed',
        'subject_type' => $job->getMorphClass(),
        'subject_id' => $job->id,
        'causer_id' => $employer->id,
    ]);
});

test('updating a job title without a status change does not write a lifecycle log', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->draft()->create([
        'title' => 'Original Title',
        'description' => 'Unchanged description.',
    ]);

    ActivityLog::query()->delete();

    $this->patch(route('employer.jobs.update', $job), [
        'title' => 'Renamed Title',
        'description' => $job->description,
        'status' => JobPostingStatus::Draft->value,
    ])->assertRedirect(route('employer.jobs.index'));

    $this->assertDatabaseMissing('activity_logs', [
        'log_name' => 'job_posting.published',
        'subject_id' => $job->id,
    ]);

    $this->assertDatabaseMissing('activity_logs', [
        'log_name' => 'job_posting.closed',
        'subject_id' => $job->id,
    ]);

    $this->assertDatabaseCount('activity_logs', 0);
});

test('job posting helper persists structured properties', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Structured Career Role',
    ]);

    $log = app(ActivityLogger::class)->logJobPostingEvent('published', $job, $employer, [
        'from_status' => JobPostingStatus::Draft->value,
    ]);

    expect($log->log_name)->toBe('job_posting.published')
        ->and($log->properties)->toMatchArray([
            'event' => 'published',
            'status' => JobPostingStatus::Published->value,
            'from_status' => JobPostingStatus::Draft->value,
            'title' => 'Structured Career Role',
            'employer_id' => $employer->id,
        ]);
});
