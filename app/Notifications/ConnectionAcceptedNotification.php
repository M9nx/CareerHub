<?php

namespace App\Notifications;

use App\Models\Connection;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConnectionAcceptedNotification extends Notification
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
        $this->connectionModel->loadMissing('addressee');

        return [
            'type' => 'connection_accepted',
            'message' => __(':name accepted your connection request.', [
                'name' => $this->connectionModel->addressee->name,
            ]),
            'url' => route('network.index'),
            'connection_id' => $this->connectionModel->id,
            'actor_id' => $this->connectionModel->addressee_id,
            'actor_name' => $this->connectionModel->addressee->name,
        ];
    }
}
