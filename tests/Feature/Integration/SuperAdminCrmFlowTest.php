<?php

use App\Enums\JobPostingStatus;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Filament\SuperAdmin\Resources\PostModerationResource\Pages\ListPosts;
use App\Filament\SuperAdmin\Resources\UserResource\Pages\ListUsers;
use App\Models\ActivityLog;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
});

test('super admin sees activity logs after job posting and moderation workflows', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $employer = actingAsEmployer();
    $employee = User::factory()->employee()->create(['name' => 'CRM Flow Employee']);

    $this->post(route('employer.jobs.store'), [
        'title' => 'CRM Tracked Role',
        'description' => 'Role used to verify CRM activity logging.',
        'status' => JobPostingStatus::Draft->value,
    ])->assertRedirect(route('employer.jobs.index'));

    $job = JobPosting::query()->where('title', 'CRM Tracked Role')->firstOrFail();

    $this->post(route('employer.jobs.publish', $job))
        ->assertRedirect(route('employer.jobs.edit', $job));

    $post = Post::factory()->for($employee, 'author')->published()->create([
        'author_role' => UserRole::Employee,
        'title' => 'CRM Moderation Post',
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListPosts::class)
        ->callAction(TestAction::make('hide')->table($post))
        ->assertNotified();

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('blockFromPosts')->table($employee))
        ->assertSuccessful();

    $this->get('/super-admin/activity-logs')
        ->assertSuccessful()
        ->assertSee('job_posting.created')
        ->assertSee('job_posting.published')
        ->assertSee('post.hidden')
        ->assertSee('user.blocked_from_posts');

    expect(ActivityLog::query()->where('log_name', 'job_posting.created')->exists())->toBeTrue()
        ->and(ActivityLog::query()->where('log_name', 'job_posting.published')->exists())->toBeTrue()
        ->and(ActivityLog::query()->where('log_name', 'post.hidden')->exists())->toBeTrue()
        ->and(ActivityLog::query()->where('log_name', 'user.blocked_from_posts')->exists())->toBeTrue()
        ->and($post->fresh()->status)->toBe(PostStatus::Hidden)
        ->and($employee->fresh()->isBlockedFromPosts())->toBeTrue();
});
