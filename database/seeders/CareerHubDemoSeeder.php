<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CareerHubDemoSeeder extends Seeder
{
    public const PASSWORD = 'password';

    /**
     * Seed a fixed demo dataset for local walkthroughs.
     *
     * Demo logins (password: "password"):
     * - admin@careerhub.test (SuperAdmin)
     * - acme@careerhub.test, globex@careerhub.test (Employers)
     * - alice@careerhub.test, bob@careerhub.test, cara@careerhub.test (Employees)
     */
    public function run(): void
    {
        $acme = $this->user('Acme Hiring', 'acme@careerhub.test', UserRole::Employer);
        $globex = $this->user('Globex Recruiting', 'globex@careerhub.test', UserRole::Employer);

        $alice = $this->user('Alice Employee', 'alice@careerhub.test', UserRole::Employee);
        $bob = $this->user('Bob Employee', 'bob@careerhub.test', UserRole::Employee);
        $cara = $this->user('Cara Employee', 'cara@careerhub.test', UserRole::Employee);

        $this->user('Admin CareerHub', 'admin@careerhub.test', UserRole::SuperAdmin);

        EmployerProfile::query()->updateOrCreate(
            ['user_id' => $acme->id],
            ['company_name' => 'Acme Robotics'],
        );

        EmployerProfile::query()->updateOrCreate(
            ['user_id' => $globex->id],
            ['company_name' => 'Globex Logistics'],
        );

        EmployeeProfile::query()->updateOrCreate(
            ['user_id' => $alice->id],
            ['cv_path' => 'cvs/demo-alice.pdf', 'application_image_path' => null],
        );

        EmployeeProfile::query()->updateOrCreate(
            ['user_id' => $bob->id],
            ['cv_path' => 'cvs/demo-bob.pdf', 'application_image_path' => 'application-images/demo-bob.jpg'],
        );

        EmployeeProfile::query()->updateOrCreate(
            ['user_id' => $cara->id],
            ['cv_path' => '', 'application_image_path' => null],
        );

        $acmeLaravel = $this->job(
            $acme,
            'Senior Laravel Engineer',
            'Build CareerHub features with Laravel, Pest, and Filament.',
            JobPostingStatus::Published,
        );

        $this->job(
            $acme,
            'Product Designer (Draft)',
            'Draft role reserved for upcoming hiring season.',
            JobPostingStatus::Draft,
        );

        $globexSupport = $this->job(
            $globex,
            'Customer Success Associate',
            'Support employers and employees through onboarding.',
            JobPostingStatus::Published,
        );

        $globexClosed = $this->job(
            $globex,
            'Operations Analyst (Closed)',
            'This role is closed and kept for CRM history.',
            JobPostingStatus::Closed,
        );

        $this->application($alice, $acmeLaravel, ApplicationStatus::Submitted, 'Excited to join Acme Robotics.');
        $this->application($bob, $acmeLaravel, ApplicationStatus::UnderReview, 'I have shipped Laravel apps at scale.');
        $this->application($cara, $globexSupport, ApplicationStatus::Accepted, 'Customer success is my focus.');
        $this->application($alice, $globexSupport, ApplicationStatus::Rejected, 'Open to support leadership tracks.');
        $this->application($bob, $globexClosed, ApplicationStatus::Cancelled, 'Withdrawing after accepting another offer.', cancelled: true);

        $this->post(
            $acme,
            UserRole::Employer,
            'Acme is hiring Laravel engineers',
            'We are growing the CareerHub platform team. Apply through published roles.',
            PostStatus::Published,
        );

        $this->post(
            $alice,
            UserRole::Employee,
            'Tips for a strong cover letter',
            'Keep it specific to the job, show outcomes, and keep it under half a page.',
            PostStatus::Published,
        );

        $this->post(
            $bob,
            UserRole::Employee,
            'How I prepare for technical screens',
            'I revisit recent projects, practice explaining trade-offs, and review core Laravel patterns.',
            PostStatus::Published,
        );

        $this->post(
            $globex,
            UserRole::Employer,
            'Draft: Globex culture note',
            'Internal draft that should not appear on the shared feed yet.',
            PostStatus::Draft,
        );

        $this->post(
            $cara,
            UserRole::Employee,
            'Hidden moderation sample',
            'Sample post kept Hidden for SuperAdmin moderation walkthroughs.',
            PostStatus::Hidden,
        );

        $this->command?->info('CareerHub demo data ready. Password for all demo users: '.self::PASSWORD);
    }

    private function user(string $name, string $email, UserRole $role): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => now(),
                'role' => $role,
                'is_active' => true,
                'is_blocked_from_posts' => false,
            ],
        );
    }

    private function job(
        User $employer,
        string $title,
        string $description,
        JobPostingStatus $status,
    ): JobPosting {
        return JobPosting::query()->updateOrCreate(
            [
                'employer_id' => $employer->id,
                'title' => $title,
            ],
            [
                'description' => $description,
                'status' => $status,
                'is_active' => true,
                'published_at' => $status === JobPostingStatus::Draft ? null : now(),
            ],
        );
    }

    private function application(
        User $employee,
        JobPosting $job,
        ApplicationStatus $status,
        string $coverLetter,
        bool $cancelled = false,
    ): Application {
        return Application::query()->updateOrCreate(
            [
                'employee_id' => $employee->id,
                'job_posting_id' => $job->id,
            ],
            [
                'status' => $status,
                'is_active' => true,
                'cover_letter' => $coverLetter,
                'cancelled_at' => $cancelled ? now()->subDay() : null,
            ],
        );
    }

    private function post(
        User $author,
        UserRole $authorRole,
        string $title,
        string $body,
        PostStatus $status,
    ): Post {
        return Post::query()->updateOrCreate(
            [
                'author_id' => $author->id,
                'title' => $title,
            ],
            [
                'author_role' => $authorRole,
                'body' => $body,
                'status' => $status,
                'is_active' => true,
            ],
        );
    }
}
