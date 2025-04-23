# MAC address test tasks

## Setup
1. `composer install`
2. `cp .env.example .env`
3. Check that the `DB_CONNECTION` value in the `.env` file is `sqlite`
4. `php artisan key:generate`
5. `cp .env .env.testing`
6. Replace the `DB_CONNECTION` value in the `.env.testing` file with `sqlite_test` (rather than sqlite)
7. Run `sail up -d`
8. Run `npm install`

There's no dependency on MySQL/Mariadb/Postgres, so it should spin up pretty quickly. Front-end bits should be accessible on localhost (hopefully - that's how it was for me, but I use Sail a lot for managing my containers, so I should be considered a tainted sample tbh).

## Back-end bits

To run the command, you can use `sail artisan app:import-ouis`, and it's on a schedule to run at 3am every day. If you want to test the scheduling without having to wait around for 3am (I wouldn't blame you), then update the `app/routes/console.php` file so that instead of `dailyAt('03:00')` it's `everyFiveMinutes()`, then run `sail artisan schedule:work` and wait for the next 5-minute interval to roll around. The process takes about 45 seconds for an update and over a minute when running fresh.

Once you have some data imported, you can hit the endpoints at `localhost/api/identifier` to see the results. The GET takes a query parameter of `mac_address`. The POST, you need to set the request body to an array. See the following example:

```json
{
    "mac_addresses": ["00:00:00:00:00:00", "12:34:AB:99:00:00"]
}
```

## Front-end bits

Start the UI bits up by running `npm run dev`. It should be accessible at `http://localhost`, but the console will tell you where.

The interface is pretty basic, but should explain everything you need. You can enter a single string search, or search for multiple by comma-delimiting your search.

You'll get a (handled) error if it can't find any matches. If it's a partial match (f.ex you searched for three, and it only found two), then you'll see two results. It doesn't currently tell you about partial success/failures.
