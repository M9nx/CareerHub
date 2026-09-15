# Profile Expansion Plan

**Status:** Stub — expand before Milestone 3 migrations  
**Date:** 2026-09-15

## Current schema (verified)

| Table | Columns today |
|-------|----------------|
| `users` | `name`, `email`, `role`, `is_active`, `is_blocked_from_posts`, … |
| `employee_profiles` | `user_id`, `cv_path`, `application_image_path` |
| `employer_profiles` | `user_id`, `company_name` |

**Missing:** headline, location, about, avatar/cover, experience, education, skills, company industry/size/website.

## Planned stages (do not implement until this doc is completed)

1. **Minimum identity** — headline, location, avatar path (users or profiles)  
2. **Public profile show** — route + policy + Blade  
3. **Employer company fields** — industry, size, location, website, about, logo  
4. **Career sections** — experience / education / skills tables  

## Rules

- No fake profile-view counters.  
- Propose exact migrations and rollback in this file before coding.  
- Update [`linkedin-gap-analysis.md`](./linkedin-gap-analysis.md) when decisions land.

## Open questions

- Avatar on `users` vs role profile tables?  
- Are professional profiles public to all authenticated users or connection-gated?  
