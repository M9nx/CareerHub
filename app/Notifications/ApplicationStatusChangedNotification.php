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

    public function via(object $notifiable): array
    {
        return ['mail'];
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

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_title' => $this->application->jobPosting->title,
            'status' => $this->application->status->value,
        ];
    }
}
