<?php

use App\Models\User;

test('super admin login page is reachable at super-admin path', function () {
    $this->get('/super-admin/login')
        ->assertSuccessful();
});

test('legacy admin panel route is not registered', function () {
    $this->get('/admin/login')
        ->assertNotFound();
});

test('user who cannot access panel receives forbidden on super admin dashboard', function () {
    $employer = User::factory()->employer()->create([
        'email' => 'employer@careerhub.test',
    ]);

    $this->actingAs($employer, 'filament')
        ->get('/super-admin')
        ->assertForbidden();
});

test('user who can access panel reaches super admin dashboard', function () {
    $superAdmin = User::factory()->superAdmin()->create([
        'email' => 'superadmin@careerhub.test',
    ]);

    $this->actingAs($superAdmin, 'filament')
        ->get('/super-admin')
        ->assertSuccessful();
});
