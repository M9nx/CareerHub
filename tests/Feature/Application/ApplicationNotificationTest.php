<?php

namespace Tests\Feature\Application;

use App\Actions\TransitionApplicationStatus;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Notifications\ApplicationStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApplicationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_is_notified_when_application_status_changes(): void
    {
        Notification::fake();

        $application = Application::factory()->create([
            'status' => ApplicationStatus::Submitted,
        ]);

        app(TransitionApplicationStatus::class)->handle(
            $application,
            ApplicationStatus::UnderReview
        );

        Notification::assertSentTo(
            $application->employee,
            ApplicationStatusChangedNotification::class
        );
    }
}
