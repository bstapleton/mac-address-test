# MAC address test tasks

## Setup
1. `composer install`
2. `cp .env.example .env`
3. Check that the `DB_CONNECTION` value in the `.env` file is `sqlite`
4. `php artisan key:generate`
5. `cp .env .env.testing`
6. Replace the `DB_CONNECTION` value in the `.env.testing` file with `sqlite_test` (rather than sqlite)
7. Run `sail up -d`

There's no dependency on MySQL/Mariadb/Postgres, so it should spin up pretty quickly. Front-end bits should be accessible on localhost (hopefully - that's how it was for me, but I use Sail a lot for managing my containers, so I should be considered a tainted sample tbh).

To run the command, you can use `sail artisan app:import-ouis`, and it's on a schedule to run at 3am every day. If you want to test the scheduling without having to wait around for 3am (I wouldn't blame you), then update the `app/routes/console.php` file so that instead of `dailyAt('03:00')` it's `everyFiveMinutes()`, then run `sail artisan schedule:work` and wait for the next 5-minute interval to roll around. The process takes about 45 seconds for an update and over a minute when running fresh.
