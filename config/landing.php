<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CareerHub public landing page — pattern-driven content
    |--------------------------------------------------------------------------
    |
    | Patterns (issue #84):
    | - Page structure: Minimalism & Swiss Style
    | - Feature modules: Bento Box Grid (mid-page, 3 tiles)
    | - Hero accent: Aurora UI (top band, headline + CTAs only)
    |
    | All media is self-hosted under public/media/landing so the page has no
    | third-party CDN dependency. Replace files in place to swap creative.
    |
    | @see https://www.uistyleguide.com/style/minimalism-swiss-style
    | @see https://www.uistyleguide.com/style/bento-box-grid
    | @see https://www.uistyleguide.com/style/aurora-ui
    |
    */

    'meta' => [
        'title' => 'CareerHub — Hire faster. Grow your career.',
        'description' => 'One platform for employers to post jobs and review applicants, and for employees to apply, track progress, and join a shared professional feed.',
        'social_image' => 'media/landing/hero-poster.jpg',
    ],

    'hero' => [
        'index' => '01',
        'eyebrow' => 'Careers, simplified',
        'headline' => 'One platform for hiring teams and job seekers',
        'subheadline' => 'Publish roles, move candidates through review, and stay visible in a shared feed — without switching tools.',
        'video' => 'media/landing/hero.mp4',
        'poster' => 'media/landing/hero-poster.jpg',
        'video_alt' => 'Colleagues walking and talking through a modern office',
        'overlay_label' => 'Live pipeline',
        'overlay_value' => 'Review → Offer',
    ],

    /**
     * Placeholder demo figures. Replace with real platform metrics before launch.
     */
    'stats' => [
        ['value' => 500, 'suffix' => '+', 'label' => 'Job postings managed'],
        ['value' => 2000, 'suffix' => '+', 'label' => 'Applications tracked'],
        ['value' => 120, 'suffix' => '+', 'label' => 'Companies hiring'],
    ],

    'trust' => [
        'Acme Labs',
        'Northwind',
        'Brightpath',
        'Studio 14',
        'Helix HR',
        'Urban Hire',
    ],

    /**
     * Bento Box Grid: exactly one 2x2 anchor tile, then supporting tiles.
     * The anchor tile plays a muted looping video; the rest use stills.
     */
    'features' => [
        [
            'key' => '01',
            'title' => 'Post jobs',
            'description' => 'Employers control visibility, status, and lifecycle for every opening from one pipeline.',
            'span' => 'hero',
            'video' => 'media/landing/collaboration.mp4',
            'poster' => 'media/landing/collaboration-poster.jpg',
            'image' => 'media/landing/feature-jobs.jpg',
        ],
        [
            'key' => '02',
            'title' => 'Apply & track',
            'description' => 'Candidates submit once and follow every stage — no chasing, no duplicate forms.',
            'span' => 'standard',
            'image' => 'media/landing/feature-apply.jpg',
        ],
        [
            'key' => '03',
            'title' => 'Shared community feed',
            'description' => 'Employers and employees stay visible on one professional surface between hires.',
            'span' => 'standard',
            'image' => 'media/landing/feature-feed.jpg',
        ],
    ],

    'showcase' => [
        'index' => '03',
        'eyebrow' => 'Product tour',
        'headline' => 'See the workspace in motion',
        'body' => 'A short walkthrough of how hiring teams and candidates collaborate inside CareerHub.',
        'video' => 'media/landing/tour.mp4',
        'poster' => 'media/landing/tour-poster.jpg',
        'captions' => 'media/landing/tour.vtt',
        'highlights' => [
            'Stage-by-stage applicant review',
            'Shared feed for employers and candidates',
            'Role-aware dashboards on one account',
        ],
    ],

    'workflow' => [
        [
            'step' => '01',
            'title' => 'Register',
            'description' => 'Choose Employer or Employee and create your account in minutes.',
        ],
        [
            'step' => '02',
            'title' => 'Act',
            'description' => 'Post jobs, submit applications, or publish updates to the feed.',
        ],
        [
            'step' => '03',
            'title' => 'Outcome',
            'description' => 'Close hires with clear status for teams and applicants alike.',
        ],
    ],

    'navigation' => [
        ['label' => 'Features', 'href' => '#features'],
        ['label' => 'Tour', 'href' => '#showcase'],
        ['label' => 'Workflow', 'href' => '#workflow'],
    ],

];
