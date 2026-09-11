<?php

namespace Tests\Feature\Application;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_factory_persists_with_submitted_status(): void
    {
        $application = Application::factory()->create();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'submitted',
        ]);
    }
}