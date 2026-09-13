<?php

use App\Models\Application;
use App\Models\ApplicationNote;
use App\Models\User;
use App\Services\ActivityLogger;

test('activity logger persists a log entry', function () {
    $causer = User::factory()->superAdmin()->create();
    $subject = User::factory()->employee()->create();

    $log = app(ActivityLogger::class)->log(
        'user.updated',
        $subject,
        $causer,
        ['role' => 'employee'],
    );

    expect($log->log_name)->toBe('user.updated')
        ->and($log->subject->is($subject))->toBeTrue()
        ->and($log->causer->is($causer))->toBeTrue();

    $this->assertDatabaseHas('activity_logs', [
        'id' => $log->id,
        'log_name' => 'user.updated',
        'description' => 'user.updated',
        'causer_id' => $causer->id,
        'subject_id' => $subject->id,
    ]);
});

test('super admin can view activity logs in filament', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $subject = User::factory()->employee()->create();

    app(ActivityLogger::class)->log(
        'user.updated',
        $subject,
        $superAdmin,
        ['role' => 'employee'],
    );

    $this->actingAs($superAdmin, 'filament')
        ->get('/super-admin/activity-logs')
        ->assertSuccessful()
        ->assertSee('user.updated')
        ->assertSee($superAdmin->name);
});

test('employer cannot view activity logs in filament', function () {
    $employer = User::factory()->employer()->create();

    $this->actingAs($employer, 'filament')
        ->get('/super-admin/activity-logs')
        ->assertForbidden();
});

test('application notes persist against an application and author', function () {
    $author = User::factory()->superAdmin()->create();
    $note = ApplicationNote::query()->create([
        'application_id' => Application::factory()->create()->id,
        'author_id' => $author->id,
        'body' => 'Follow up with the candidate.',
    ]);

    expect($note->author->is($author))->toBeTrue()
        ->and($note->application)->not->toBeNull();

    $this->assertDatabaseHas('application_notes', [
        'id' => $note->id,
        'author_id' => $author->id,
        'body' => 'Follow up with the candidate.',
    ]);
});
