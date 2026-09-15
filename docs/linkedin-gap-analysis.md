# CareerHub ↔ Professional Network Gap Analysis

**Date:** 2026-09-15  
**Branch:** `feat/#99-timeline-feed`  
**Reference:** LinkedIn-class UX/IA only (not branding or pixel copy)  
**Basis:** Verified against migrations, models, routes, views, policies, and tests — not filenames alone.

Related docs:

- [`CODEBASE_AUDIT.md`](./CODEBASE_AUDIT.md) — current layout/stack inventory  
- [`ui-architecture.md`](./ui-architecture.md) — design system + milestone task plan  

---

## Verified product snapshot (what actually exists)

| Area | Verified state |
|------|----------------|
| Auth roles | `employer`, `employee`, `super_admin` |
| App shell | `x-app.page` → `x-app.shell-layout` + `x-app.header` |
| Home after login | Role **dashboard**, not `/feed` |
| Feed | Posts + published jobs + application events (in-memory merge) |
| Social on posts | Like (toggle), share-to-feed, attachments (image/PDF) |
| Comments | **Absent** (no model, migration, or UI) |
| Connections / follow | **Absent** |
| Messaging | **Absent** |
| Global search | **Absent** (job title search only on employee jobs index) |
| Notifications UI | **Absent** (one mail notification for application status) |
| Profiles | Employer: `company_name` only. Employee: `cv_path` + optional image. No headline/location/cover/skills |
| Jobs schema | `title`, `description`, `status`, `published_at` — no location, type, experience |
| Saved content | **Absent** |
| Design tokens | `--landing-*` / `.swiss-*` / `.app-*` — mixed with Breeze form controls on profile/forms |

---

## Navigation

| | |
|--|--|
| **Current state** | Sticky header with persona CRUD links: Dashboard, Feed, Jobs, Applications, Posts + user menu. No search. No Network / Messaging / Notifications. Logo links to dashboard. |
| **Desired state** | Compact professional-network header: logo, global search, Home (feed), Network, Jobs, Messaging, Notifications, Me menu. Employer shortcuts (Post a job, Applications) in Me or secondary cluster. Feed is primary home. |
| **Affected files** | `resources/views/components/app/header.blade.php`, `user-menu.blade.php`, `routes/web.php`, employer/employee route entry redirects |
| **Backend** | Optional unread counts endpoints later; redirect `/` + login landing to `feed.index` |
| **Frontend** | Redesign header; active states; mobile drawer; badges |
| **Database** | None for nav IA; badges depend on notifications/messages later |
| **Risk** | Medium — changes IA muscle memory; tests assert old nav copy |
| **Priority** | **P0** |

---

## Feed

| | |
|--|--|
| **Current state** | Single narrow column (`x-app.page` narrow). Composer + timeline cards. No left/right sidebars. Mixed post/job/application cards. |
| **Desired state** | 3-column desktop (identity \| feed \| widgets), 2-col tablet, 1-col mobile. Feed is authenticated home. Shared card foundation for item types. |
| **Affected files** | `feed/index.blade.php`, `feed/partials/*`, `components/app/page.blade.php` or new `components/app/feed/layout.blade.php`, `FeedController`, `TimelineQuery` |
| **Backend** | Pass sidebar view models; keep TimelineQuery API stable initially |
| **Frontend** | Feed layout grid; profile summary; sidebar widgets; composer UX |
| **Database** | None for layout |
| **Risk** | Low–medium if layout-only; high if feed query rewrite |
| **Priority** | **P0** |

---

## Profiles

| | |
|--|--|
| **Current state** | Account settings (`/profile`) + thin role profiles. No public professional profile page. No cover, headline, location, about, experience, education, skills. |
| **Desired state** | Public-ish professional profile: cover, avatar, headline, location, About, Experience, Education, Skills, Activity/Posts. Company profile for employers. |
| **Affected files** | `ProfileController`(s), `employer/profile/*`, `employee/profile/*`, profile migrations/models, new profile show routes/views |
| **Backend** | Schema expansion; policies for view/edit; profile show controllers |
| **Frontend** | Profile header + sections components |
| **Database** | Staged: headline/location/about/avatar/cover first; then experience/education/skills tables |
| **Risk** | High if big-bang schema; medium if staged |
| **Priority** | **P0** (minimum identity fields), **P1** (full sections) |

See also: planned `docs/profile-expansion-plan.md` (Milestone 3).

---

## Social Graph

| | |
|--|--|
| **Current state** | No connections, follows, or friend requests. |
| **Desired state** | Mutual **connections** for people (`pending` / `accepted` / `rejected` / `withdrawn`). Optional company follow later. |
| **Affected files** | New models/controllers/policies/routes; network page; profile CTAs |
| **Backend** | ConnectionRequest/Connection domain; state machine; authorization |
| **Frontend** | Connect/Accept/Ignore/Remove; Network page |
| **Database** | New tables (e.g. `connections`) |
| **Risk** | Medium — new domain; must be tested thoroughly |
| **Priority** | **P1** (after feed shell) |

---

## Posts

| | |
|--|--|
| **Current state** | CRUD via employer/employee post resources + feed composer (`title` auto from body). Share + attachments + publish/hide moderation. |
| **Desired state** | Feed-first authoring; richer composer (photo/document); consistent card; more menu (edit/delete/report later). Persona post indexes remain management tools. |
| **Affected files** | `FeedController`, composer, timeline-item, post policies |
| **Backend** | Keep Form Requests / StorePostAttachments / SharePostToFeed |
| **Frontend** | Modal/expandable composer; card overflow menu |
| **Database** | Optional later (visibility, mentions) |
| **Risk** | Low if presentation-first |
| **Priority** | **P0** (presentation), **P1** (composer modal polish) |

---

## Comments

| | |
|--|--|
| **Current state** | **Do not exist.** No `post_comments` table, model, or UI. |
| **Desired state** | Threaded or single-level comments on posts; policy + validation; shown on feed card. |
| **Affected files** | New migration/model/policy/request/controller; feed card partial; Post model relation |
| **Backend** | `PostComment` (+ optional `parent_id`); authorize create/delete |
| **Frontend** | Comment list + composer under card |
| **Database** | `post_comments` (`post_id`, `user_id`, `parent_id` nullable, `body`) |
| **Risk** | Medium — N+1 and moderation |
| **Priority** | **P0** for Milestone 2 (after layout), required for social feed credibility |

---

## Reactions

| | |
|--|--|
| **Current state** | Single like type via `post_reactions` + `TogglePostReaction`. Count shown on card. |
| **Desired state** | Keep like; optionally expand reaction types later. Show reactor summary without fake counts. |
| **Affected files** | `TogglePostReaction`, `PostReactionType`, timeline-item |
| **Backend** | Stable; optional “who liked” endpoint |
| **Frontend** | Clearer reaction bar |
| **Database** | Exists; unique `(post_id, user_id)` |
| **Risk** | Low |
| **Priority** | **P0** polish; **P2** multi-reaction types |

---

## Connections / Following

| | |
|--|--|
| **Current state** | Absent (same as Social Graph). |
| **Desired state** | People connections (mutual). Company follow optional P2. |
| **Affected files** | Network + profile CTAs |
| **Backend / Frontend / Database** | See Social Graph |
| **Risk** | Medium |
| **Priority** | **P1** |

---

## Jobs

| | |
|--|--|
| **Current state** | Employer CRUD + publish/close; employee list + show + apply. Thin fields. Table/list UI, not discovery layout. |
| **Desired state** | Discovery layout (list + detail). Richer cards when fields exist. Preserve route/policy boundaries. |
| **Affected files** | `employee/jobs/*`, `employer/jobs/*`, `JobPosting` model/migration |
| **Backend** | Optional field expansion; filters when columns exist |
| **Frontend** | Split-pane or card discovery UI |
| **Database** | Add `location`, `employment_type`, etc. when needed — not in first batch |
| **Risk** | Low for UI; medium for schema |
| **Priority** | **P1** UI; **P1–P2** schema enrichment |

---

## Job Discovery

| | |
|--|--|
| **Current state** | Employee jobs index: keyword search on title only. |
| **Desired state** | Filters (keyword, location, company, type, date) when data supports them; “Jobs for you” widget from real signals. |
| **Affected files** | `JobBrowseController`, jobs views, sidebar widgets |
| **Backend** | Query builder / SearchJobs service |
| **Frontend** | Filter UI + job suggestions widget |
| **Database** | Depends on job field expansion |
| **Risk** | Low–medium |
| **Priority** | **P1** |

---

## Messaging

| | |
|--|--|
| **Current state** | Absent. |
| **Desired state** | Documented architecture first (`docs/messaging-architecture.md`); HTTP inbox later; real-time only if justified. |
| **Affected files** | Future conversations/messages domain + `/messaging` |
| **Backend** | Conversations, participants, messages, read state |
| **Frontend** | Inbox UI |
| **Database** | Multiple tables |
| **Risk** | High if early; defer |
| **Priority** | **P3** (architecture doc **P2**) |

---

## Notifications

| | |
|--|--|
| **Current state** | `ApplicationStatusChangedNotification` (mail). No in-app notification center. No `notifications` migration observed in app migrations. |
| **Desired state** | Unified center: connection events, reactions, comments, application updates. Header badge. |
| **Affected files** | Notifications classes, header, new controller/views |
| **Backend** | Database notifications channel; event listeners |
| **Frontend** | Dropdown / page |
| **Database** | Laravel `notifications` table |
| **Risk** | Medium |
| **Priority** | **P1** after social events exist; **P2** polish |

---

## Search

| | |
|--|--|
| **Current state** | No global search. Filament searchable admin tables only. Employee job title search. |
| **Desired state** | Header search → People / Jobs / Companies / Posts. Service layer swappable to Scout later. |
| **Affected files** | New `SearchController` / `App\Services\Search\*`, header, results view |
| **Backend** | Eloquent search service |
| **Frontend** | Typeahead + results page |
| **Database** | Indexes on searchable columns as needed |
| **Risk** | Medium (perf) |
| **Priority** | **P1** |

---

## Recommendations

| | |
|--|--|
| **Current state** | None. |
| **Desired state** | Deterministic widgets: recent published jobs, other published authors, profile completion based on real null fields. No fake metrics. |
| **Affected files** | Sidebar components; FeedController view data |
| **Backend** | Simple query helpers |
| **Frontend** | `components/app/sidebar/*` |
| **Database** | None initially |
| **Risk** | Low |
| **Priority** | **P0** (real-data widgets in first batch) |

---

## User Identity

| | |
|--|--|
| **Current state** | `users.name` + role badge. Avatar = initial letter. No headline/photo/location. |
| **Desired state** | Headline, avatar upload, location; feed identity card reflects real data only. |
| **Affected files** | User/EmployeeProfile schema, profile forms, feed profile-summary |
| **Backend** | Migrations + validation |
| **Frontend** | Avatar/headline display |
| **Database** | Columns on `users` and/or `employee_profiles` |
| **Risk** | Medium |
| **Priority** | **P0** minimum fields for identity card |

---

## Employer Identity

| | |
|--|--|
| **Current state** | `employer_profiles.company_name` only. |
| **Desired state** | Logo, industry, size, location, website, about; company card on feed/jobs. |
| **Affected files** | Employer profile model/views |
| **Backend** | Schema + Form Request |
| **Frontend** | Company identity components |
| **Database** | Expand `employer_profiles` |
| **Risk** | Medium |
| **Priority** | **P1** |

---

## Saved Content

| | |
|--|--|
| **Current state** | Absent. |
| **Desired state** | Save job / save post; list in profile shortcuts. |
| **Affected files** | Pivot tables, controllers, UI |
| **Backend** | `saved_jobs` / `saved_posts` |
| **Frontend** | Save buttons + saved list |
| **Database** | New tables |
| **Risk** | Low–medium |
| **Priority** | **P2** |

---

## Privacy

| | |
|--|--|
| **Current state** | Role middleware + policies on CRUD. Feed posts are globally published to all authenticated viewers. No profile visibility settings. |
| **Desired state** | Explicit profile visibility; connection-scoped feed later; block already exists for posts. |
| **Affected files** | Policies, profile settings |
| **Backend** | Visibility enums / scopes |
| **Frontend** | Settings UI |
| **Database** | Visibility columns |
| **Risk** | High if wrong defaults |
| **Priority** | **P2** |

---

## Mobile UX

| | |
|--|--|
| **Current state** | Header hamburger; single-column page. Feed not optimized as network home. |
| **Desired state** | Single-column feed; drawers for secondary; touch targets ≥44px; no horizontal overflow. |
| **Affected files** | Shell, header, feed layout |
| **Backend** | None |
| **Frontend** | Responsive grid breakpoints |
| **Database** | None |
| **Risk** | Low |
| **Priority** | **P0** |

---

## Accessibility

| | |
|--|--|
| **Current state** | Partial: skip link, some labels, Alpine menus. Breeze modal used for delete account. Mixed focus rings. |
| **Desired state** | Semantic actions, labeled inputs, focus traps for modals, ARIA on menus, contrast. |
| **Affected files** | All new UI components |
| **Backend** | None |
| **Frontend** | a11y patterns in Alpine components |
| **Database** | None |
| **Risk** | Low if enforced as definition of done |
| **Priority** | **P0** for new UI |

---

## Performance

| | |
|--|--|
| **Current state** | `TimelineQuery` loads all posts/jobs/applications into memory, sorts, then slices for pagination. Eager-loads post relations. |
| **Desired state** | Document scaling path; keep in-memory for demo scale; later DB union / activity feed table / cursor pagination. |
| **Affected files** | `TimelineQuery`, future feed docs |
| **Backend** | Scaling plan + eventual rewrite behind same interface |
| **Frontend** | Avoid N+1 in Blade (counts preloaded) |
| **Database** | Possible feed index table later |
| **Risk** | High if rewrite without tests |
| **Priority** | **P1** document (`feed-scaling-plan.md`); **P2** implement |

---

## Administration

| | |
|--|--|
| **Current state** | Filament Super Admin: users, posts moderation, jobs, activity, block-from-posts. Separate UI (do not restyle with app shell). |
| **Desired state** | Keep Filament; add moderation for comments/connections when those domains exist. |
| **Affected files** | `app/Filament/SuperAdmin/*` |
| **Backend** | New resources as domains grow |
| **Frontend** | Filament only |
| **Database** | As needed |
| **Risk** | Low if isolated |
| **Priority** | **P2** |

---

## Priority rollup

| Priority | Themes |
|----------|--------|
| **P0** | Network nav + feed as home; 3-column feed shell; real sidebar data; composer/card polish; identity fields for summary; a11y/mobile; comments (Milestone 2) |
| **P1** | Connections + Network page; search; job discovery UI; employer identity; notifications center; profile sections; feed scaling doc |
| **P2** | Saved content; privacy controls; multi-reactions; Filament for new domains; messaging architecture doc |
| **P3** | Messaging product; real-time infra; Scout/Meilisearch |

---

## Explicit non-goals (near term)

- Rebuilding Filament or Breeze auth from scratch  
- SPA framework introduction  
- Fake profile views / impressions / connection counts  
- Pixel LinkedIn clone or LinkedIn branding  
- Premature WebSockets for chat  
- Dangerous TimelineQuery rewrite without a scaling plan + tests  
