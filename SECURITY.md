# Security Policy

Bali Project is a learning-oriented native PHP application. While it applies common security practices — prepared statements, password hashing, CSRF tokens, and server-side validation — it is **not certified** for high-risk production use without review.

## Reporting a Vulnerability

Please **do not open a public issue** for security problems.

Instead, report privately by emailing the maintainer:

**Qoid Rif'at — qoidrifat23@gmail.com**

Please include:

- Affected file(s) and version/branch.
- A clear description of the vulnerability.
- Steps to reproduce (sanitized — no real credentials).
- Suggested fix, if known.

You should receive an acknowledgement within **72 hours**. Disclosure will follow a reasonable coordinated timeline so a fix can be prepared first.

## Security Notes for Users

- The app reads database credentials from environment variables (`BALI_DB_*`) with local fallbacks. For production, set real credentials in the hosting environment — never commit them.
- SQL dumps live under `storage/private/` and are blocked by `.htaccess`. For production, move them outside the public web root when possible.
- The `.htaccess` protection (blocking `config/`, `includes/`, `database/`, `partials/`, `storage/`, and `*.md`) only applies on Apache. Non-Apache servers need equivalent rules.
- Enable HTTPS and set `display_errors=Off` / `log_errors=On` in production.

## Supported Versions

| Version | Supported |
|---------|-----------|
| `main`  | ✅ Active development |

There are no tagged releases yet; the `main` branch is the canonical source.
