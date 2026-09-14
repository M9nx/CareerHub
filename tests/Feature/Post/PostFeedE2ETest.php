<?php

use App\Enums\PostStatus;
use App\Filament\SuperAdmin\Resources\PostModerationResource\Pages\ListPosts;
use App\Filament\SuperAdmin\Resources\UserResource\Pages\ListUsers;
use App\Models\Post;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
});

test('both personas publish to the feed, super admin hides a post, then blocks the author', function () {
    $employer = actingAsEmployer(['name' => 'Feed Employer']);

    $this->post(route('employer.posts.store'), [
        'title' => 'Employer Feed Story',
        'body' => 'Sharing hiring updates with the community.',
        'publish' => '1',
    ])->assertRedirect(route('employer.posts.index'));

    $employerPost = Post::query()
        ->where('author_id', $employer->id)
        ->where('title', 'Employer Feed Story')
        ->firstOrFail();

    $employee = actingAsEmployee(['name' => 'Feed Employee']);

    $this->post(route('employee.posts.store'), [
        'title' => 'Employee Feed Story',
        'body' => 'Sharing career progress with the community.',
        'publish' => '1',
    ])->assertRedirect(route('employee.posts.index'));

    $employeePost = Post::query()
        ->where('author_id', $employee->id)
        ->where('title', 'Employee Feed Story')
        ->firstOrFail();

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Employer Feed Story')
        ->assertSee('Employee Feed Story')
        ->assertSee('Feed Employer')
        ->assertSee('Feed Employee');

    $superAdmin = User::factory()->superAdmin()->create();
    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListPosts::class)
        ->callAction(TestAction::make('hide')->table($employerPost))
        ->assertNotified();

    expect($employerPost->fresh()->status)->toBe(PostStatus::Hidden);

    $this->post(route('logout'));
    $this->actingAs($employer);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertDontSee('Employer Feed Story')
        ->assertSee('Employee Feed Story');

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('blockFromPosts')->table($employee))
        ->assertSuccessful();

    expect($employee->fresh()->isBlockedFromPosts())->toBeTrue();

    $this->post(route('logout'));
    $this->actingAs($employee->fresh());

    $this->get(route('employee.posts.create'))
        ->assertRedirect(route('feed.index'))
        ->assertSessionHas('error', __('You are blocked from creating posts.'));
});
