# Bali Project — Developer Guide

A developer guide for new contributors who want to run, understand, and extend Bali Project.

## 1. Project Summary

Bali Project is a native PHP + MySQL/MariaDB tourism website with file-based routing: every `.php` file in the root (or a supported subfolder) can act as a direct endpoint.

**Key capabilities:**

- Bali tourism homepage.
- Destination listing and detail pages from the database.
- Search forms for flights, bus tickets, hotels, and car rentals.
- Search result pages built on prepared statements.
- Login, register, logout, sessions, and CSRF protection.
- Admin dashboard with role-based access.
- Destination CRUD with validated image uploads.
- Internal booking flow with HTML invoices.
- Destination reviews and ratings for signed-in users.
- Shared partials for head, navbar, and footer.
- Custom CSS design system with light/dark mode.
- Apache `.htaccess` protection for sensitive files and static asset caching.

## 2. Tech Stack

| Area | Technology |
|---|---|
| Backend | Native PHP |
| Database | MySQL / MariaDB |
| Driver | `mysqli` |
| Frontend | HTML, CSS, JavaScript |
| Styling | Custom CSS with internal design tokens |
| Local server | Laragon / XAMPP / Apache, or the PHP built-in server |
| Dependency manager | None (no Composer or npm) |

The project does not use Laravel, CodeIgniter, React, Vue, Vite, Composer, or an npm build system.

## 3. Folder Structure

```text
bali-project/
|-- index.php                         # Homepage
|-- destination.php                   # Destination listing
|-- detail.php                        # Destination detail + reviews
|-- about.php                         # About page
|-- contact.php                       # Contact page
|-- visa.php                          # Visa information
|-- transport.php                     # Flight search form
|-- tiket.php                         # Ticket/booking menu
|-- tiket.bus.php                     # Bus ticket form
|-- booking.hotel.php                 # Hotel booking form
|-- sewa.mobil.php                    # Car rental form
|-- hasil.bus.php                     # Bus search results
|-- hasil.hotel.php                   # Hotel search results
|-- hasil.pesawat.php                 # Flight search results
|-- hasil.mobil.php                   # Car rental search results
|-- login.php                         # Login
|-- register.php                      # Register
|-- logout.php                        # Logout
|-- connection.php                    # Legacy-compatible DB adapter
|-- .htaccess                         # Apache protection, headers, caching
|-- admin/                            # Admin dashboard and CRUD
|-- assets/js/app.js                  # Global JavaScript
|-- booking/                          # Internal booking flow
|-- config/database.php               # DB config from environment variables
|-- config.example.php                # Example config without secrets
|-- database/                         # Manual SQL migrations
|-- docs/                             # Guides, images, and reports
|-- images/                           # Original image assets
|-- images/optimized/                 # Optimized WebP assets
|-- images/uploads/                   # Admin-uploaded images
|-- includes/                         # Helpers: DB, escaping, auth, CSRF
|-- partials/                         # Shared head, navbar, footer
|-- scripts/                          # CLI tools (backup, preview renderer)
|-- storage/private/                  # Protected SQL dump and archive
|-- styles/                           # CSS tokens, base, components, pages
```

**Notes:**

- The application root is `C:\laragon\www\bali-project`.
- `storage/`, `database/`, `config/`, `includes/`, and `partials/` are blocked from direct access by `.htaccess`.
- `storage/private/database/bali.sql` is required for local setup but must never be published as a public route in production.

## 4. Local Setup

### Prerequisites

- PHP 8.x.
- PHP `mysqli` extension.
- MySQL or MariaDB.
- Apache (Laragon/XAMPP) or the PHP built-in server.
- A modern browser.

No `composer install` or `npm install` is required.

### Laragon / XAMPP

1. Start Apache and MySQL/MariaDB.
2. Place the project under the document root:

```text
C:\laragon\www\bali-project
```

3. Create an empty database named `bali`.
4. Import:

```text
storage/private/database/bali.sql
```

5. Open:

```text
http://localhost/bali-project/
```

### PHP Built-in Server

```sh
php -S 127.0.0.1:8088 -t .
```

Open:

```text
http://127.0.0.1:8088/index.php
```

Note: the PHP built-in server does not read `.htaccess`. Use Apache to test the `.htaccess` protection rules.

## 5. Database

Canonical local dump:

```text
storage/private/database/bali.sql
```

Default database name: `bali`.

| Table | Purpose |
|---|---|
| `destination` | Tourism destination master data |
| `detail` | Destination detail content |
| `detail_image` | Additional destination images |
| `destinations` | City/destination options for hotel and car flows |
| `from_city` / `to_city` | Origin and destination city options |
| `hotel` | Hotel master data |
| `bookings_hotel` | Hotel availability/link data |
| `car` | Car rental master data |
| `bookings_mobil` | Car rental availability/link data |
| `pesawat` | Airline master data |
| `bookings_pesawat` | Flight availability/link data |
| `buses` | Bus operator data |
| `routes_bus` | Bus route availability/link data |

Optional migrations in `database/` are additive — they create tables if they do not exist and never delete existing data.

**Safe database rules:**

- Review SQL files before running them.
- Back up the database before applying manual SQL.
- Never run SQL against production without explicit approval.
- Never publish SQL dumps as public routes.

## 6. Configuration

Connection flow:

```text
connection.php
-> includes/database.php
-> config/database.php
-> environment variables or local fallback
```

Supported environment variables:

```text
BALI_DB_HOST
BALI_DB_USER
BALI_DB_PASSWORD
BALI_DB_NAME
```

For local development, fallback values remain compatible with Laragon/XAMPP. For production, use hosting environment variables — never commit real credentials. A secret-free example is available at `config.example.php`.

## 7. Routes & Pages

### Public Pages

| URL | Purpose |
|---|---|
| `/index.php` | Homepage |
| `/destination.php` | Destination listing |
| `/detail.php?id=1` | Destination detail by `id` |
| `/about.php` | About page |
| `/contact.php` | Contact page |
| `/visa.php` | Visa information |
| `/transport.php` | Flight search form |
| `/tiket.php` | Ticket and booking menu |
| `/tiket.bus.php` | Bus ticket form |
| `/booking.hotel.php` | Hotel booking form |
| `/sewa.mobil.php` | Car rental form |
| `/hasil.pesawat.php` | Flight results |
| `/hasil.bus.php` | Bus results |
| `/hasil.hotel.php` | Hotel results |
| `/hasil.mobil.php` | Car rental results |

### Authentication

| URL | Purpose |
|---|---|
| `/register.php` | Register user |
| `/login.php` | Login user |
| `/profile.php` | Profile settings and password update |
| `/logout.php` | Logout user |

### Internal Booking

| URL | Purpose |
|---|---|
| `/booking/index.php` | Internal booking form |
| `/booking/store.php` | Booking POST handler |
| `/booking/confirmation.php?id=...&token=...` | Booking confirmation |
| `/booking/invoice.php?id=...&token=...` | HTML invoice |
| `/booking/history.php` | Invoice history for signed-in users |

### Admin

| URL | Purpose |
|---|---|
| `/admin/index.php` | Admin dashboard |
| `/admin/destinations/index.php` | Destination management |
| `/admin/destinations/create.php` | Create destination |
| `/admin/destinations/edit.php?id=1` | Edit destination |
| `/admin/destinations/delete.php?id=1` | Disable/soft-delete destination |
| `/admin/users/index.php` | User management |
| `/admin/bookings/index.php` | Booking management |
| `/admin/invoices/index.php` | Invoice management |

Admin access requires a signed-in user with the `admin` role.

### Intentionally Blocked Routes

| Path | Reason |
|---|---|
| `/hasil.transport.php` | Legacy raw-SQL file, not a public page |
| `/bali.sql` | Legacy root dump; must return 403/404 |
| `/storage/` | Private dump, archive, and quarantine |
| `/_archive/` | Legacy archive; must return 403/404 |
| `/database/` | Manual SQL files |
| `/config/` | Database config |
| `/includes/` | Internal helpers |
| `/partials/` | Internal partials |
| `*.md` | Documentation is not a public route |

These blocks apply on Apache when `.htaccess` is active.

## 8. Architecture Overview

The architecture is a native PHP monolith:

```text
Browser
-> PHP file acts as the route
-> includes shared head/navbar/footer partials
-> includes connection.php when DB access is needed
-> mysqli query / prepared statement
-> renders HTML directly
```

**Established patterns:**

- Shared layout: `partials/head.php`, `partials/navbar.php`, `partials/footer.php`.
- Escaping/validation helpers: `includes/helpers.php`.
- Auth/session/CSRF helpers: `includes/auth.php`.
- DB helpers: `includes/database.php`.
- DB config: `config/database.php`.
- CSS tokens and components: `styles/_tokens.css`, `_base.css`, `_components.css`, `_animations.css`, `_theme-sync.css`.

## 9. Security Notes

**In place:**

- `detail.php` validates `id` and uses prepared statements.
- Result pages (`hasil.*.php`) validate GET input and use prepared statements.
- Auth uses `password_hash()` / `password_verify()`.
- Session ID is regenerated after login.
- Auth, review, booking, and admin forms use CSRF tokens.
- Admin image uploads validate extension, MIME, and size.
- `.htaccess` blocks sensitive files and internal folders.
- Security headers are set in `.htaccess` and `partials/head.php`.

**Rules to maintain:**

- Never show real credentials in docs or code.
- Never publish `storage/private/database/bali.sql` as a public production route.
- Never render raw database errors to end users.
- Never run manual SQL without a backup.
- If hosting ignores `.htaccess`, move sensitive files outside the public root.

## 10. Assets & Performance

| Path | Purpose |
|---|---|
| `images/` | Original/fallback assets |
| `images/optimized/` | Optimized WebP assets |
| `styles/` | CSS |
| `assets/js/app.js` | Global JavaScript |

**Notes:**

- Many pages already serve optimized WebP where available.
- Original large images are kept as source/fallback.
- `.htaccess` adds cache headers for images, CSS, JS, and fonts.
- Do not remove originals until an asset pipeline decision is made.

## 11. Verification Commands

Syntax checks:

```sh
php -l index.php
php -l destination.php
php -l detail.php
php -l hasil.bus.php
php -l hasil.hotel.php
php -l hasil.pesawat.php
php -l hasil.mobil.php
php -l login.php
php -l register.php
php -l logout.php
php -l admin/index.php
php -l booking/index.php
php -l booking/store.php
```

Local smoke tests (Apache):

```text
http://localhost/bali-project/index.php
http://localhost/bali-project/destination.php
http://localhost/bali-project/detail.php?id=1
http://localhost/bali-project/tiket.php
http://localhost/bali-project/transport.php
```

File-leak tests (Apache):

```text
http://localhost/bali-project/bali.sql
http://localhost/bali-project/storage/
http://localhost/bali-project/storage/private/database/bali.sql
http://localhost/bali-project/_archive/
http://localhost/bali-project/database/
http://localhost/bali-project/config/database.php
http://localhost/bali-project/includes/helpers.php
http://localhost/bali-project/partials/head.php
```

All sensitive URLs above should return `403 Forbidden`.

## 12. Troubleshooting

| Symptom | Likely Cause | Fix |
|---|---|---|
| Blank page | `display_errors` off or a PHP fatal | Run `php -l file.php`; check Apache/PHP logs |
| "Database not available" | MySQL stopped or config mismatch | Start MySQL; verify database `bali` and env vars |
| "Table ... doesn't exist" | Dump not imported or migration missing | Import `storage/private/database/bali.sql`; apply the required manual SQL |
| Login/register table error | Auth tables/columns missing | Review and run `database/2026_06_09_create_auth_tables.sql` |
| Admin denied | User role is not admin | Promote the user using the reviewed SQL template |
| Reviews inactive | `reviews` table missing | Review and run `database/2026_06_09_create_reviews_table.sql` |
| Booking save fails | Booking tables missing | Review and run `database/2026_06_09_create_internal_booking_tables.sql` |
| Image upload fails | Folder not writable or invalid file | Check `images/uploads/destinations/`, format, MIME, size |
| `.htaccess` triggers 500 | Unsupported Apache directive | Rename `.htaccess` temporarily; re-enable rules one by one |
| SQL file is browser-accessible | Not Apache, or `.htaccess` inactive | Move SQL outside the public root; configure the server |
| CSS/JS/images missing | Nested URL paths differ | Check base URL and folder location under the document root |

## 13. New Developer Checklist

- [ ] Clone/copy the project into the document root.
- [ ] Confirm the actual application root.
- [ ] Run PHP 8.x with `mysqli`.
- [ ] Start Apache and MySQL/MariaDB.
- [ ] Create database `bali`.
- [ ] Import `storage/private/database/bali.sql`.
- [ ] Open `index.php`.
- [ ] Test `destination.php` and `detail.php?id=1`.
- [ ] For auth, run the manual auth SQL.
- [ ] For admin, promote a user to admin.
- [ ] For internal booking, run the booking SQL.
- [ ] For reviews, run the reviews SQL.
- [ ] Run `php -l` on every file you edit.
- [ ] Never commit production credentials.

## 14. Roadmap

**Phase 1 — Stabilization**

- Consolidate all DB access through helpers.
- Remove remaining `@` suppressions on queries.
- Build a reusable error page.
- Ensure every POST route uses CSRF.

**Phase 2 — Backend Structure**

- Extract per-feature query helpers/services.
- Standardize page include patterns.
- Document table and field naming.

**Phase 3 — UI/UX**

- Finish migrating legacy CSS to design tokens.
- Reduce inline CSS.
- Verify responsive layouts across all pages.

**Phase 4 — Admin & Data**

- Complete destination CRUD.
- Add CRUD for hotels, transport, buses, and cars.
- Add contact message and booking dashboards.

**Phase 5 — Production Readiness**

- Keep SQL dumps and archives out of the production public root.
- Add final sitemap/robots after the domain is known.
- Add a backup strategy.
- Re-run a security audit before publishing.

## 15. Document Index

- `README.md` — project summary and quick start.
- `DEPLOYMENT.md` — production deployment checklist.
- `docs/ADMIN_OPERATIONS.md` — admin operations guide.
- `docs/reports/` — audit, review, and cleanup reports.
- `database/README.md` — database dump notes.
- `PRODUCT.md` — product and design direction.

## 16. Assumptions

- Local database name is `bali`.
- Application root is `C:\laragon\www\bali-project`.
- Production runs Apache or shared hosting with `.htaccess` support.
- Manual SQL in `database/` is applied intentionally, not automatically.
- Production credentials are never available to, or stored in, the repository.
