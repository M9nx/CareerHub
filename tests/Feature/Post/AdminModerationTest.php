<?php

use App\Enums\PostStatus;
use App\Filament\SuperAdmin\Resources\PostModerationResource\Pages\ListPosts;
use App\Models\Post;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
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
test('employer cannot access post moderation', function () {
    $employer = User::factory()->employer()->create();
    $this->actingAs($employer, 'filament')
        ->get('/super-admin/post-moderation')
        ->assertForbidden();
    Livewire::test(ListPosts::class)
        ->assertForbidden();
});
