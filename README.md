# Scoreforge AI

Scoreforge AI is a web-based examination and student performance analytics application. Teachers can build exams and review results, while students can take assessments and track their progress. Google Gemini provides optional AI-generated exam insights.

## Features

### Teachers

- Manage classes and subjects.
- Create exams with time limits and generated access codes.
- Build multiple-choice, true/false, and identification questions.
- Manage exam status: draft, published, or closed.
- Review exam analytics, topic performance, and student performance predictions.
- Generate and regenerate AI exam insights through Gemini.

### Students

- Register and access a student dashboard.
- Join an exam using its access code.
- Read instructions, complete a timed exam, and view results.
- Track performance trends, rankings, academic readiness, and study recommendations.

The application also includes an admin dashboard and role-based access. Public registration supports teacher and student accounts.

## Technology

| Area | Stack |
| --- | --- |
| Backend | PHP and Laravel 13 |
| Interface | Blade, Tailwind CSS 3, Alpine.js 3 |
| Asset build | Vite 8 |
| Database | MySQL |
| Authentication | Laravel Breeze |
| AI integration | Google Gemini API |
| Tests | Pest 4 |

Performance predictions, readiness assessments, and study recommendations use application-defined calculations and rules. Gemini is used separately to generate exam insights.

## Requirements

- PHP 8.5 for the project environment, with Laravel's required extensions and PDO MySQL.
- Composer 2.
- Node.js 22.12 or newer and npm, compatible with the installed Vite version.
- A running MySQL server.
- PDO SQLite to run the configured in-memory database tests.
- A Gemini API key if you want to generate AI exam insights.

## Local setup

### 1. Get the project

Clone this repository using the URL from GitHub's **Code** button, then open a terminal in the project folder.

### 2. Install dependencies

```sh
composer install
npm ci
```

### 3. Configure the environment

Copy `.env.example` to `.env`.

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

On macOS or Linux:

```sh
cp .env.example .env
```

Set the application name and database connection in `.env`. Create an empty MySQL database named `scoreforge_ai` first, then use your local database credentials:

```dotenv
APP_NAME="Scoreforge AI"
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=scoreforge_ai
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

Generate the application key and create the database tables:

```sh
php artisan key:generate --no-interaction
php artisan migrate --no-interaction
```

The example configuration uses database-backed sessions, cache, and queues, so run the migrations before using the application.

### 4. Configure Gemini (optional)

Add these settings to `.env` to enable AI exam insights:

```dotenv
GEMINI_API_KEY=your_gemini_api_key
GEMINI_MODEL=gemini-2.5-flash
```

The model above is the default in `config/services.php`. Use a model available to your API account. Without a configured key, the Gemini service reports that AI insights are unavailable; the local analytics calculations do not require this key.

After changing environment settings, clear cached configuration:

```sh
php artisan config:clear --no-interaction
```

### 5. Start development

```sh
composer run dev
```

This starts the application server, queue listener, and Vite development server. Open the address printed by the application server in your terminal.

To build frontend assets:

```sh
npm run build
```

## Basic workflow

1. Register a teacher account.
2. Create a class and subject, then create an exam and add questions.
3. Publish the exam and share its access code with students.
4. Register or sign in as a student and enter the exam code.
5. Complete the assessment and review the result.
6. Sign in as the teacher to review analytics and optionally generate an AI insight.

## Testing

Run the test suite:

```sh
php artisan test --compact
```

Run a specific test file:

```sh
php artisan test --compact tests/Feature/ExampleTest.php
```

`phpunit.xml` configures an in-memory SQLite database for tests.

## Project structure

```text
app/Http/Controllers/   Authentication, teacher, and student request handling
app/Models/             Application data models
app/Services/           Analytics, predictions, recommendations, and Gemini
config/                 Application and integration configuration
database/migrations/    Database schema
resources/views/        Blade pages and components
resources/js/           Frontend JavaScript
routes/                 Web, authentication, and role-specific routes
tests/                  Feature and unit tests
```

## Configuration notes

- Keep `.env` and API keys out of version control. The repository already ignores `.env`; use `.env.example` as the setup reference.
- The default mail configuration writes messages to the application log. Configure a mail provider to deliver password-reset emails.
- If frontend changes are missing, run `npm run dev` during development or rebuild with `npm run build`.
- If the database connection fails, check that MySQL is running, the database exists, and the credentials in `.env` are correct.
