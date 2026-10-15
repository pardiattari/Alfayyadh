# Repository Guidelines

## Project Overview

Alfayyadh.ID is a PHP affiliate marketplace aggregator for Shopee, Tokopedia, and TikTok Shop. It uses MySQL for products, categories, marketplaces, banners, articles, and settings. There is no checkout flow; marketplace actions go through `redirect.php`.

## Local Development

- Run the project with XAMPP Apache and MySQL.
- The expected document root is `C:\xampp\htdocs\alfayyadh-id`.
- Import `database/alfayyadh_db.sql` into MySQL before using the site.
- Database credentials and `BASE_URL` are configured in `includes/config.php`.
- Public site: `http://localhost/alfayyadh-id/`.
- Admin site: `http://localhost/alfayyadh-id/admin/login.php`.

## Repository Layout

- Root PHP files: public pages and affiliate redirect handling.
- `includes/`: shared configuration, database access, helpers, header, and footer.
- `admin/`: authenticated administration pages and shared admin includes.
- `assets/css/style.css`: site and admin styling.
- `assets/js/main.js`: shared browser behavior.
- `assets/uploads/products/`: uploaded product images.
- `database/alfayyadh_db.sql`: schema and seed data.

## Coding Conventions

- Preserve the existing procedural PHP style and shared helper usage.
- Load shared code with `require_once` and use `__DIR__` for file-relative paths.
- Escape HTML output with the existing `e()` helper.
- Use prepared statements through the existing database helpers for user-controlled values.
- Keep Indonesian user-facing copy consistent with the existing site.
- Preserve the configured Asia/Jakarta timezone and current `BASE_URL` behavior.
- Do not commit credentials, uploaded secrets, or generated files.

## Validation

- Run `php -l path/to/changed-file.php` for each changed PHP file.
- For database or behavior changes, test the affected page through the local XAMPP URL and inspect both authenticated and unauthenticated paths as applicable.
- Keep schema changes synchronized with `database/alfayyadh_db.sql`.

## Change Scope

- Make focused edits and preserve unrelated user changes.
- Do not add dependencies or a build system unless the task requires them.
- Do not commit changes unless explicitly requested.
