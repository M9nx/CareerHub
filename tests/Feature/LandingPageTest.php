<?php

use App\Models\User;

test('landing page media is self hosted and present on disk', function () {
    $paths = collect([
        config('landing.hero.video'),
        config('landing.hero.poster'),
        config('landing.showcase.video'),
        config('landing.showcase.poster'),
        config('landing.showcase.captions'),
    ])->merge(collect(config('landing.features'))->flatMap(
        fn (array $feature): array => array_filter([
            $feature['image'] ?? null,
            $feature['video'] ?? null,
            $feature['poster'] ?? null,
        ])
    ));

    expect($paths)->not->toBeEmpty();

    $paths->each(function (string $path): void {
        expect($path)->not->toStartWith('http');
        expect(public_path($path))->toBeFile();
    });
});

test('landing page exposes social and structured metadata', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('property="og:image"', false)
        ->assertSee('name="twitter:card"', false)
        ->assertSee('rel="canonical"', false)
        ->assertSee('application/ld+json', false);
});

test('landing page stays readable without javascript', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('<noscript>', false)
        ->assertSee('.landing-reveal { opacity: 1 !important', false);
});

test('guest can view the careerhub landing page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Register as Employer')
        ->assertSee('Register as Employee')
        ->assertSee('One platform for hiring teams and job seekers')
        ->assertSee('data-landing-loader', false)
        ->assertSee('landing-bento', false)
        ->assertSee('landing-aurora-band', false)
        ->assertSee('id="features"', false)
        ->assertSee('id="showcase"', false)
        ->assertSee('Post jobs', false)
        ->assertSee('Apply &amp; track', false)
        ->assertSee('Shared community feed', false)
        ->assertDontSee('/super-admin/login');
});

test('landing page links preselect employer registration role', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('register', ['role' => 'Employer']), false);
});

test('landing page links preselect employee registration role', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('register', ['role' => 'Employee']), false);
});

test('authenticated employer is redirected from home to feed', function () {
    $user = User::factory()->employer()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertRedirect(route('feed.index'));
});

test('authenticated employee is redirected from home to feed', function () {
    $user = User::factory()->employee()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertRedirect(route('feed.index'));
});

test('register page honors role query parameter from landing ctas', function () {
    $this->get(route('register', ['role' => 'Employee']))
        ->assertOk()
        ->assertSee('value="Employee"', false)
        ->assertSee('checked', false);
});
