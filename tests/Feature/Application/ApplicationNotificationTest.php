<?php

use App\Actions\CancelApplication;
use App\Actions\TransitionApplicationStatus;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\ApplicationStatusChangedNotification;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpKernel\Exception\HttpException;

test('employee is notified when application status changes', function () {
    Notification::fake();

    $job = JobPosting::factory()->published()->create([
        'title' => 'Laravel Developer',
    ]);
    $application = Application::factory()
        ->for($job, 'jobPosting')
        ->submitted()
        ->create();

    app(TransitionApplicationStatus::class)->handle(
        $application,
        ApplicationStatus::UnderReview
    );

    Notification::assertSentTo(
        $application->employee,
        ApplicationStatusChangedNotification::class,
        function (ApplicationStatusChangedNotification $notification) use ($application): bool {
            $html = (string) $notification->toMail($application->employee)->render();

            return str_contains($html, 'Laravel Developer')
                && str_contains($html, 'Under Review');
        }
    );
});

test('invalid status transition does not notify the employee', function () {
    Notification::fake();

    $application = Application::factory()->submitted()->create();

    expect(fn () => app(TransitionApplicationStatus::class)->handle(
        $application,
        ApplicationStatus::Accepted
    ))->toThrow(HttpException::class);

    Notification::assertNothingSent();
});

test('employer is notified when an employee cancels an application', function () {
    Notification::fake();

    $employer = User::factory()->employer()->create();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Laravel Developer',
    ]);
    $application = Application::factory()
        ->for($job, 'jobPosting')
        ->submitted()
        ->create();

    app(CancelApplication::class)->handle($application);

    Notification::assertSentTo(
        $employer,
        ApplicationStatusChangedNotification::class,
        function (ApplicationStatusChangedNotification $notification) use ($employer): bool {
            $html = (string) $notification->toMail($employer)->render();

            return str_contains($html, 'Laravel Developer')
                && str_contains($html, 'Cancelled');
        }
    );
    Notification::assertNotSentTo(
        $application->employee,
        ApplicationStatusChangedNotification::class
    );
});

test('application notification mail escapes the job title', function () {
    $application = Application::factory()
        ->for(JobPosting::factory()->published()->create([
            'title' => '<script>alert("xss")</script>',
        ]), 'jobPosting')
        ->create([
            'status' => ApplicationStatus::UnderReview,
        ]);

    $html = (string) (new ApplicationStatusChangedNotification($application))
        ->toMail($application->employee)
        ->render();

    expect($html)
        ->not->toContain('<script>alert("xss")</script>')
        ->toContain('&lt;script&gt;');
});
