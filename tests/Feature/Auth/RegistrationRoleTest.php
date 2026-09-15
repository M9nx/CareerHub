<?php

use App\Enums\UserRole;
use App\Models\User;

test('employer registration persists employer role', function () {
    $this->post('/register', [
        'name' => 'Acme Corp',
        'email' => 'employer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'Employer',
    ])->assertRedirect(route('feed.index', absolute: false));

    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'email' => 'employer@example.com',
        'role' => UserRole::Employer->value,
    ]);

    expect(User::where('email', 'employer@example.com')->first()?->role)
        ->toBe(UserRole::Employer);
});

test('employee registration persists employee role', function () {
    $this->post('/register', [
        'name' => 'Jane Applicant',
        'email' => 'employee@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'Employee',
    ])->assertRedirect(route('feed.index', absolute: false));

    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'email' => 'employee@example.com',
        'role' => UserRole::Employee->value,
    ]);

    expect(User::where('email', 'employee@example.com')->first()?->role)
        ->toBe(UserRole::Employee);
});

test('super admin role cannot be registered', function () {
    $this->from('/register')
        ->post('/register', [
            'name' => 'Bad Actor',
            'email' => 'bad@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'SuperAdmin',
        ])
        ->assertRedirect('/register')
        ->assertSessionHasErrors('role');

    $this->assertGuest();

    $this->assertDatabaseMissing('users', [
        'email' => 'bad@example.com',
    ]);
});

test('registration requires a role', function () {
    $this->from('/register')
        ->post('/register', [
            'name' => 'No Role User',
            'email' => 'norole@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertRedirect('/register')
        ->assertSessionHasErrors('role');

    $this->assertGuest();
});
