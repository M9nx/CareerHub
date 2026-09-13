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

test('super admin cannot authenticate through breeze login', function () {
    $user = User::factory()->superAdmin()->create();

    $this->from('/login')
        ->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect('/login')
        ->assertSessionHasErrors([
            'email' => 'Please sign in at '.url('/super-admin/login').'.',
        ]);

    $this->assertGuest();
});

test('super admin with invalid password sees generic credentials error', function () {
    $user = User::factory()->superAdmin()->create();

    $this->from('/login')
        ->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->assertRedirect('/login')
        ->assertSessionHasErrors([
            'email' => trans('auth.failed'),
        ]);

    $this->assertGuest();
});

test('super admin web session is redirected from dashboard to filament', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect('/super-admin');
});
