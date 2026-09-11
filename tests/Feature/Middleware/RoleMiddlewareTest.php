<?php

test('employer cannot access employee dashboard', function () {
    actingAsEmployer();

    $this->get('/employee/dashboard')
        ->assertForbidden();
});

test('employee cannot access employer dashboard', function () {
    actingAsEmployee();

    $this->get('/employer/dashboard')
        ->assertForbidden();
});

test('employer can access employer dashboard', function () {
    actingAsEmployer();

    $this->get('/employer/dashboard')
        ->assertSuccessful();
});

test('employee can access employee dashboard', function () {
    actingAsEmployee();

    $this->get('/employee/dashboard')
        ->assertSuccessful();
});

test('guest is redirected to login from employer dashboard', function () {
    $this->get('/employer/dashboard')
        ->assertRedirect('/login');
});

test('guest is redirected to login from employee dashboard', function () {
    $this->get('/employee/dashboard')
        ->assertRedirect('/login');
});
