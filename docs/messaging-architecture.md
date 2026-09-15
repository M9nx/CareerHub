# Messaging Architecture

**Status:** Planning only (CH-DOC-004) — **no implementation until approved**  
**Date:** 2026-09-15  
**Companion:** [`linkedin-gap-analysis.md`](./linkedin-gap-analysis.md), [`ui-architecture.md`](./ui-architecture.md)

## Goal

Add CareerHub direct messaging that fits the Swiss app shell, without early real-time complexity.

## Non-goals (MVP)

- No WebSockets / presence until volume justifies it  
- No group chats in v1  
- No Filament moderation until the domain exists  

## Proposed domain

| Table | Purpose |
|-------|---------|
| `conversations` | Thread header (`type` = direct) |
| `conversation_participants` | `conversation_id`, `user_id`, `last_read_at` |
| `messages` | `conversation_id`, `user_id`, `body`, timestamps |

Constraints:

- Direct conversations are unique for an unordered pair of users  
- Only connection-accepted peers may start a thread (recommended policy)  
- Soft-delete messages optional later  

## HTTP surface (MVP)

| Route | Behavior |
|-------|----------|
| `GET /messaging` | Inbox list |
| `GET /messaging/{conversation}` | Thread view |
| `POST /messaging/{conversation}` | Send message |
| `POST /messaging/people/{user}` | Find-or-create direct conversation |

Polling every 15–30s or manual refresh is acceptable for MVP. Real-time (Reverb/Pusher) is a later milestone.

## Authorization

- Participant-only read/write  
- Blocked-from-posts users may still message unless product says otherwise (decide before coding)  
- Super Admin moderation deferred  

## Notifications

- Database notification on new message when recipient is offline from the thread  
- Optional mail digest later  

## Implementation order (after approval)

1. Migrations + models + factories  
2. Policies + Form Requests  
3. Inbox + thread Blade UI in `x-app.page`  
4. Header Messaging link (no badge until unread counts exist)  
5. Pest coverage for authz and pairwise uniqueness  

## Open decisions

| Topic | Options | Recommendation |
|-------|---------|----------------|
| Who can message | Connections only vs any peer | **Connections only** |
| Message length | 1k / 5k | **2000 chars** |
| Attachments | None / images | **None in MVP** |
| Real-time | Polling / Reverb | **Polling first** |

**Do not implement CH-MSG-001 until this document is explicitly approved.**
