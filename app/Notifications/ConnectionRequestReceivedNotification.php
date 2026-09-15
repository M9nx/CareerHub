<?php

namespace App\Notifications;

use App\Models\Connection;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConnectionRequestReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Connection $connectionModel,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->connectionModel->loadMissing('requester');

        return [
            'type' => 'connection_request_received',
            'message' => __(':name sent you a connection request.', [
                'name' => $this->connectionModel->requester->name,
            ]),
            'url' => route('network.index'),
            'connection_id' => $this->connectionModel->id,
            'actor_id' => $this->connectionModel->requester_id,
            'actor_name' => $this->connectionModel->requester->name,
        ];
    }
}
