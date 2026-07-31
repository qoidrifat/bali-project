# Contributing to Bali Project

Thanks for taking the time to contribute! Bali Project is a small, framework-free PHP application, and every improvement helps — whether it is a bug fix, a documentation tweak, or a new feature.

Please read and follow the [Code of Conduct](CODE_OF_CONDUCT.md).

## Table of Contents

- [Getting Started](#getting-started)
- [How to Contribute](#how-to-contribute)
- [Coding Conventions](#coding-conventions)
- [Commit Messages](#commit-messages)
- [Pull Request Process](#pull-request-process)
- [Security](#security)

## Getting Started

1. Fork the repository.
2. Clone your fork:
   ```sh
   git clone https://github.com/qoidrifat/bali-project.git
   cd bali-project
   ```
3. Set up a local environment (PHP 8.x + MySQL + Apache or `php -S`). See [README.md](README.md#getting-started) and the [developer guide](docs/PROJECT_GUIDE.md).
4. Create a feature branch:
   ```sh
   git checkout -b feat/your-feature
   ```

## How to Contribute

### Reporting bugs

Open an issue using the [bug report template](.github/ISSUE_TEMPLATE/bug_report.yml). Include:

- PHP version, web server, and operating system.
- Steps to reproduce.
- Expected vs. actual behavior.
- Any relevant error messages (sanitized — no credentials).

### Suggesting features

Open an issue using the [feature request template](.github/ISSUE_TEMPLATE/feature_request.yml). Describe the problem you are solving and the proposed behavior.

### Improving code

- Keep changes **minimal** and **backward compatible**. Preserve existing URLs unless there is a migration plan.
- Do not introduce frameworks or build tools (no Composer, no npm, no Laravel).
- Every changed PHP file must pass `php -l`.
- Add or update documentation when behavior changes.

### Improving documentation

- Keep documentation in **English**, concise, and consistent with existing docs.
- Update `README.md` tables and `docs/PROJECT_GUIDE.md` when routes or structure change.

## Coding Conventions

- **PHP 8.x** with `mysqli`. No dependencies.
- Escape all output with `e()` (`includes/helpers.php`).
- Use prepared statements for any query that includes user input.
- Protect state-changing forms with `csrf_field()` / `verify_csrf_token()`.
- Reuse shared partials (`partials/`) and helpers (`includes/`) instead of duplicating markup.
- Prefer design-token CSS (`styles/_tokens.css`) over inline styles.
- Do not suppress errors with `@`; log them with `error_log()` instead.
- Never commit credentials, `.env` files, or SQL dumps.

## Commit Messages

Write clear, imperative commit messages. Keep the first line under 72 characters.

```text
fix: validate id before querying destination detail
feat: add pagination to admin bookings list
docs: clarify database setup steps in README
chore: remove unused login-admin.txt from root
```

## Pull Request Process

1. Push your branch and open a pull request against `main`.
2. Fill out the [pull request template](.github/PULL_REQUEST_TEMPLATE.md).
3. Ensure your branch is up to date with `main`.
4. Reference any related issue (e.g., `Closes #12`).
5. A maintainer will review, request changes if needed, and merge once the checks pass.

## Security

If you find a security vulnerability, do **not** open a public issue. Report it privately by following [SECURITY.md](SECURITY.md).

---

Thank you for contributing to Bali Project. 🌴
