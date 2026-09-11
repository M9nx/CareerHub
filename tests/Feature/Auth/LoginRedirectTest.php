<?php

use App\Models\User;

test('employer is redirected to employer dashboard after login', function () {
    $user = User::factory()->employer()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('employer.dashboard'));
});

test('employee is redirected to employee dashboard after login', function () {
    $user = User::factory()->employee()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('employee.dashboard'));
});

test('super admin is redirected to default dashboard after login', function () {
    $user = User::factory()->superAdmin()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));
});
