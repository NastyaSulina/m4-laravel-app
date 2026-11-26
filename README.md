# Staylist

Staylist is a Laravel application for organising accommodation options into selections when planning a trip. You can save places with links, photos and notes, set their status, and add criteria to each selection. Share selections with other registered users to view and edit them together, while the owner manages access.

## Requirements

- PHP 8.5 with the standard Laravel extensions, SQLite support (`pdo_sqlite`), GD and EXIF.
- Composer 2, Git, and Node.js 20.19+ (20.x) or 22.12+ with npm.
- Laravel Herd for local hosting; the instructions below use its default `~/Herd` directory.

The project uses Laravel 13, SQLite, Blade, Tailwind CSS 4, Alpine.js 3 and Vite 8. Composer and npm install the project dependencies.

## Install on local development

1. Clone the project into Herd's directory:

   ```bash
   cd ~/Herd
   git clone https://github.com/NastyaSulina/m4-laravel-app.git
   cd m4-laravel-app
   ```

   Select PHP 8.5 for this site in Herd, and make sure `php -v` also reports PHP 8.5 in your terminal.

2. Install PHP dependencies and create the local configuration:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   Set these values in `.env`. Keep `DB_CONNECTION=sqlite`; no separate database server is needed.

   ```dotenv
   APP_NAME=Staylist
   APP_URL=http://m4-laravel-app.test
   ```

3. Create the database, load demo data, and make uploaded images accessible:

   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   php artisan storage:link
   ```

4. Install and build the frontend assets:

   ```bash
   npm ci
   npm run build
   ```

5. Open [m4-laravel-app.test](http://m4-laravel-app.test). Herd serves the site automatically. Register an account or sign in with the seeded demo account: **test@example.com** / **password**.

6. To process sharing notifications, keep a queue worker running in a separate terminal from the project directory:

   ```bash
   php artisan queue:work
   ```

   The default `MAIL_MAILER=log` writes emails to `storage/logs/laravel.log` instead of sending them.

During frontend development, run `npm run dev` instead of rebuilding after each change. To run the tests, use `php artisan test --compact`.
