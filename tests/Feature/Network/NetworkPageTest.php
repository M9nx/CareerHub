<?php

use App\Models\Connection;
use App\Models\Post;
use App\Models\User;

test('network page lists invitations connections and suggestions', function () {
    $viewer = actingAsEmployee(['name' => 'Network Viewer']);
    $inviter = User::factory()->employer()->create(['name' => 'Incoming Person']);
    $outgoingTarget = User::factory()->employer()->create(['name' => 'Outgoing Person']);
    $connected = User::factory()->employee()->create(['name' => 'Connected Person']);
    $suggested = User::factory()->employer()->create(['name' => 'Suggested Author']);

    Connection::factory()->pending()->create([
        'requester_id' => $inviter->id,
        'addressee_id' => $viewer->id,
    ]);

    Connection::factory()->pending()->create([
        'requester_id' => $viewer->id,
        'addressee_id' => $outgoingTarget->id,
    ]);

    Connection::factory()->accepted()->create([
        'requester_id' => $viewer->id,
        'addressee_id' => $connected->id,
    ]);

    Post::factory()->for($suggested, 'author')->published()->create([
        'author_role' => $suggested->role,
        'body' => 'Suggestion seed post',
    ]);

    $this->get(route('network.index'))
        ->assertOk()
        ->assertSee(__('Network'))
        ->assertSee(__('Invitations'))
        ->assertSee('Incoming Person')
        ->assertSee('Outgoing Person')
        ->assertSee('Connected Person')
        ->assertSee('Suggested Author')
        ->assertSee(__('People you may know'));
});

test('network suggestions exclude open connection pairs and are ordered by name', function () {
    $viewer = actingAsEmployee(['name' => 'Viewer']);
    $alpha = User::factory()->employer()->create(['name' => 'Alpha Author']);
    $beta = User::factory()->employer()->create(['name' => 'Beta Author']);
    $pending = User::factory()->employer()->create(['name' => 'Pending Author']);

    foreach ([$alpha, $beta, $pending] as $author) {
        Post::factory()->for($author, 'author')->published()->create([
            'author_role' => $author->role,
        ]);
    }

    Connection::factory()->pending()->create([
        'requester_id' => $viewer->id,
        'addressee_id' => $pending->id,
    ]);

    $response = $this->get(route('network.index'))->assertOk();
    $content = $response->getContent();
    $suggestionsSection = substr($content, (int) strpos($content, __('People you may know')));

    expect($content)->toContain('Pending Author')
        ->and($suggestionsSection)->toContain('Alpha Author')
        ->and($suggestionsSection)->toContain('Beta Author')
        ->and($suggestionsSection)->not->toContain('Pending Author');

    expect(strpos($suggestionsSection, 'Alpha Author'))->toBeLessThan(strpos($suggestionsSection, 'Beta Author'));
});

test('header includes network navigation', function () {
    actingAsEmployee();

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee(__('Network'))
        ->assertSee(route('network.index'), false);
});

test('guests cannot open the network page', function () {
    $this->get(route('network.index'))
        ->assertRedirect(route('login'));
});
