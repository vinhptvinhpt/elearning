\# CLAUDE.md - Project Knowledge Base



\## 1. Project Overview

\- \*\*Name/Type:\*\* E-Learning EASIA - Monolith Web Application (Laravel + Moodle Integration)

\- \*\*Description:\*\* A comprehensive enterprise e-learning management system (LMS) integrating Laravel backend with Moodle LMS (v3.7). Manages training programs, competency frameworks, course enrollment, certification generation, surveys, self-assessments, and organizational hierarchies with HiStaff HR system integration.

\- \*\*Key Stack:\*\*

&nbsp; - Backend: PHP 7.1.3+ | Laravel 5.8

&nbsp; - Frontend: Vue.js 2.6.11 | Bootstrap-Vue 2.2.2 | Bootstrap 4.4.1

&nbsp; - Database: MySQL (accessed via Eloquent ORM + Direct Moodle DB queries)

&nbsp; - Infrastructure: Docker (docker-compose.yml), Nginx, Python (certificate generation)

&nbsp; - External Integrations: Moodle 3.7, Azure Storage (SAS URLs), HiStaff HR System



\## 2. Key Architectural Patterns

\- \*\*Architecture:\*\* MVC with Repository Pattern (Interface-based repositories in `/app/Repositories`)

\- \*\*Authentication:\*\* JWT (tymon/jwt-auth 0.5.\*) for API + Laravel Session-based auth for web

\- \*\*Data Layer:\*\* 

&nbsp; - Eloquent ORM for Laravel tables (prefixed `tms\_\*`)

&nbsp; - Direct queries for Moodle tables (prefixed `mdl\_\*`)

&nbsp; - Repository Pattern: Interfaces (`ICommonInterface`, `IMdlCourseInterface`, etc.) → Concrete Repositories

\- \*\*Frontend Integration:\*\* Vue.js 2 SPA with Vue Router 2.7.0 (catch-all routes to Vue in `routes/web.php`)

\- \*\*Middleware:\*\* Custom middleware including `HistaffIntegrationMiddleware`, `CheckToken`, session-based `auth:web`, `clearance`

\- \*\*Permission System:\*\* Spatie Laravel Permission 2.37 (Roles \& Permissions)



\## 3. Critical Directory Map

\- `/app/Http/Controllers/Backend/`: Backend admin controllers (System, Course, Trainning, Notification, etc.)

\- `/app/Http/Controllers/Api/`: API controllers (TaskController for cron jobs, BackgroundController)

\- `/app/Repositories/`: Interface-based repositories implementing business logic abstraction

\- `/app/Space/helpers.php`: Custom helper functions (autoloaded via composer.json)

\- `/app/Mdl\*.php`: Eloquent models mapping to Moodle database tables (mdl\_course, mdl\_user, etc.)

\- `/app/Tms\*.php`: Eloquent models for custom TMS (Training Management System) tables

\- `/resources/js/components/`: Vue.js components organized by domain (education, system, etc.)

\- `/public/lms/`: Moodle LMS installation directory

\- `/python/generate.py`: Python script for automated certificate image generation (runs via cron)

\- `/config/constants.php`: Application-specific configuration constants

\- `/storage/app/public/cron/`: JSON flag files controlling cron job execution states



\## 4. Development Commands

\- \*\*Start App (Docker):\*\* `sudo docker-compose up -d`

\- \*\*Remote into App Container:\*\* `sudo docker-compose exec app bash`

\- \*\*Install Dependencies:\*\* `composer install` \&\& `npm install`

\- \*\*Initial Setup:\*\* 

&nbsp; - `php artisan key:generate`

&nbsp; - `php artisan config:cache`

&nbsp; - `php artisan storage:link`

\- \*\*Run Tests:\*\* `phpunit` or `./vendor/bin/phpunit`

\- \*\*Build Assets:\*\* 

&nbsp; - Development: `npm run dev` or `npm run watch`

&nbsp; - Production: `npm run prod`

\- \*\*Database Import (Docker):\*\* `docker exec -i <db\_container\_name> mysql -u <user> -p<password> <database> < /path/to/sql/file`



\## 5. Coding Conventions \& Style

\- \*\*Naming:\*\* 

&nbsp; - PHP: PSR-4 autoloading, `PascalCase` for classes, `camelCase` for methods

&nbsp; - Database: `snake\_case` for columns/tables

&nbsp; - Vue Components: `PascalCase` filenames (e.g., `CourseStatisticComponent.vue`)

&nbsp; - Moodle tables: `mdl\_\*` prefix | Custom tables: `tms\_\*` prefix

\- \*\*Type Safety:\*\* Minimal strict typing (PHP 7.1 era), type hints used sparingly in newer code

\- \*\*Code Style:\*\* PSR-compliant (no explicit PSR-12 config), ESLint config present (`.eslintrc`)

\- \*\*Specific Rules:\*\*

&nbsp; - Controllers delegate to Repositories (e.g., `CourseController` → `MdlCourseRepository`)

&nbsp; - API responses use `ResponseModel` ViewModel pattern

&nbsp; - Vue routes use catch-all pattern: `Route::get('/{vue?}')` forwarding to SPA

&nbsp; - Cron tasks use middleware `CheckToken` for security

&nbsp; - Never directly modify Moodle core; interact via models/API



\## 6. Critical Integration Points

\- \*\*Moodle Integration:\*\* 

&nbsp; - Dual database access: Laravel tables + Moodle tables via Eloquent models

&nbsp; - Config file: `/public/lms/config.php` (must match `.env` DB credentials)

&nbsp; - Moodle cron runs via: `php /var/www/public/lms/admin/cli/cron.php`

\- \*\*HiStaff HR System:\*\* 

&nbsp; - Employee sync via `TmsOrganizationEmployee`, `TmsOrganizationHistaffMapping`

&nbsp; - Middleware: `HistaffIntegrationMiddleware`

&nbsp; - Sync controller: `SyncDataController`

\- \*\*Azure Storage:\*\* SAS URL generation cron for video storage (`autogenerateSASAzure`)

\- \*\*Certificate Generation:\*\* Python-based image generation (`/python/generate.py`) runs every minute via cron



\## 7. Cron Job System

\- \*\*Control Mechanism:\*\* JSON flag files in `/storage/app/public/cron/` (e.g., `enroll\_trainning.json`, `enroll\_user.json` with `{"flag":"stop"}`)

\- \*\*Execution:\*\* Via `php artisan route:call /api/cron/...` with token authentication

\- \*\*Critical Jobs:\*\*

&nbsp; - User enrollment: `autoEnrol`, `autoEnrolCron`

&nbsp; - Competency management: `autoAddTrainningUser`, `completeTrainning`

&nbsp; - Course completion: `completeCourse`, `completeCourseSingle`

&nbsp; - Notifications: Multiple mail cron jobs (sendESEC, sendRemindERC, sendInvitation, etc.)

&nbsp; - Certificate generation: Python script every minute

&nbsp; - Azure SAS URL refresh: Daily at midnight



\## 8. Key Business Domains

\- \*\*Trainning (Competency Framework):\*\* Multi-course training programs assigned to roles/organizations

\- \*\*Courses:\*\* Moodle-backed courses with custom enrollment logic, statistics, certificates

\- \*\*Organizations:\*\* Hierarchical structure (City → Branch → Sale Room) with employee assignments

\- \*\*Roles \& Permissions:\*\* Spatie-based RBAC with custom role-course mappings

\- \*\*Surveys \& Self-Assessment:\*\* Custom questionnaire system independent of Moodle

\- \*\*Notifications:\*\* Queue-based email system with cron processing

\- \*\*Certificates:\*\* Auto-generated via Python script, stored as images with metadata in DB



\## 9. Environment Setup Requirements

\- PHP 7.1.3+ with extensions: curl, json, openssl, soap

\- MySQL database (separate or same instance for Laravel + Moodle)

\- Node.js \& NPM (for frontend asset compilation)

\- Python with pip packages: `mysql-connector-python`, `Pillow`

\- Docker \& Docker Compose (optional but recommended)

\- Nginx configured to serve `/public` directory



\## 10. Important Configuration Files

\- `.env`: Laravel environment config (DB, mail, app settings)

\- `/public/lms/config.php`: Moodle database configuration (must match `.env`)

\- `/config/constants.php`: Application-specific constants and business logic configs

\- `docker-compose.yml`: Container orchestration (app, nginx, mysql services)

\- `webpack.mix.js`: Laravel Mix configuration for asset compilation



\## 11. Security Notes

\- JWT tokens for API authentication (check token middleware on cron routes)

\- Session-based auth for web routes with `clearance` middleware

\- Never commit `.env` file (use `.env.example` as template)

\- Cron jobs protected by token parameter in URLs

\- File permissions critical: `public`, `storage`, `vendor`, `node\_modules` need write access



\## 12. Common Pitfalls \& Troubleshooting

\- \*\*Dual DB Config:\*\* Always update BOTH `.env` and `/public/lms/config.php` for DB changes

\- \*\*Cron Flag Files:\*\* Missing JSON flag files in `/storage/app/public/cron/` will break cron jobs

\- \*\*Asset Compilation:\*\* Run `npm run dev` after any Vue component changes

\- \*\*Storage Link:\*\* Run `php artisan storage:link` after fresh install

\- \*\*Moodle Cron:\*\* Must run Moodle's own cron for course recyclebin and cleanup tasks

\- \*\*Python Script:\*\* Ensure Python dependencies installed and script has DB access for certificate generation

