# Lintasarta — Attendance & Scheduling Dashboard

Project built on Laravel 12 with a Tailwind + Vite frontend. This application provides attendance monitoring, shift & schedule management, exports (Excel/PDF), and admin user management.

## Tech stack
- PHP 8.2+
- Laravel 12
- TailwindCSS + Vite
- Chart.js, FullCalendar, Lucide icons
- Maatwebsite Excel, barryvdh/laravel-dompdf

## Prerequisites
- PHP 8.2+
- Composer
- Node.js (v16+) and npm
- A web server (Laragon, Valet, Sail, or built-in PHP server)

## Local setup (Windows / PowerShell)

1. Clone the repo and enter folder:

```powershell
git clone <repo-url> lintasarta
cd lintasarta
```

2. Install PHP dependencies:

```powershell
composer install
```

3. Copy `.env` and configure database and other env vars:

```powershell
cp .env.example .env
# edit .env (DB_CONNECTION, DB_DATABASE, APP_URL, MAIL settings)
```

4. Generate app key and run migrations (and seeders if needed):

```powershell
php artisan key:generate
php artisan migrate --seed
```

5. Install Node dependencies and start Vite in dev mode:

```powershell
npm install
npm run dev
```

6. Start local server:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
# open http://127.0.0.1:8000
```

## Useful scripts
- `composer run-script dev` — start server + queue + vite concurrently (project `composer.json` defines this)
- `npm run dev` — start Vite
- `npm run build` — build assets for production
- `composer test` / `php artisan test` — run tests

## Database
- You can use SQLite for quick local development (project scaffolding supports it). Otherwise configure MySQL/Postgres in `.env`.

## Notes
- If assets don't load, ensure `npm run dev` is running or use `npm run build` to produce `public/build` files.
- Ensure `storage` and `bootstrap/cache` are writable by your web server.

## Contributing
- Follow PSR-12 coding standards and run tests before submitting PRs.

---
If you want, I can add:
- A `development.md` with common dev tasks and examples.
- Deployment instructions for a target environment (cPanel, DigitalOcean, Forge, etc.).
- Seed user credentials and a small script to create an admin user.
