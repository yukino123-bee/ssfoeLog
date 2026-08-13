# SSFO eLog — local setup

## Requirements

- PHP 8.1+ with `mysqli`
- Composer
- MySQL 8+ or MariaDB

## Run locally

1. Start MySQL.
2. Copy `.env.example` to `.env` and adjust the database values if needed.
3. Run `composer install` once to install the PHP dependencies.
4. Import `database/complete_schema.sql`,
   `database/migrations/002_request_logs_and_access.sql`, and
   `database/seeders.sql` into MySQL if the database has not been created yet.
5. Run `composer start`, then open <http://127.0.0.1:8080>.

The current local database includes the default administrator:

- Email: `admin@ssfo.local`
- Password: `admin123`

Change the default password after first login. SMS is disabled locally unless
explicitly enabled in `.env`.
