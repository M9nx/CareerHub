# Feed Scaling Plan

**Status:** Milestone 2 notes captured — TimelineQuery rewrite still deferred  
**Date:** 2026-09-15  
**Task:** CH-DOC-002

## Current implementation (verified)

`App\Support\Timeline\TimelineQuery`:

1. Loads all published posts (with author, attachments, reactions, top-level comments + users, sharedPost)  
2. Loads all published jobs  
3. Loads role-scoped applications  
4. Concatenates, sorts in PHP, slices for page  

**Fit:** Demo / early production scale.  
**Risk:** Memory and latency grow with total rows, not page size.

Comments are stored in `post_comments` and eager-loaded for the current page slice only after the full post collection is loaded — they do not change the merge algorithm, but they increase per-post payload.

## Non-goals (still)

Do **not** rewrite TimelineQuery while shipping comments, reactions polish, or attachment lightbox.

## Candidate paths (evaluate before rewrite)

| Option | Pros | Cons |
|--------|------|------|
| A. Keep merge; cap windows (e.g. last 90 days) | Simple | Still memory-bound |
| B. SQL `UNION ALL` + DB sort/limit | True pagination | Heterogeneous rows |
| C. Dedicated `feed_items` / activity table written by observers | Fast reads | Dual-write complexity |
| D. Cursor pagination on a single primary stream + inject jobs | Simpler than full union | Product tradeoffs |

**Lean recommendation for CareerHub next:** start with **A** (time window + hard row caps) as a safety rail, then prototype **B** behind a feature flag if page latency exceeds ~200ms on staging data.

## Required before rewrite

- [x] Pest coverage for feed ordering / role filtering exists under `tests/Feature/Post/`  
- [ ] Query count / memory baseline notes on a seeded dataset (≥1k posts)  
- [ ] Chosen option + rollback plan recorded here  
- [ ] Feature flag or parallel class if option B/C  

## Comment load notes

- Flat comments only in UI (`parent_id` reserved).  
- Eager load: `comments` where `parent_id` is null, oldest first, with `user`.  
- Future: paginate comments per post or lazy-load when the comments panel opens.
