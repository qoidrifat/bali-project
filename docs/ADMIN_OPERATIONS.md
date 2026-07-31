# Bali Project — Admin Operations

Operational notes for safe admin use on development and staging environments.

## Active Admin Modules

- Dashboard: `admin/index.php`
- Users and roles: `admin/users/index.php`, `admin/roles/index.php`
- Destinations: `admin/destinations/index.php`
- Categories: `admin/categories/index.php`
- Tickets/packages: `admin/tickets/index.php`
- Bookings: `admin/bookings/index.php`
- Invoices: `admin/invoices/index.php`
- Payments: `admin/payments/index.php`
- Gallery: `admin/gallery/index.php`
- Articles: `admin/articles/index.php`
- Messages: `admin/messages/index.php`
- Settings: `admin/settings/index.php`
- Reports: `admin/reports/index.php`
- Activity log: `admin/activity/index.php`

## Prepared Migrations

```text
database/2026_06_09_create_admin_content_tables.sql
database/2026_06_09_create_contact_messages_table.sql
database/2026_06_09_create_admin_activity_logs_table.sql
```

All SQL above is additive: it creates tables only if they do not exist and never deletes existing data. Review each file before running it and back up the database first.

## CSV Export

- Booking CSV: `admin/bookings/export.php`
- Invoice CSV: `admin/invoices/export.php`

Exports are capped at the 1,000 most recent rows to stay lightweight on shared hosting.

## Database Backup

Run from the project root:

```sh
php scripts/backup_database.php
```

Output is stored in:

```text
storage/private/backups/
```

Notes:

- The script requires `mysqldump` to be available on `PATH`.
- The script never drops databases.
- Backup files must not be moved to a public production root.
- Never commit backup files to Git.

## Email Notification

The notification scaffold lives at:

```text
includes/notifications.php
```

Default mode is **log-only**: booking/invoice updates are recorded to:

```text
storage/private/mail/
```

To enable native PHP `mail()`:

```text
BALI_MAIL_ENABLED=true
```

Use a proper SMTP/transactional email provider before going to production.

## Manual Checklist

- [ ] Sign in as an admin.
- [ ] Open the admin dashboard.
- [ ] Create a destination category.
- [ ] Create a ticket/package.
- [ ] Create a draft article.
- [ ] Save a non-sensitive site setting.
- [ ] Update a booking status.
- [ ] Export booking CSV.
- [ ] Export invoice CSV.
- [ ] Review the activity log.
- [ ] Run a database backup on local/staging.
