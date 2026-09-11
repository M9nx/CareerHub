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

test('employer cannot access super admin dashboard', function () {
    $employer = User::factory()->employer()->create([
        'email' => 'employer@careerhub.test',
    ]);

    $this->actingAs($employer, 'filament')
        ->get('/super-admin')
        ->assertForbidden();
});

test('employee cannot access super admin dashboard', function () {
    $employee = User::factory()->employee()->create([
        'email' => 'employee@careerhub.test',
    ]);

    $this->actingAs($employee, 'filament')
        ->get('/super-admin')
        ->assertForbidden();
});

test('super admin can access super admin dashboard', function () {
    $superAdmin = User::factory()->superAdmin()->create([
        'email' => 'superadmin@careerhub.test',
    ]);

    $this->actingAs($superAdmin, 'filament')
        ->get('/super-admin')
        ->assertSuccessful();
});
