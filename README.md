# Scoreforge AI

Scoreforge AI is a web-based examination and student performance
analytics application. Teachers can build exams and review results,
while students can take assessments and track their progress. Google
Gemini provides optional AI-generated exam insights.

## Features

### Teachers

-   Manage classes and subjects.
-   Create exams with time limits and generated access codes.
-   Build multiple-choice, true/false, and identification questions.
-   Manage exam status: draft, published, or closed.
-   Review exam analytics and topic performance.
-   Generate and regenerate AI-assisted exam insights through Gemini.

### Students

-   Register and access a student dashboard.
-   Join classes using a class code.
-   Access published class examinations.
-   Join an exam using its access code.
-   Read instructions, complete a timed exam, and view results.
-   Review available performance information.

The application also includes an admin dashboard and role-based access.
Public registration supports teacher and student accounts.

## Technology

  Area             Stack
  ---------------- ------------------------------------
  Backend          PHP and Laravel 13
  Interface        Blade, Tailwind CSS 3, Alpine.js 3
  Asset build      Vite 8
  Database         MySQL
  Authentication   Laravel Breeze
  AI integration   Google Gemini API
  Tests            Pest 4

## Requirements

Before installing AI-Q on another computer, install the following:

-   Git
-   PHP 8.5 with Laravel-required extensions and PDO MySQL
-   Composer 2
-   Node.js 22.12 or newer and npm
-   MySQL Server, XAMPP, or another compatible MySQL installation
-   A code editor such as Visual Studio Code
-   A Gemini API key only if AI-assisted insights will be used

For the configured automated tests, PDO SQLite is also required.

## Installing AI-Q on Another Device

The instructions below assume the project is stored in GitHub and will
be installed on a new development computer.

### Step 1 --- Install the required software

Install Git, PHP, Composer, Node.js/npm, and MySQL on the new computer.

Verify the installation in a terminal:

``` sh
git --version
php -v
composer --version
node -v
npm -v
```

Make sure MySQL is running before continuing.

### Step 2 --- Clone the project from GitHub

Open PowerShell, Command Prompt, Terminal, or the VS Code terminal.

Move to the folder where you want to keep the project.

Example on Windows:

``` powershell
cd C:\Users\YourName\Desktop
```

Clone the repository using the URL shown by GitHub's **Code** button:

``` sh
git clone YOUR_GITHUB_REPOSITORY_URL
```

Enter the project folder:

``` sh
cd Scoreforge_AI
```

If the project was transferred using a USB drive or ZIP file instead of
GitHub, extract/copy the entire project folder and open a terminal
inside it. Do not copy another computer's `vendor` or `node_modules`
folders as a substitute for installing dependencies.

### Step 3 --- Install PHP dependencies

From the project directory, run:

``` sh
composer install
```

Composer will create the `vendor` directory using `composer.lock`.

If Composer reports that a PHP extension is missing, enable/install the
extension it names before continuing.

### Step 4 --- Install JavaScript dependencies

Run:

``` sh
npm ci
```

This installs the frontend dependencies from `package-lock.json`.

If `npm ci` cannot be used because the lock file is missing or
incompatible, use:

``` sh
npm install
```

### Step 5 --- Create the environment file

The `.env` file contains device-specific configuration and must not be
committed to GitHub.

On Windows PowerShell:

``` powershell
Copy-Item .env.example .env
```

On Command Prompt:

``` cmd
copy .env.example .env
```

On macOS or Linux:

``` sh
cp .env.example .env
```

### Step 6 --- Generate the Laravel application key

Run:

``` sh
php artisan key:generate
```

Laravel will automatically place the generated key in `.env`.

Do not copy an application key from public documentation or commit
`.env` to GitHub.

### Step 7 --- Create the MySQL database

Start MySQL.

Create an empty database named:

``` text
scoreforge_ai
```

For example, in phpMyAdmin:

1.  Open phpMyAdmin.
2.  Select **Databases**.
3.  Enter `scoreforge_ai`.
4.  Click **Create**.

You can also create it through MySQL:

``` sql
CREATE DATABASE scoreforge_ai;
```

### Step 8 --- Configure the database connection

Open `.env` and configure the MySQL connection for the new computer.

Example:

``` dotenv
APP_NAME="Scoreforge AI"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=scoreforge_ai
DB_USERNAME=root
DB_PASSWORD=
```

The username and password must match the MySQL installation on that
device.

A typical local XAMPP installation may use `root` with an empty
password, but use the actual credentials configured on the computer.

### Step 9 --- Create the database tables

For a new installation with an empty database, run:

``` sh
php artisan migrate
```

This creates the tables required by the project.

Do **not** use `php artisan migrate:fresh` on a database containing data
you want to keep because it drops the existing tables.

### Step 10 --- Optional: move existing AI-Q data to the new device

Skip this step if you want a completely new/empty installation.

If you want the new computer to contain the existing teachers, students,
classes, exams, questions, attempts, and analytics, export the
`scoreforge_ai` database from the old computer and import it into the
new computer.

With phpMyAdmin:

**Old computer**

1.  Open phpMyAdmin.
2.  Select `scoreforge_ai`.
3.  Choose **Export**.
4.  Use the SQL format.
5.  Save the `.sql` file.

**New computer**

1.  Create/select the `scoreforge_ai` database.
2.  Choose **Import**.
3.  Select the exported `.sql` file.
4.  Start the import.

If you import a complete existing database, do not run `migrate:fresh`.
After importing, run:

``` sh
php artisan migrate
```

Laravel will only apply migrations that have not already been recorded.

### Step 11 --- Configure Gemini AI (optional)

Gemini is only required for AI-assisted features. The normal examination
and local analytics functionality can run without it.

Add the Gemini settings to `.env`:

``` dotenv
GEMINI_API_KEY=your_gemini_api_key
GEMINI_MODEL=your_working_gemini_model
```

Use a Gemini model that is available to the API account being used by
the project.

Never commit the real Gemini API key to GitHub.

After editing `.env`, run:

``` sh
php artisan config:clear
```

### Step 12 --- Clear Laravel caches

After installation or after changing environment settings, run:

``` sh
php artisan optimize:clear
```

### Step 13 --- Start AI-Q

The simplest development command is:

``` sh
composer run dev
```

This starts the Laravel development services configured by the project,
including the web application and Vite development server.

Open the Laravel URL printed in the terminal, normally:

``` text
http://127.0.0.1:8000
```

Keep the terminal running while using the system.

### Alternative: run Laravel and Vite separately

Terminal 1:

``` sh
php artisan serve
```

Terminal 2:

``` sh
npm run dev
```

Then open:

``` text
http://127.0.0.1:8000
```

### Step 14 --- Verify the installation

Test the following:

1.  Open the AI-Q landing/login page.
2.  Register or log in as a teacher.
3.  Open the Teacher Command Center.
4.  Open **Classes** and verify class creation.
5.  Create or open an exam.
6.  Add questions and publish the exam.
7.  Log in as a student.
8.  Join a class using its class code if applicable.
9.  Enter an exam code from the Student Overview or open a published
    class exam.
10. Submit an exam and verify that the result is saved.
11. Log back in as the teacher and verify analytics.
12. If Gemini is configured, test AI-assisted exam insights.

## Updating an Existing Installation on Another Device

If AI-Q is already installed on the computer and you only need the
newest code, open the project folder and run:

``` sh
git pull
composer install
npm ci
php artisan migrate
php artisan optimize:clear
```

Then start the project:

``` sh
composer run dev
```

If `package-lock.json` changed, `npm ci` ensures that the device uses
the dependency versions recorded by the project.

## Frontend Production Build

For a compiled frontend build:

``` sh
npm run build
```

During active development, use:

``` sh
npm run dev
```

or:

``` sh
composer run dev
```

## Basic Workflow

1.  Register or sign in as a teacher.
2.  Create/manage a class and subject.
3.  Create an exam and add questions.
4.  Publish the exam.
5.  Students may join a class using its class code.
6.  Students access an official exam through the supported exam flow.
7.  Students complete the assessment and review the result.
8.  Teachers review examination analytics.
9.  Teachers may generate AI-assisted insights when Gemini is configured
    and available.

## Testing

Run the test suite:

``` sh
php artisan test --compact
```

Run a specific test file:

``` sh
php artisan test --compact tests/Feature/ExampleTest.php
```

`phpunit.xml` configures an in-memory SQLite database for tests.

## Project Structure

``` text
app/Http/Controllers/   Authentication, teacher, and student request handling
app/Models/             Application data models
app/Services/           Analytics and Gemini integration
config/                 Application and integration configuration
database/migrations/    Database schema
resources/views/        Blade pages and components
resources/js/           Frontend JavaScript
routes/                 Web, authentication, and role-specific routes
tests/                  Feature and unit tests
```

## Common Installation Problems

### `php` is not recognized

PHP is not installed or its directory is not in the system PATH.

### `composer` is not recognized

Install Composer and restart the terminal.

### `npm` or `node` is not recognized

Install Node.js and restart the terminal.

### MySQL connection refused

Make sure MySQL is running and verify `DB_HOST`, `DB_PORT`,
`DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.

### `Unknown database 'scoreforge_ai'`

Create the `scoreforge_ai` database before running migrations.

### `No application encryption key has been specified`

Run:

``` sh
php artisan key:generate
```

### `Vite manifest not found` or frontend styles are missing

During development run:

``` sh
npm run dev
```

or build the assets:

``` sh
npm run build
```

### Changes are not appearing

Run:

``` sh
php artisan optimize:clear
```

Then refresh the browser. If Vite is being used in development, make
sure `npm run dev` is still running.

### Gemini AI does not respond

Check `GEMINI_API_KEY` and `GEMINI_MODEL` in `.env`, then run:

``` sh
php artisan config:clear
```

Gemini may also temporarily reject requests because of quota limits or
provider availability. Core exam and local analytics features should
remain usable.

## Configuration and Security Notes

-   Never upload `.env` to GitHub.
-   Never commit database passwords or Gemini API keys.
-   Use `.env.example` as the public configuration reference.
-   Use `php artisan migrate`, not `migrate:fresh`, when existing data
    must be preserved.
-   Database exports can contain student and examination data; store and
    transfer them securely.
-   The default mail configuration may write messages to the application
    log. Configure a mail provider if password-reset emails must
    actually be delivered.
