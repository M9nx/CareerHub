<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostCommentedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Post $post,
        public PostComment $comment,
        public User $actor,
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
        return [
            'type' => 'post_commented',
            'message' => __(':name commented on your post.', [
                'name' => $this->actor->name,
            ]),
            'url' => route('feed.index').'#post-'.$this->post->id,
            'post_id' => $this->post->id,
            'comment_id' => $this->comment->id,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
        ];
    }
}
