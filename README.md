# Lintasarta — Enterprise Attendance & Scheduling System

A robust Laravel-based enterprise application for managing employee attendance, shift scheduling, and workforce management. Features include real-time attendance tracking, dynamic scheduling, comprehensive reporting (PDF/Excel), and advanced security logging.

## Key Features
- 👥 **User Management**: Role-based access control (Admin, Coordinator, User)
- 📊 **Dashboard & Analytics**: Real-time attendance stats and schedule overview
- 📅 **Dynamic Scheduling**: Flexible shift management and calendar integration
- 📲 **Attendance System**: Track attendance with status monitoring
- 📑 **Export & Reports**: Generate PDF/Excel reports for attendance & schedules
- 🔒 **Enterprise Security**: 
  - Session management & suspicious IP blocking
  - Activity logging & audit trails
  - Login attempt monitoring
  - Password policies & OTP support

## Tech Stack
### Backend
- PHP 8.2+
- Laravel 12
- MySQL/PostgreSQL
- Redis (queue/cache/session) [Optional]

### Frontend
- TailwindCSS
- Vite (build system)
- Chart.js (analytics)
- FullCalendar (scheduling)
- Lucide icons

### Tools & Integrations
- Maatwebsite Excel (spreadsheet exports)
- DomPDF (PDF generation)
- Laravel Queue (background jobs)

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

## Development Guidelines

### Code Standards
- Follow PSR-12 coding standards
- Use Laravel best practices and design patterns
- Document complex business logic and important methods
- Write tests for critical features

### Working with the Codebase
1. **Models & Database**
   - Use migrations for schema changes
   - Document model relationships
   - Follow Laravel's Eloquent conventions
   - Use factories & seeders for testing

2. **Controllers & Routes**
   - Keep controllers focused (single responsibility)
   - Use form requests for validation
   - Implement proper error handling
   - Document API endpoints

3. **Views & Frontend**
   - Follow component-based structure
   - Use Blade components for reusability
   - Keep JavaScript modular
   - Optimize assets for production

### Testing
- Run `php artisan test` before commits
- Write feature tests for critical paths
- Use factories for test data
- Mock external services

### Security Considerations
- Follow OWASP security guidelines
- Implement rate limiting
- Validate all inputs
- Use proper authentication middleware
- Keep dependencies updated

## Deployment
For production deployment instructions and environment-specific configurations, see [DEPLOYMENT.md](DEPLOYMENT.md).

## Troubleshooting
- If assets don't load, ensure `npm run dev` is running or use `npm run build`
- Ensure `storage` and `bootstrap/cache` are writable
- Check logs in `storage/logs` for errors
- For queue issues, verify Redis connection and worker status

## Support
For issues and feature requests, please use the GitHub issue tracker.

## License
This project is proprietary software. All rights reserved.
