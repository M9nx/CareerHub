# ADR 0001: Panel isolation — Filament for SuperAdmin, Breeze for personas

## Status

Accepted (P0-M9nx, issue #10)

## Context

CareerHub has three personas: SuperAdmin, Employer, and Employee. We need clear authentication surfaces so admin CRM tools never share routes or session confusion with end-user Breeze flows.

## Decision

| Persona | UI stack | URL prefix | Auth guard |
|---------|----------|------------|------------|
| SuperAdmin | Filament 5 + Breezy | `/super-admin` | `filament` |
| Employer | Laravel Breeze | `/employer` | `web` |
| Employee | Laravel Breeze | `/employee` | `web` |

- The legacy `/admin` Filament panel (`AdminPanelProvider`) is **deprecated** and unregistered.
- Only `SuperAdminPanelProvider` registers a Filament panel (`id: super-admin`).
- Employer and Employee never use Filament; they use Blade + Breeze controllers only.
- `User::canAccessPanel()` must return `true` only for the `super-admin` panel and SuperAdmin users (role enforcement lands in P0-Mariam, issue #11).

## Consequences

- SuperAdmin features are implemented as `app/Filament/SuperAdmin/**` resources.
- Persona features are implemented under `app/Http/Controllers/Employer/**` and `Employee/**` with `routes/employer.php` and `routes/employee.php`.
- Tests for panel access live in `tests/Feature/Panel/`.
- Adding a second Filament panel for Employer/Employee is **out of scope** unless this ADR is superseded.
