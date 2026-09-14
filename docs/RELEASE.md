# CareerHub Release Checklist

Use this checklist before demo, staging, or production release.

## Prerequisites

- PHP 8.5+
- Composer dependencies installed
- Node.js for frontend assets
- SQLite (local) or configured database (staging/production)

## Release steps

### 1. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Confirm database, mail, and app URL values in `.env`.

### 2. Database

```bash
php artisan migrate --force
php artisan db:seed --class=CareerHubDemoSeeder   # optional demo data
```

### 3. Storage

```bash
php artisan storage:link
```

See [storage-local-setup.md](./storage-local-setup.md) for local disk details.

### 4. Frontend assets

```bash
npm ci
npm run build
```

For local development:

```bash
composer run dev
```

### 5. Test suite

```bash
php artisan test --compact
```

All feature, integration, and E2E tests must pass before release.

### 6. Cache and optimization (production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Demo script

1. Open the public landing page and register as an **Employer**.
2. Create and **publish** a job posting from the employer dashboard.
3. Log out and register as an **Employee**.
4. Browse jobs, open the published role, and **submit an application**.
5. Log back in as the employer and move the application to **Under Review**, then **Accepted**.
6. Visit the shared **Feed** and create a post from both personas.
7. Log in to the **Super Admin** panel at `/super-admin`.
8. Review dashboard stats, moderate a post, and block a user from posting if needed.
9. As the employee, upload a **CV** on the profile page and download it to verify storage.
10. Confirm employer **company name** appears on employee job browse pages.

## Smoke checks

- [ ] Employer dashboard loads
- [ ] Employee dashboard loads
- [ ] Super Admin panel login works at `/super-admin/login`
- [ ] Feed shows published posts from both personas
- [ ] Job search filter preserves query on pagination
- [ ] Blocked users see feed read-only without create actions
- [ ] CV upload and download work after `storage:link`

## Rollback

```bash
php artisan migrate:rollback
git checkout <previous-tag-or-commit>
composer install --no-dev
npm ci && npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Sign-off

| Check | Owner | Date |
| --- | --- | --- |
| Migrations applied | | |
| Demo seeder verified | | |
| Full test suite green | | |
| Storage linked | | |
| Demo script completed | | |
