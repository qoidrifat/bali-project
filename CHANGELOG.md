# Changelog

All notable changes to **Bali Project** are documented here. This project follows [Keep a Changelog](https://keepachangelog.com/) and uses [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Repository & documentation
- Redesigned `README.md` with a premium, professional presentation.
- Moved developer reports to `docs/reports/`; added `docs/images/` for documentation screenshots.
- Renamed `tools/` to `scripts/` for CLI utilities.
- Added `CONTRIBUTING.md`, `SECURITY.md`, `CODE_OF_CONDUCT.md`, `LICENSE`, and `CHANGELOG.md`.
- Added GitHub issue templates, a pull request template, and a PHP lint CI workflow.
- Translated developer documentation (`docs/PROJECT_GUIDE.md`, `docs/ADMIN_OPERATIONS.md`) to English.
- Removed development clutter (browser profile caches, leftover login text file, empty `package-lock.json`).

## [0.1.0] - 2026-06-09 (initial development snapshot)

### Added
- Native PHP tourism portal: homepage, destinations, detail pages, visa, transport, and tickets.
- Flight, bus, hotel, and car rental search flows with prepared-statement results.
- User authentication (register, login, logout) with password hashing and CSRF protection.
- Role-based admin dashboard with destination CRUD and image uploads.
- Internal booking flow with confirmation pages and HTML invoices.
- Shared partials (head, navbar, footer) and a design-token CSS system with light/dark theme.
- Apache `.htaccess` protection for sensitive files and static asset caching.
- Optimized WebP image variants.
