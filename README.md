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

