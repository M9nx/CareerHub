<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('user can upload a cover photo on professional profile', function () {
    Storage::fake('public');
    $user = actingAsEmployee();

    $this->patch(route('profile.professional.update'), [
        'cover' => UploadedFile::fake()->image('cover.jpg', 1200, 400),
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $user->refresh();

    expect($user->cover_path)->not->toBeNull()
        ->and($user->coverUrl())->not->toBeNull();

    Storage::disk('public')->assertExists($user->cover_path);
});

test('cover photo appears on the public profile', function () {
    Storage::fake('public');
    $viewer = actingAsEmployee();
    $person = User::factory()->employee()->create([
        'name' => 'Cover Person',
        'cover_path' => 'covers/demo-cover.jpg',
    ]);
    Storage::disk('public')->put('covers/demo-cover.jpg', 'fake-image');

    $this->get(route('people.show', $person))
        ->assertOk()
        ->assertSee('Cover Person')
        ->assertSee(Storage::disk('public')->url('covers/demo-cover.jpg'), false);
});

test('cover upload rejects unsupported file types', function () {
    Storage::fake('public');
    actingAsEmployee();

    $this->from(route('profile.edit'))
        ->patch(route('profile.professional.update'), [
            'cover' => UploadedFile::fake()->create('cover.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors('cover');
});
