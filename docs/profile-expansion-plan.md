# Profile Expansion Plan

**Status:** Approved for Milestone 3 implementation (CH-DOC-003)  
**Date:** 2026-09-15  
**Companion:** [`linkedin-gap-analysis.md`](./linkedin-gap-analysis.md), [`ui-architecture.md`](./ui-architecture.md)

## Current schema (verified)

| Table | Columns today |
|-------|----------------|
| `users` | `name`, `email`, `role`, `is_active`, `is_blocked_from_posts`, … |
| `employee_profiles` | `user_id`, `cv_path`, `application_image_path` |
| `employer_profiles` | `user_id`, `company_name` |

**Missing before M3:** headline, location, about, avatar/cover, experience, education, skills, company industry/size/website.

## Decisions

| Question | Decision |
|----------|----------|
| Avatar / headline ownership | **`users`** — shared professional identity for both roles |
| Company fields | **`employer_profiles`** — industry, size, location, website, about, logo |
| Public visibility | Authenticated Employer/Employee may view other **active** Employer/Employee profiles |
| Email on public profile | **Hidden** |
| CV / application image on public profile | **Hidden** (private career documents) |
| Fake metrics | **Never** (no profile views / impressions) |
| Experience / education / skills | **Shipped** in CH-PROF-004 (Milestone 3b) |

## Stage migrations

### Stage 1 — CH-PROF-001 Minimum identity (`users`)

```php
$table->string('headline')->nullable();
$table->string('location')->nullable();
$table->text('about')->nullable();
$table->string('avatar_path')->nullable();
```

Rollback: drop the four columns.

### Stage 2 — CH-PROF-003 Employer company (`employer_profiles`)

```php
$table->string('industry')->nullable();
$table->string('company_size')->nullable();
$table->string('location')->nullable();
$table->string('website')->nullable();
$table->text('about')->nullable();
$table->string('logo_path')->nullable();
```

Rollback: drop the six columns. `company_name` remains required for posting context.

### Stage 3 — CH-PROF-002 Public profile

- Route: `GET /people/{user}` → `people.show`
- Policy: `UserPolicy::view` allows active peer Employer/Employee
- Blade: Swiss `x-app.page` show surface (avatar, headline, location, about, company block, recent posts)

### Stage 4 — CH-PROF-004 Career sections (shipped)

Tables:

- `profile_experiences` (`user_id`, title, company, location, started_at/ended_at, description)
- `profile_educations` (`user_id`, school, degree, field, started_at/ended_at)
- `profile_skills` (`user_id`, name, sort)

Owner CRUD on `/profile`; public read on `/people/{user}`.

## Application touch points

| Area | Change |
|------|--------|
| `ProfileUpdateRequest` / professional form | Edit headline, location, about, avatar |
| `UpdateEmployerProfileRequest` | Company enrichment fields + logo |
| Feed `profile-summary` / completion checks | Show headline/location/avatar; completion includes new fields |
| Timeline avatars | Prefer `avatar_path` when present |

## Rules

- Nullable columns only (safe for existing rows).  
- Store avatars/logos on the `public` disk via `LocalDocumentStorage`.  
- No cover image in Milestone 3 (defer with experience tables).  
- Update gap analysis when Stage 4 ships.
