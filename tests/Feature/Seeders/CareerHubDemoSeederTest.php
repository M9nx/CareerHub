<?php

use App\Enums\ApplicationStatus;
use App\Enums\JobPostingStatus;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\EmployeeProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\CareerHubDemoSeeder;
use Illuminate\Support\Facades\Hash;

test('career hub demo seeder creates the expected demo dataset', function () {
    $this->seed(CareerHubDemoSeeder::class);

    expect(User::query()->count())->toBeGreaterThanOrEqual(6)
        ->and(User::query()->where('role', UserRole::SuperAdmin)->count())->toBe(1)
        ->and(User::query()->where('role', UserRole::Employer)->count())->toBe(2)
        ->and(User::query()->where('role', UserRole::Employee)->count())->toBe(3)
        ->and(EmployerProfile::query()->count())->toBe(2)
        ->and(EmployeeProfile::query()->count())->toBe(3)
        ->and(JobPosting::query()->count())->toBeGreaterThanOrEqual(4)
        ->and(Application::query()->count())->toBeGreaterThanOrEqual(5)
        ->and(Post::query()->published()->count())->toBeGreaterThanOrEqual(3);

    expect(User::query()->where('email', 'admin@careerhub.test')->first())
        ->not->toBeNull()
        ->role->toBe(UserRole::SuperAdmin);

    expect(Hash::check(
        CareerHubDemoSeeder::PASSWORD,
        User::query()->where('email', 'alice@careerhub.test')->value('password'),
    ))->toBeTrue();

    expect(JobPosting::query()->where('status', JobPostingStatus::Published)->count())->toBeGreaterThanOrEqual(2)
        ->and(Application::query()->where('status', ApplicationStatus::Cancelled)->exists())->toBeTrue()
        ->and(Post::query()->where('status', PostStatus::Draft)->exists())->toBeTrue()
        ->and(Post::query()->where('status', PostStatus::Hidden)->exists())->toBeTrue();
});

test('career hub demo seeder is idempotent for demo emails', function () {
    $this->seed(CareerHubDemoSeeder::class);
    $this->seed(CareerHubDemoSeeder::class);

    expect(User::query()->whereIn('email', [
        'admin@careerhub.test',
        'acme@careerhub.test',
        'globex@careerhub.test',
        'alice@careerhub.test',
        'bob@careerhub.test',
        'cara@careerhub.test',
    ])->count())->toBe(6)
        ->and(User::query()->count())->toBe(6)
        ->and(JobPosting::query()->count())->toBe(4)
        ->and(Application::query()->count())->toBe(5)
        ->and(Post::query()->count())->toBe(5);
});

test('database seeder calls the demo seeder in local and testing', function () {
    $this->seed();

    expect(User::query()->where('email', 'admin@careerhub.test')->exists())->toBeTrue()
        ->and(User::query()->count())->toBeGreaterThanOrEqual(6);
});
