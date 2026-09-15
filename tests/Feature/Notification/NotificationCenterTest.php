<?php

use App\Models\Application;
use App\Models\Connection;
use App\Models\Post;
use App\Models\User;
use App\Notifications\ApplicationStatusChangedNotification;
use App\Notifications\ConnectionAcceptedNotification;
use App\Notifications\ConnectionRequestReceivedNotification;
use App\Notifications\PostCommentedNotification;
use Illuminate\Support\Facades\Notification;

test('connection request creates a database notification for the addressee', function () {
    Notification::fake();

    $requester = actingAsEmployee(['name' => 'Requester']);
    $addressee = User::factory()->employer()->create(['name' => 'Addressee']);

    $this->post(route('network.connect', $addressee))
        ->assertRedirect();

    Notification::assertSentTo($addressee, ConnectionRequestReceivedNotification::class);
});

test('accepting a connection notifies the requester', function () {
    Notification::fake();

    $requester = User::factory()->employee()->create();
    $addressee = actingAsEmployer();
    $connection = Connection::factory()->pending()->create([
        'requester_id' => $requester->id,
        'addressee_id' => $addressee->id,
    ]);

    $this->post(route('network.connections.accept', $connection))
        ->assertRedirect();

    Notification::assertSentTo($requester, ConnectionAcceptedNotification::class);
});

test('commenting on a post notifies the author', function () {
    Notification::fake();

    $author = User::factory()->employer()->create();
    $commenter = actingAsEmployee();
    $post = Post::factory()->for($author, 'author')->published()->create([
        'author_role' => $author->role,
    ]);

    $this->post(route('feed.posts.comments.store', $post), [
        'body' => 'Nice update',
    ])->assertRedirect();

    Notification::assertSentTo($author, PostCommentedNotification::class);
});

test('notifications page lists unread items and can mark them read', function () {
    $user = actingAsEmployee();
    $user->notify(new ConnectionRequestReceivedNotification(
        Connection::factory()->pending()->create([
            'requester_id' => User::factory()->employer()->create()->id,
            'addressee_id' => $user->id,
        ])
    ));

    $this->get(route('notifications.index'))
        ->assertOk()
        ->assertSee(__('Notifications'))
        ->assertSee(__('Open'));

    $notification = $user->notifications()->first();

    $this->post(route('notifications.read', $notification))
        ->assertRedirect();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('header shows notifications destination', function () {
    actingAsEmployee();

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee(__('Notifications'))
        ->assertSee(route('notifications.index'), false);
});

test('application status notification still supports mail and database channels', function () {
    $notification = new ApplicationStatusChangedNotification(
        Application::factory()->create()
    );

    expect($notification->via((object) []))->toContain('mail', 'database');
});
