# CareerHub Codebase Audit

**Date:** 2026-09-15  
**Branch:** `feat/#99-timeline-feed`  
**Scope:** Full application structure, domain, routes, frontend, and view layouts

---

## 1. Executive summary

CareerHub is a multi-persona Laravel job/career platform (Employer, Employee, SuperAdmin) with a shared timeline feed, applications workflow, and Filament admin.

**UI status (layouts):**

| Surface | Layout | Design system |
|---------|--------|---------------|
| Public landing `/` | `x-landing.layout` | Swiss (IBM Plex, `--landing-*`) |
| Authenticated app | `x-app.page` → `x-app.shell-layout` | Swiss app shell |
| Auth (login/register/…) | `x-guest-layout` | Legacy Breeze (Figtree, gray/indigo) |
| Super Admin | Filament `/super-admin` | Filament |

Authenticated pages have been migrated off Breeze layout. Auth guest pages and several form/partials still use Breeze primitives inside (or instead of) the Swiss shell.

---

## 2. Stack & versions

| Item | Value |
|------|--------|
| PHP (runtime) | 8.5.10 |
| Composer PHP constraint | `^8.3` |
| Laravel | `^13.17` (`laravel/framework`) |
| Filament | `^5.8` |
| Filament Breezy | `^3.2` (2FA) |
| Laravel Breeze | `^2.4` (dev — auth scaffolding) |
| Pest | `^5.1` |
| Vite | `^8` |
| Tailwind | `^3.1` (+ unused `@tailwindcss/vite ^4` in `package.json`) |
| Alpine.js | `^3.4` |

---

## 3. Directory map

### `app/`

| Area | Role |
|------|------|
| `Actions/` | Domain actions (react, share, attachments, application transitions, moderation) |
| `Enums/` | `UserRole`, `ApplicationStatus`, `JobPostingStatus`, `PostStatus`, `PostReactionType` |
| `Filament/SuperAdmin/` | Super-admin resources, pages, widgets |
| `Http/Controllers/Auth/` | Breeze auth controllers |
| `Http/Controllers/Employer/` | Dashboard, jobs, applications, posts, profile |
| `Http/Controllers/Employee/` | Dashboard, jobs browse, applications, posts, profile |
| `Http/Controllers/` | `FeedController`, `ProfileController` |
| `Http/Middleware/` | `EnsureEmployer`, `EnsureEmployee` |
| `Http/Requests/` | Auth, Feed, Employer, Employee form requests |
| `Models/` | See §5 |
| `Policies/` | Application, JobPosting, Post, User |
| `Providers/` + `Providers/Filament/` | App + SuperAdmin panel |
| `Services/` | `ActivityLogger`, `LocalDocumentStorage` |
| `Support/Timeline/` | `TimelineQuery`, `TimelineItem`, `TimelineItemType` |
| `View/Components/` | `AppLayout`, `GuestLayout`, `App/ShellLayout`, `App/Page` |
| `Notifications/`, `Observers/` | Application status notify; Post/JobPosting observers |

### `resources/`

- `css/` — `app.css` imports `landing.css` (Swiss + app shell tokens)
- `js/` — `app.js` (Alpine), `landing.js` (landing motion)
- `views/` — auth, components, employee, employer, feed, layouts, profile, landing

### `routes/`

| File | Purpose |
|------|---------|
| `web.php` | `/`, dashboard redirect, feed, profile; requires `auth.php` |
| `auth.php` | Breeze auth routes |
| `employer.php` | Employer persona (`auth` + `employer`) |
| `employee.php` | Employee persona (`auth` + `employee`) |
| `console.php` | Artisan schedule/console |

Persona routes are loaded from `bootstrap/app.php` via a `then:` callback.

### `tests/`

~**63** `*Test.php` files — mostly Feature (`Post`, `Auth`, `Application`, `JobPosting`, `Profile`, `SuperAdmin`, `Crm`, `Integration`, …) plus one Unit example.

---

## 4. View layouts audit (critical)

### 4.1 Layout decision matrix

```
Landing     → x-landing.layout              → Swiss marketing
Auth guest  → x-guest-layout → layouts.guest → Breeze (legacy)
App pages   → x-app.page → x-app.shell-layout → Swiss app
              (x-app-layout unused by views)
Admin       → Filament /super-admin           → Filament
Dead        → layouts/app + layouts/navigation + employer-nav / employee-nav
```

### 4.2 Layout & shell files

#### Active — Swiss app shell

| Path | Role |
|------|------|
| `resources/views/components/app/shell-layout.blade.php` | HTML document: IBM Plex, `landing-shell`, CSRF, Vite, `<x-app.header>` |
| `resources/views/components/app/page.blade.php` | Page chrome: eyebrow, title, actions slot, column width (`narrow` / `wide`) |
| `resources/views/components/app/header.blade.php` | Sticky role-based nav + mobile menu |
| `resources/views/components/app/user-menu.blade.php` | Profile / logout dropdown |
| `resources/views/components/app/flash.blade.php` | Success / error flash |

**PHP:** `App\View\Components\App\ShellLayout`, `App\View\Components\App\Page`

#### Active — Landing

| Path | Role |
|------|------|
| `resources/views/components/landing/layout.blade.php` | Public HTML shell + SEO / OG |
| `resources/views/components/landing/{header,footer,hero,stats,features-bento,showcase,workflow,loader,section-heading}.blade.php` | Landing sections |

#### Active — Breeze guest (auth only)

| Path | Role |
|------|------|
| `resources/views/layouts/guest.blade.php` | Guest card layout (Figtree, gray canvas) |
| `resources/views/auth/*.blade.php` | All six auth screens use `<x-guest-layout>` |

**PHP:** `App\View\Components\GuestLayout` → `layouts.guest`

#### Orphaned / dead Breeze app shell

| Path | Status |
|------|--------|
| `resources/views/layouts/app.blade.php` | **Orphaned** — classic Breeze shell; not used by any live view |
| `resources/views/layouts/navigation.blade.php` | **Orphaned** — only included by `layouts/app` |
| `resources/views/components/employer-nav.blade.php` | **Dead at runtime** (still referenced in one test) |
| `resources/views/components/employee-nav.blade.php` | **Dead at runtime** (still referenced in one test) |

**Note:** `App\View\Components\AppLayout` now renders `components.app.shell-layout`, but **no Blade view uses `<x-app-layout>`**. Live authenticated pages use `<x-app.page>` directly.

### 4.3 Views → layout mapping

#### `x-app.page` (authenticated Swiss)

| View | Notes |
|------|-------|
| `dashboard.blade.php` | Fallback |
| `employer/dashboard.blade.php` | |
| `employer/jobs/{index,create,edit}.blade.php` | |
| `employer/applications/{index,show}.blade.php` | |
| `employer/posts/{index,create,edit}.blade.php` | |
| `employee/dashboard.blade.php` | |
| `employee/jobs/{index,show}.blade.php` | |
| `employee/applications/{index,show}.blade.php` | |
| `employee/posts/{index,create,edit}.blade.php` | |
| `feed/index.blade.php` | `narrow` |
| `profile/edit.blade.php` | Includes role profile partials |

#### `x-guest-layout` (Breeze auth)

- `auth/login.blade.php`
- `auth/register.blade.php`
- `auth/forgot-password.blade.php`
- `auth/reset-password.blade.php`
- `auth/confirm-password.blade.php`
- `auth/verify-email.blade.php`

#### `x-landing.layout`

- `landing.blade.php`

### 4.4 Breeze primitives still used inside Swiss pages

These components remain Breeze-styled (`gray-*`, indigo focus rings) and are still pulled into Swiss-shell pages:

| Component | Typical use |
|-----------|-------------|
| `x-text-input`, `x-input-label`, `x-input-error` | Forms |
| `x-primary-button`, `x-secondary-button`, `x-danger-button` | Actions |
| `x-modal` | Delete-account confirm |
| `x-dropdown`, `x-dropdown-link` | (available; app header uses custom menu) |
| `x-nav-link`, `x-responsive-nav-link` | Only via dead Breeze nav |

**Hotspots (mixed design systems):**

| File | Issue |
|------|--------|
| `profile/partials/update-*-form.blade.php` | `app-form` + Breeze inputs/buttons |
| `profile/partials/delete-user-form.blade.php` | Full Breeze modal + danger button |
| `employer/profile/edit.blade.php` | Gray headings + Breeze controls |
| `employee/profile/edit.blade.php` | Indigo links, Breeze file inputs |
| `employee/applications/partials/timeline.blade.php` | Indigo timeline dots |
| `employee/applications/partials/status-badge.blade.php` | Non-Swiss color pills |
| Job/post create & edit | `app-card` + mix of native/`x-text-input` |

### 4.5 Unused / leftover views

| Path | Notes |
|------|--------|
| `feed/partials/post-card.blade.php` | Unused — feed uses `timeline-item.blade.php` |
| `layouts/app.blade.php` + `navigation.blade.php` | Dead Breeze shell |
| `components/employer-nav.blade.php` / `employee-nav.blade.php` | Dead nav; test still asserts against them |

### 4.6 Fonts & tokens

| Context | Font | Tokens |
|---------|------|--------|
| Landing + app shell | IBM Plex Sans | `--landing-*`, `.swiss-*`, `.app-*` |
| Guest auth | Figtree (Bunny) | Tailwind `gray-*` / indigo |
| Tailwind config | IBM Plex Sans / Hanken Grotesk as `font-sans` | — |

Authenticated UI reuses `landing-shell` / `--landing-*` naming (shared CSS by design; naming conflates marketing + app).

---

## 5. Domain model

| Model | Role |
|-------|------|
| `User` | Auth, role, block-from-posts, Filament access |
| `EmployerProfile` | Company name |
| `EmployeeProfile` | CV + application image paths |
| `JobPosting` | Employer jobs (draft/published/closed/archived) |
| `Application` | Employee applications + status |
| `ApplicationNote` | CRM notes |
| `Post` | Feed posts (+ share via `shared_post_id`) |
| `PostAttachment` | Images / PDFs |
| `PostReaction` | Likes |
| `ActivityLog` | Audit / CRM activity |

### Feed timeline (`Support/Timeline`)

- `TimelineQuery` merges published posts + published jobs + role-scoped application events, sorts newest-first, paginates **in memory**
- Feed UI: composer, timeline-item cards, attachments; react + share endpoints on `FeedController`

---

## 6. Routes (named groups)

### Shared (`web.php`)

| Name | Notes |
|------|--------|
| `/` | Landing or role redirect |
| `dashboard` | Role redirect |
| `feed.index`, `feed.store`, `feed.posts.react`, `feed.posts.share` | Auth feed |
| `profile.edit`, `profile.update`, `profile.destroy` | Account |

### Auth (`auth.php`)

Guest: `register`, `login`, `password.request`, `password.reset`, …  
Auth: `verification.*`, `password.confirm`, `password.update`, `logout`

### Employer (`employer.*`)

- `employer.dashboard`
- `employer.profile.edit` / `update`
- `employer.jobs` resource (except show) + `publish` / `close`
- `employer.applications.index` / `show` / `update`
- `employer.posts` resource (except show)

### Employee (`employee.*`)

- `employee.dashboard`
- `employee.profile.edit` / `update` + CV / application-image download
- `employee.jobs.index` / `show`
- `employee.applications.index` / `show` / `store` + `cancel`
- `employee.posts` resource (except show)

### Filament

- Panel: `/super-admin` (`SuperAdminPanelProvider`)
- Legacy `AdminPanelProvider` exists but is **unregistered**

---

## 7. Auth & roles

| Role (`UserRole`) | Access |
|-------------------|--------|
| `employer` | `employer` middleware routes |
| `employee` | `employee` middleware routes |
| `super_admin` | Filament panel; Gate bypass |

- Middleware aliases: `employer` → `EnsureEmployer`, `employee` → `EnsureEmployee`
- User flags: `is_active`, `is_blocked_from_posts`
- Guards: `web` + `filament` (session → users)
- 2FA via Filament Breezy (`TwoFactorAuthenticatable`)

---

## 8. Frontend build

| Entry | Purpose |
|-------|---------|
| `resources/css/app.css` | Imports `landing.css` + Tailwind |
| `resources/css/landing.css` | Swiss + app shell utilities |
| `resources/js/app.js` | Alpine |
| `resources/js/landing.js` | Landing loader / reveals (landing layout only) |

Vite inputs: CSS + both JS files. Tailwind content scans `resources/views/**/*.blade.php`.

---

## 9. Tests overview

| Area | Approx. coverage |
|------|------------------|
| `Post/` | Feed, social, CRUD, moderation |
| `Auth/` | Login, register/roles, password, verification |
| `Application/` | Workflow, cancel, notify, E2E, timeline |
| `JobPosting/` | Employer CRUD, employee browse |
| `Profile/` | Profiles, CV upload |
| `SuperAdmin/` | Users, jobs, block, stats |
| `Crm/` | Activity logs, notes |
| `Integration/` | Cross-persona flows |
| Landing / Profile / Example | Smoke |

**Known flake:** `EmployeeCvUploadTest` — validation assertion on fake upload temp path (intermittent).

**UI test debt:** `EmployeeProfileEditTest` still renders dead `employee-nav` / `employer-nav` instead of `components/app/header`.

---

## 10. Tech debt & recommendations

### P0 — Layout / UI consistency

1. **Keep Breeze on auth only** (current intent) — or migrate guest auth to Swiss/`landing-shell` for one visual language.
2. **Restyle profile partials** (account, password, delete modal, employer/employee profile) to Swiss buttons/inputs; drop Breeze modal or wrap it.
3. **Swiss-ify application timeline + status badge** (remove indigo/gray Breeze leftovers).
4. **Delete or quarantine dead files:** `layouts/app.blade.php`, `layouts/navigation.blade.php`, `employer-nav`, `employee-nav`, `feed/partials/post-card.blade.php`.
5. **Update nav tests** to assert against `x-app.header`.
6. **Clarify `AppLayout`:** either remove the class or document it as unused fallback.

### P1 — Frontend hygiene

7. Remove unused `@tailwindcss/vite` v4 dependency or migrate intentionally.
8. Consider renaming app tokens from `--landing-*` / `landing-shell` to `--app-*` / `app-shell` once auth is aligned (optional).
9. Ensure `npm run build` / `composer run dev` is part of local verify after Blade/CSS changes.

### P2 — Domain / scale

10. `TimelineQuery` in-memory merge + paginate — fine for demo; plan DB-level union/cursor as data grows.
11. Unregister or delete `AdminPanelProvider` remnant.
12. Stabilize `EmployeeCvUploadTest` mime/validation expectations.

---

## 11. Layout inventory checklist

| Item | Status |
|------|--------|
| Auth uses `x-guest-layout` only | Done |
| App pages use `x-app.page` | Done |
| Landing uses `x-landing.layout` | Done |
| No live `<x-app-layout>` usage | Done |
| Breeze nav unused at runtime | Done (files still present) |
| Profile forms fully Swiss | **Incomplete** |
| Application partials fully Swiss | **Incomplete** |
| Auth screens Swiss | **Not started** (intentionally Breeze) |
| Dead layout files removed | **Not started** |

---

## 12. Quick reference — where to change what

| Goal | Start here |
|------|------------|
| App chrome / nav | `components/app/header.blade.php`, `shell-layout.blade.php` |
| Page title / width | `components/app/page.blade.php` |
| Design tokens / buttons | `resources/css/landing.css` |
| Feed cards | `feed/partials/timeline-item.blade.php`, `composer.blade.php` |
| Auth look | `layouts/guest.blade.php` + `auth/*` |
| Landing | `components/landing/*`, `config/landing.php` |
| Admin | `app/Filament/SuperAdmin/*` |

---

*Generated as a point-in-time audit of the CareerHub repository. Update this document when major layout or architecture decisions land (e.g. P8-UI #103).*
