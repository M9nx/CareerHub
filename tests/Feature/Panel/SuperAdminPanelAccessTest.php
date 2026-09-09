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
    $employer = User::factory()->create([
        'email' => 'employer@careerhub.test',
    ]);

    $this->actingAs($employer, 'filament')
        ->get('/super-admin')
        ->assertForbidden();
});

test('user who can access panel reaches super admin dashboard', function () {
    // Interim: uses current canAccessPanel() until P0-Mariam (#11) adds UserRole.
    $superAdmin = User::factory()->create([
        'email' => 'superadmin@gmail.com',
    ]);

    $this->actingAs($superAdmin, 'filament')
        ->get('/super-admin')
        ->assertSuccessful();
});
