<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Application $application,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->application->loadMissing('jobPosting');

        $title = $this->application->jobPosting->title;
        $status = str($this->application->status->name)->headline()->toString();

        return (new MailMessage)
            ->subject(__('Application Status Updated'))
            ->line(__('The application for :title has been updated.', ['title' => $title]))
            ->line(__('New status: :status', ['status' => $status]));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->application->loadMissing('jobPosting');

        return [
            'type' => 'application_status_changed',
            'message' => __('Application for :title is now :status.', [
                'title' => $this->application->jobPosting->title,
                'status' => str($this->application->status->name)->headline()->toString(),
            ]),
            'url' => route('employee.applications.index'),
            'application_id' => $this->application->id,
            'job_title' => $this->application->jobPosting->title,
            'status' => $this->application->status->value,
        ];
    }
}
