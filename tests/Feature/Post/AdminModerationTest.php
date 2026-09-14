<?php

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Filament\SuperAdmin\Resources\PostModerationResource;
use App\Filament\SuperAdmin\Resources\PostModerationResource\Pages\ListPosts;
use App\Filament\SuperAdmin\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
});

test('super admin can list posts with author and role', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $author = User::factory()->employer()->create(['name' => 'Moderation Author']);

    Post::factory()->for($author, 'author')->published()->create([
        'title' => 'Moderation List Post',
        'author_role' => UserRole::Employer,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListPosts::class)
        ->assertOk()
        ->assertCanSeeTableRecords(Post::all())
        ->assertSee('Moderation List Post')
        ->assertSee('Moderation Author')
        ->assertSee(UserRole::Employer->label());
});

test('super admin can hide a post', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $author = User::factory()->create();
    $post = Post::factory()->create([
        'author_id' => $author->id,
        'author_role' => $author->role,
        'status' => PostStatus::Published,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListPosts::class)
        ->callAction(TestAction::make('hide')->table($post))
        ->assertNotified();

    expect($post->fresh()->status)->toBe(PostStatus::Hidden);

    $this->assertDatabaseHas('activity_logs', [
        'log_name' => 'post.hidden',
        'subject_id' => $post->id,
        'causer_id' => $superAdmin->id,
    ]);
});

test('super admin can archive a post', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $author = User::factory()->create();
    $post = Post::factory()->create([
        'author_id' => $author->id,
        'author_role' => $author->role,
        'status' => PostStatus::Published,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListPosts::class)
        ->callAction(TestAction::make('archive')->table($post))
        ->assertNotified();

    expect($post->fresh()->status)->toBe(PostStatus::Archived);
});

test('hide action is hidden when the post is already hidden', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $author = User::factory()->create();
    $hidden = Post::factory()->create([
        'author_id' => $author->id,
        'author_role' => $author->role,
        'status' => PostStatus::Hidden,
    ]);
    $published = Post::factory()->create([
        'author_id' => $author->id,
        'author_role' => $author->role,
        'status' => PostStatus::Published,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListPosts::class)
        ->assertActionHidden(TestAction::make('hide')->table($hidden))
        ->assertActionVisible(TestAction::make('hide')->table($published));
});

test('archive action is hidden when the post is already archived', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $author = User::factory()->create();
    $archived = Post::factory()->create([
        'author_id' => $author->id,
        'author_role' => $author->role,
        'status' => PostStatus::Archived,
    ]);
    $published = Post::factory()->create([
        'author_id' => $author->id,
        'author_role' => $author->role,
        'status' => PostStatus::Published,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListPosts::class)
        ->assertActionHidden(TestAction::make('archive')->table($archived))
        ->assertActionVisible(TestAction::make('archive')->table($published));
});

test('block author action links to the user edit page', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $author = User::factory()->employee()->create();
    $post = Post::factory()->for($author, 'author')->published()->create([
        'author_role' => UserRole::Employee,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListPosts::class)
        ->assertActionVisible(TestAction::make('blockAuthor')->table($post))
        ->assertActionHasUrl(
            TestAction::make('blockAuthor')->table($post),
            UserResource::getUrl('edit', ['record' => $author]),
        );
});

test('status filter limits the moderation list', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $author = User::factory()->create();

    $published = Post::factory()->create([
        'author_id' => $author->id,
        'author_role' => $author->role,
        'title' => 'Published Moderation Post',
        'status' => PostStatus::Published,
    ]);
    $draft = Post::factory()->create([
        'author_id' => $author->id,
        'author_role' => $author->role,
        'title' => 'Draft Moderation Post',
        'status' => PostStatus::Draft,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListPosts::class)
        ->filterTable('status', PostStatus::Published->value)
        ->assertCanSeeTableRecords([$published])
        ->assertCanNotSeeTableRecords([$draft]);
});

test('employer cannot access post moderation', function () {
    $employer = User::factory()->employer()->create();

    $this->actingAs($employer, 'filament')
        ->get(PostModerationResource::getUrl('index'))
        ->assertForbidden();

    Livewire::test(ListPosts::class)
        ->assertForbidden();
});
