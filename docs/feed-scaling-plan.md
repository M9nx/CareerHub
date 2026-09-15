# Feed Scaling Plan

**Status:** Stub — complete before any TimelineQuery rewrite  
**Date:** 2026-09-15

## Current implementation (verified)

`App\Support\Timeline\TimelineQuery`:

1. Loads all published posts (with author, attachments, reactions, sharedPost)  
2. Loads all published jobs  
3. Loads role-scoped applications  
4. Concatenates, sorts in PHP, slices for page  

**Fit:** Demo / early production scale.  
**Risk:** Memory and latency grow with total rows, not page size.

## Non-goals for Milestone 1

Do **not** rewrite the query while building the three-column UI.

## Candidate paths (to evaluate later)

| Option | Pros | Cons |
|--------|------|------|
| A. Keep merge; cap windows (e.g. last 90 days) | Simple | Still memory-bound |
| B. SQL `UNION ALL` + DB sort/limit | True pagination | Heterogeneous rows |
| C. Dedicated `feed_items` / activity table written by observers | Fast reads | Dual-write complexity |
| D. Cursor pagination on a single primary stream + inject jobs | Simpler than full union | Product tradeoffs |

## Required before rewrite

- [ ] Pest coverage for ordering, role filtering, empty pages  
- [ ] Query count / memory baseline notes  
- [ ] Chosen option + rollback plan  
- [ ] Feature flag or parallel class if risky  

See Milestone 2 task **CH-DOC-002** in [`ui-architecture.md`](./ui-architecture.md).
