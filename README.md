# Employee Appreciation Platform — Laravel + Blade

Server-rendered (Blade) edition of the Internal Employee Appreciation Platform.
This is a **single Laravel application** — no Vue SPA and no JSON API surface.
All pages are rendered with Blade and styled with Tailwind (via CDN).

It reuses the original domain layer unchanged: Eloquent models, services
(`AppreciationService`, `AuthService`, `SettingService`, …), repositories,
migrations, and seeders.

## Stack

- **Laravel 11**, PHP 8.2+
- **Blade** views + **Tailwind** (CDN — no Node build step)
- **Session authentication** (`web` guard); IIS Windows Auth for employees
- MySQL · Spatie permissions · Spatie activitylog · LDAP (optional)

## Features

- Windows auto-login (IIS) and admin/username form login
- Dashboard with quota + latest appreciations (+ leaderboard for admins)
- Employee search and profiles
- Send appreciation with a **required reason** (radio) + optional message
- My history (received / sent)
- Admin: platform settings (incl. **logo upload**), user management
- Super-admin: **appreciation reasons** management (CRUD)
- Bilingual UI (English / Arabic) with RTL support

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# configure DB in .env, then:
php artisan migrate --seed
php artisan storage:link        # required for logo uploads
php artisan serve
```

Default admin (from seeders): `admin` / `Admin@12345` (super-admin).
Demo employees: `john.doe`, `jane.smith`, … / `Employee@12345`.

## Authentication

- **Employees** sign in automatically via IIS Windows Authentication. The
  `GET /auth/windows` route reads the IIS server variables
  (`LOGON_USER` / `AUTH_USER` / `REMOTE_USER`), normalizes `DOMAIN\user` →
  `user`, provisions the account (LDAP sync or auto-create), and logs them into
  the session.
- **Admins** use the username/password form at `/login`.

## IIS deployment notes

- App pool: **No Managed Code**; site physical path → `public/`.
- Enable **Anonymous + Windows** authentication; scope Windows-only to the
  `auth/windows` path (Anonymous disabled there) via a `web.config`
  `<location>` block. Unlock the auth sections first
  (`appcmd unlock config /section:…/windowsAuthentication`).
- Add the site host to the browser **Local Intranet** zone for silent SSO.
- Run `php artisan storage:link` so uploaded logos are served from `/storage`.

> Tailwind is loaded from a CDN for simplicity. For an air-gapped intranet,
> swap the CDN `<script>` in `resources/views/layouts/*.blade.php` for a
> compiled stylesheet.
