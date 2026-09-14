# Local file storage setup

CareerHub stores employee CVs and application images on Laravel's **local `public` disk** only. There is no S3 (or other cloud) disk configuration for profile documents.

## Requirements

- Files are written under `storage/app/public`
- The web server serves them through the `public/storage` symlink
- Upload and download features assume this local layout in every environment (local, staging, production)

## Create the storage symlink

From the project root:

```bash
php artisan storage:link
```

This connects:

| Link | Target |
| --- | --- |
| `public/storage` | `storage/app/public` |

After linking, a stored path such as `cvs/abc123.pdf` is available at `/storage/cvs/abc123.pdf` for public preview URLs.

## Verify

```bash
ls -la public/storage
```

You should see a symlink pointing at `storage/app/public`.

## Authorized downloads

Employees download their own CV (and application image) through authenticated routes:

- `GET /employee/profile/cv/download` → `employee.profile.cv.download`
- `GET /employee/profile/application-image/download` → `employee.profile.application-image.download`

These routes always use the signed-in employee's profile. Guests and other personas receive `401`/`403` from auth middleware; missing files return `404`.

## Disk configuration

See `config/filesystems.php` → `disks.public`:

- **Driver:** `local`
- **Root:** `storage/app/public`
- **Visibility:** `public`

Do not point profile document storage at S3 for this project. Keep CareerHub on the local public disk.

## Troubleshooting

| Symptom | Fix |
| --- | --- |
| Upload succeeds but preview/link 404s | Run `php artisan storage:link` |
| Symlink already exists warning | Link is present; remove a broken `public/storage` directory first if needed |
| Download returns 404 | Confirm the employee has uploaded a file and the path exists under `storage/app/public` |
