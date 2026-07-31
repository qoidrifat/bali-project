# Database

## Canonical Dump

The canonical database dump for local setup is:

```text
../storage/private/database/bali.sql
```

The older `database/bali.sql` dump has been archived to:

```text
../storage/private/archive/database/bali.legacy.sql
```

## Safety Notes

- Keep SQL dumps out of production public routes.
- The project `.htaccess` blocks direct access to `storage/` and `.sql` files as an additional safety layer.
- For production, move dumps outside the public web root whenever your hosting supports it.

## Manual Migrations

Optional feature migrations live in this folder as additive `*.sql` files. Review each file before running it, and back up your database first. See the table in the project README for which migration enables which feature.
