# CareerHub UI Architecture & Implementation Plan

**Date:** 2026-09-15  
**Status:** Milestone 3 complete — ready for Milestone 4 (network)  
**Companion:** [`linkedin-gap-analysis.md`](./linkedin-gap-analysis.md), [`CODEBASE_AUDIT.md`](./CODEBASE_AUDIT.md)

---

## 1. Principles

1. **Extend** the existing Swiss app shell — do not invent a second layout system.  
2. Blade + Alpine + Tailwind remain the stack.  
3. One semantic token set for authenticated app surfaces.  
4. No fake metrics. Omit until real data exists.  
5. Preserve route names, middleware, policies, and Form Requests unless a change is required.  
6. Filament stays isolated. Breeze remains auth-guest until deliberately migrated.  
7. Incremental milestones with Pest coverage each slice.

---

## 2. Shell hierarchy (current → target)

```
Current:
  x-app.shell-layout
    └── x-app.header
    └── <main> slot
          └── x-app.page (title + single column)

Target:
  x-app.shell-layout
    └── x-app.header          (network IA)
    └── <main> slot
          ├── x-app.page                (CRUD / settings — single column)
          └── x-app.feed.layout         (Home/feed — 3-column responsive)
                ├── left:  profile-summary
                ├── center: composer + timeline
                └── right:  sidebar widgets
```

`x-app.page` stays for jobs CRUD, applications, profile settings, etc.  
Feed gets a dedicated layout component that still uses `shell-layout` (no competing HTML document).

---

## 3. Design system — semantic tokens

Map onto existing `--landing-*` values initially (aliases), then rename gradually.

Proposed aliases in `resources/css/landing.css` (or `app.css`):

| Token | Maps from (current) | Role |
|-------|---------------------|------|
| `--app-background` | `--landing-canvas` | Page canvas |
| `--app-surface` | `--landing-card` | Cards / panels |
| `--app-surface-hover` | derived | Hover surfaces |
| `--app-border` | `--landing-rule` | Borders / rules |
| `--app-text` | `--landing-ink` | Primary text |
| `--app-text-muted` | `--landing-muted` | Secondary text |
| `--app-primary` | `--landing-accent` | Primary actions |
| `--app-primary-hover` | derived opacity | Primary hover |
| `--app-danger` | `#C8102E` / existing alert | Destructive |
| `--app-success` | new restrained green | Success |
| `--app-warning` | new restrained amber | Warning |

### Primitives to standardize (CSS + Blade)

| Primitive | Approach |
|-----------|----------|
| Buttons | `.swiss-btn-primary`, `.swiss-btn-secondary`, `.swiss-btn-danger` → alias `.app-btn-*` |
| Inputs | Expand `.app-form` so Swiss pages stop needing Breeze `x-text-input` look |
| Cards | `.app-card` |
| Avatar | `.app-avatar` (+ image support later) |
| Badge | `.app-badge` |
| Alerts | `.app-alert-*` |
| Empty states | new `.app-empty` patterns |
| Dropdown / modal | Alpine + app tokens (replace Breeze modal on authenticated pages over time) |

**Rule:** No new indigo/gray Breeze classes on authenticated screens.

---

## 4. Target component tree (integrate with existing)

```
resources/views/components/app/
  shell-layout.blade.php          # keep
  header.blade.php                # redesign IA
  user-menu.blade.php             # extend
  page.blade.php                  # keep for CRUD
  flash.blade.php                 # keep
  feed/
    layout.blade.php              # NEW 3-column shell
    profile-summary.blade.php     # NEW left rail
    composer.blade.php            # move/adapt from feed/partials
    timeline.blade.php            # NEW list wrapper
    item.blade.php                # NEW switch by TimelineItemType
    post-card.blade.php           # NEW (replace unused old post-card)
    job-card.blade.php            # NEW
    application-card.blade.php    # NEW
  sidebar/
    job-suggestions.blade.php     # NEW — real published jobs
    people-suggestions.blade.php  # NEW — other authors (deterministic)
    profile-completion.blade.php  # NEW — based on null fields only
```

Partials under `resources/views/feed/partials/` may remain as thin wrappers that `@include` components during migration.

---

## 5. Responsive breakpoints

| Width | Behavior |
|-------|----------|
| ≥1280px | 3 columns: left ~280px, center fluid, right ~300px |
| 768–1279px | Left + center; right collapsed into bottom accordion or “More” |
| &lt;768px | Center only; left/right via drawers / Me menu |

---

## 6. Milestone overview

| Milestone | Focus | Outcome |
|-----------|--------|---------|
| **M1 Foundation** | Tokens, header IA, 3-col feed shell, identity summary, real sidebars | Feels like a network home |
| **M2 Feed experience** | Composer, cards, comments, reaction polish | Credible social feed |
| **M3 Identity** | Profile expansion | Professional identity |
| **M4 Network** | Connections + `/network` | Social graph |
| **M5 Discovery** | Search + job discovery | Find people/jobs |
| **M6 Engagement** | Notifications, saved, messaging docs/impl | Retention loops |

---

## 7. Task catalog

### Milestone 1 — Foundation (first safe batch)

#### CH-DOC-001 — Gap analysis & architecture docs

| | |
|--|--|
| **Purpose** | Shared plan before coding |
| **Files** | `docs/linkedin-gap-analysis.md`, `docs/ui-architecture.md` |
| **DB / BE / FE** | None |
| **Tests** | N/A |
| **Dependencies** | None |
| **Risk** | None |
| **Acceptance** | Docs reviewed; first batch agreed |

#### CH-UI-001 — Semantic app tokens

| | |
|--|--|
| **Purpose** | Alias `--app-*` tokens; document primitives |
| **Files** | `resources/css/landing.css`, optionally `app.css` |
| **DB** | None |
| **Backend** | None |
| **Frontend** | Token aliases; keep existing class names working |
| **Tests** | Visual; build passes |
| **Dependencies** | CH-DOC-001 |
| **Risk** | Low |
| **Acceptance** | Authenticated pages can use `--app-*`; no visual regression on feed |

#### CH-UI-002 — Network header IA

| | |
|--|--|
| **Purpose** | Home=Feed, Jobs, (placeholders or links for Network later), Me menu; compact sticky header; search input UI (non-functional stub OK if labeled disabled or posts to later route) |
| **Files** | `components/app/header.blade.php`, `user-menu.blade.php` |
| **DB** | None |
| **Backend** | Optional: change post-login redirect to `feed.index` |
| **Frontend** | Nav IA, mobile menu, employer shortcuts in Me |
| **Tests** | Update nav assertions; feature smoke for feed as home |
| **Dependencies** | CH-UI-001 |
| **Risk** | Medium (IA) |
| **Acceptance** | Header matches network IA; mobile usable; no Filament/auth breakage |

#### CH-UI-003 — Feed as authenticated home

| | |
|--|--|
| **Purpose** | After login / `/` for employer & employee → `feed.index` |
| **Files** | `routes/web.php`, possibly auth controllers redirects |
| **DB** | None |
| **Backend** | Redirect changes only |
| **Frontend** | None |
| **Tests** | Redirect tests |
| **Dependencies** | CH-UI-002 |
| **Risk** | Low |
| **Acceptance** | Employees/employers land on feed; dashboards still reachable |

#### CH-FEED-001 — Three-column feed layout

| | |
|--|--|
| **Purpose** | Responsive professional feed shell |
| **Files** | NEW `components/app/feed/layout.blade.php`, `feed/index.blade.php`, CSS grid utilities |
| **DB** | None |
| **Backend** | `FeedController@index` passes sidebar data arrays |
| **Frontend** | Layout grid; keep composer + items in center |
| **Tests** | Feed feature tests still pass; assert layout landmarks |
| **Dependencies** | CH-UI-001 |
| **Risk** | Low |
| **Acceptance** | 3/2/1 column behavior; no domain logic change |

#### CH-FEED-002 — Left profile summary (real data only)

| | |
|--|--|
| **Purpose** | Identity card: name, role, company_name or available fields, link to profile settings; avatar initial |
| **Files** | NEW `components/app/feed/profile-summary.blade.php` |
| **DB** | None in this task (omit missing metrics) |
| **Backend** | None beyond existing auth user relations |
| **Frontend** | Component; employer vs employee variant |
| **Tests** | Assert name / company when present; assert no fake “views” text |
| **Dependencies** | CH-FEED-001 |
| **Risk** | Low |
| **Acceptance** | No fabricated stats; works for both personas |

#### CH-FEED-003 — Right sidebar with real widgets

| | |
|--|--|
| **Purpose** | 2–3 widgets from DB: e.g. latest published jobs, recent post authors, profile completion checklist |
| **Files** | NEW `components/app/sidebar/*.blade.php`, FeedController |
| **DB** | None |
| **Backend** | Simple queries (limit 5); eager-load safely |
| **Frontend** | Widget cards |
| **Tests** | Assert job title appears when published job exists |
| **Dependencies** | CH-FEED-001 |
| **Risk** | Low |
| **Acceptance** | Empty states when no data; no placeholders pretending to be users |

#### CH-FEED-004 — Composer presentation polish

| | |
|--|--|
| **Purpose** | LinkedIn-like “Start a post” affordance; keep existing `feed.store` + attachments |
| **Files** | `feed/partials/composer.blade.php` → prefer `components/app/feed/composer.blade.php` |
| **DB** | None |
| **Backend** | Unchanged validation/auth |
| **Frontend** | Expandable card or Alpine modal; same form posts to `feed.store` |
| **Tests** | Existing `PostSocialFeedTest` still green |
| **Dependencies** | CH-FEED-001 |
| **Risk** | Low |
| **Acceptance** | Can publish + attach as today; looks professional |

#### CH-FEED-005 — Unified feed card presentation

| | |
|--|--|
| **Purpose** | Consistent card chrome for post / shared post / job / application; clearer action bar |
| **Files** | `timeline-item.blade.php` → split into `feed/item` + type cards |
| **DB** | None |
| **Backend** | Unchanged |
| **Frontend** | Shared header/footer patterns |
| **Tests** | Timeline / social tests still pass |
| **Dependencies** | CH-FEED-001 |
| **Risk** | Low–medium (assertion text) |
| **Acceptance** | Visual consistency; like/share still work |

#### CH-UI-004 — Dead Breeze shell cleanup (safe)

| | |
|--|--|
| **Purpose** | Remove or quarantine unused `layouts/app`, `navigation`, persona-nav after updating tests |
| **Files** | Dead layouts/components; `EmployeeProfileEditTest` |
| **DB** | None |
| **Backend** | None |
| **Frontend** | Delete unused |
| **Tests** | Point tests at `app/header` |
| **Dependencies** | CH-UI-002 |
| **Risk** | Low if grep-clean |
| **Acceptance** | No references; suite green |

---

### Milestone 2 — Feed experience

| ID | Title | Priority | Notes |
|----|-------|----------|-------|
| CH-FEED-006 | Comments schema + model + policy | P0 | Verify then migrate `post_comments` |
| CH-FEED-007 | Comment store/destroy + feed UI | P0 | Form Request; nested optional later |
| CH-FEED-008 | Reaction bar UX polish | P1 | Keep single like |
| CH-FEED-009 | Attachment presentation upgrade | P1 | Lightbox optional |
| CH-DOC-002 | `docs/feed-scaling-plan.md` | P1 | No rewrite yet |

---

### Milestone 3 — Identity

| ID | Title | Priority | Notes |
|----|-------|----------|-------|
| CH-DOC-003 | `docs/profile-expansion-plan.md` | P0 | Before migrations |
| CH-PROF-001 | Minimum identity columns (headline, location, avatar) | P0 | Staged migration |
| CH-PROF-002 | Public profile show page | P1 | Route + policy |
| CH-PROF-003 | Employer company fields | P1 | Expand employer_profiles |
| CH-PROF-004 | Experience/Education/Skills tables | P2 | After plan |

---

### Milestone 4 — Network

| ID | Title | Priority | Notes |
|----|-------|----------|-------|
| CH-SOCIAL-001 | Connections schema + state machine | P1 | Mutual connections |
| CH-SOCIAL-002 | Connect/Accept/Ignore/Remove actions | P1 | Policies + tests |
| CH-SOCIAL-003 | `/network` page | P1 | Invitations + suggestions |
| CH-SOCIAL-004 | Deterministic people recommendations | P1 | No random fake users |

---

### Milestone 5 — Discovery

| ID | Title | Priority | Notes |
|----|-------|----------|-------|
| CH-SEARCH-001 | Search service interface + Eloquent impl | P1 | People/Jobs/Companies/Posts |
| CH-SEARCH-002 | Header search + results page | P1 | |
| CH-JOB-001 | Job discovery split layout | P1 | Preserve routes |
| CH-JOB-002 | Job field enrichment | P1–P2 | location, type |

---

### Milestone 6 — Engagement

| ID | Title | Priority | Notes |
|----|-------|----------|-------|
| CH-NOTIF-001 | Database notifications + center | P1 | Build on existing mail |
| CH-SAVE-001 | Saved jobs/posts | P2 | |
| CH-DOC-004 | `docs/messaging-architecture.md` | P2 | No impl until approved |
| CH-MSG-001 | Messaging MVP | P3 | After architecture sign-off |

---

## 8. First safe implementation batch (proposed)

**Scope:** Milestone 1 foundation only — **no new domain tables** in this batch.

| Order | Task IDs |
|-------|----------|
| 1 | CH-UI-001 tokens |
| 2 | CH-FEED-001 layout |
| 3 | CH-FEED-002 profile summary |
| 4 | CH-FEED-003 sidebar widgets |
| 5 | CH-FEED-004 composer polish |
| 6 | CH-FEED-005 card polish |
| 7 | CH-UI-002 header IA |
| 8 | CH-UI-003 feed as home |
| 9 | CH-UI-004 dead code (optional end of batch) |

### Explicitly deferred from batch 1

- Comments migration  
- Connections  
- Search backend  
- Profile schema expansion  
- Messaging  
- TimelineQuery rewrite  
- Auth guest Swiss migration  

---

## 9. Proposed file changes (Batch 1) — review before coding

### Create

| Path | Why |
|------|-----|
| `resources/views/components/app/feed/layout.blade.php` | 3-column responsive feed shell |
| `resources/views/components/app/feed/profile-summary.blade.php` | Left identity |
| `resources/views/components/app/feed/composer.blade.php` | Moved/polished composer |
| `resources/views/components/app/feed/item.blade.php` | Type switch |
| `resources/views/components/app/feed/post-card.blade.php` | Post/shared presentation |
| `resources/views/components/app/feed/job-card.blade.php` | Job item |
| `resources/views/components/app/feed/application-card.blade.php` | Application item |
| `resources/views/components/app/sidebar/job-suggestions.blade.php` | Real jobs |
| `resources/views/components/app/sidebar/people-suggestions.blade.php` | Real authors |
| `resources/views/components/app/sidebar/profile-completion.blade.php` | Real completeness |

### Modify

| Path | Why |
|------|-----|
| `resources/css/landing.css` | `--app-*` aliases + feed grid utilities |
| `resources/views/feed/index.blade.php` | Use feed layout instead of narrow `x-app.page` |
| `resources/views/feed/partials/*` | Thin wrappers or delete after move |
| `resources/views/components/app/header.blade.php` | Network IA |
| `resources/views/components/app/user-menu.blade.php` | Employer shortcuts |
| `app/Http/Controllers/FeedController.php` | Sidebar view models only |
| `routes/web.php` | Optional home → feed redirects |
| Relevant Pest tests | Nav / feed / redirects |

### Do not touch in Batch 1

| Path | Why |
|------|-----|
| Filament panel | Separate product surface |
| `resources/views/auth/*` | Breeze guest intentional |
| `TimelineQuery` algorithm | Scaling doc later |
| Migrations | No schema in batch 1 |

### Optional delete (after reference check)

| Path |
|------|
| `resources/views/layouts/app.blade.php` |
| `resources/views/layouts/navigation.blade.php` |
| `resources/views/components/employer-nav.blade.php` |
| `resources/views/components/employee-nav.blade.php` |
| Unused `feed/partials/post-card.blade.php` (replace with new component) |

---

## 10. Verification loop (each task)

1. `php artisan test --compact` (narrow) then broader when touching redirects/nav  
2. `npm run build`  
3. Desktop 1440 / tablet 1024 / mobile 390  
4. Confirm like/share/compose/apply still work  
5. Confirm no fake metric strings in Blade  
6. Pint on dirty PHP  

---

## 11. Decision log (open)

| Decision | Options | Recommendation |
|----------|---------|----------------|
| Connection model | Mutual connections vs follow graph | **Mutual connections** for people |
| Comments depth | Flat vs threaded | **Flat first**; `parent_id` nullable for later |
| Home route | Keep dashboard vs feed | **Feed as home**; keep dashboard routes |
| Search | Inline only vs results page | **Results page** + lightweight typeahead later |
| Tokens | Parallel `--app-*` vs rename `--landing-*` | **Alias `--app-*` first** |

---

## 12. Definition of done — first major iteration

Aligned with product brief:

- [x] Professional-network authenticated shell  
- [x] Feed is primary home  
- [x] Desktop multi-column feed  
- [x] Mobile usable  
- [x] Jobs/applications/posts still work  
- [x] Modern composer  
- [x] Consistent feed cards  
- [x] Real identity summary (no fake metrics)  
- [x] Right sidebar with real data  
- [x] Clear network-style header  
- [x] Less Breeze look on authenticated pages  
- [x] Authz unchanged / tests green / build green  
- [x] Ready for comments, connections, search next  

---

**Next:** Milestone 4 — connections / network (CH-SOCIAL-001+) after Milestone 3 sign-off.

---

## 15. Milestone 3 status (2026-09-15)

**Completed:** CH-DOC-003, CH-PROF-001, CH-PROF-002, CH-PROF-003.

Shipped:
- Profile expansion plan decisions
- `users` identity columns (headline, location, about, avatar)
- Employer company enrichment + logo
- Public profile at `/people/{user}`
- Feed summary / completion / author links updated

**Deferred:** CH-PROF-004 experience / education / skills tables.

---

## 14. Batch / Milestone 2 status (2026-09-15)

**Completed:** CH-FEED-006, CH-FEED-007, CH-FEED-008, CH-FEED-009, CH-DOC-002.

Shipped:
- `post_comments` schema (`parent_id` reserved), model, factory, policies
- Comment store/destroy on feed with Form Request + Pest coverage
- Reaction bar Liked/Comment/Share polish
- Attachment lightbox for images
- Expanded feed scaling notes (rewrite still deferred)
