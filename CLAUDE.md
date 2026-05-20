# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**ChronoFusion Kart** — a karting reservation web application built with PHP (no framework), MySQL, and Tailwind CSS v4. The project runs on WampServer (Apache + MySQL) at `http://localhost/projetFin/`.

## Development Commands

### Tailwind CSS (watch mode during development)
```bash npx @tailwindcss/cli -i styles/input.css -o styles/output.css --watch

ADMINadmin1234*
```

### One-time CSS build
```bash
npx @tailwindcss/cli -i styles/input.css -o styles/output.css
```

### Database setup
Import `karting_resa.sql` into MySQL via phpMyAdmin or:
```bash
mysql -u root < karting_resa.sql
```

## Architecture

### Routing
All requests go through `index.php`, which:
1. Sanitizes `$_GET['page']` (whitelist + regex stripping)
2. Splits the URL into segments
3. Dispatches to `MainController` methods (`showHome()`, `showSession()`, etc.)

The allowed pages whitelist is defined in `index.php` — add new routes there.

### MVC Structure
- **`controllers/`** — `AbstractController` (base with `render()` + `redirectToRoute()`), `MainController`, `HomeController`
- **`models/`** — `Database.php` (returns a `$pdo` instance via `return $pdo`; included with `require_once`), `LoginModel.php`, `RegisterModel.php`, `UserModel.php`
- **`entities/`** — Plain PHP classes with getters/setters. `User` and `Session` use `namespace Entity;`, `Booking` does not — keep this consistent when adding entities.
- **`views/`**
  - `pages/` — Full page files (include header/footer themselves)
  - `components/` — `_header.php` (loads Tailwind output.css, Flatpickr CDN, Google Fonts), `_menu.php`, `_footer.php`
  - `commons/` — Shared template layout (currently empty)
- **`helpers/`** — `validator.php` (pure functions: `isEmailValid`, `isPasswordStrong`, `isNameValid`, `isPhoneValid`)

### Database Connection Pattern
`models/Database.php` creates and **returns** a PDO instance. Models include it as:
```php
$pdo = require_once "Database.php";
```
Database: `karting` on `127.0.0.1:3306`, user `root`, no password (WampServer default).

### Database Schema (key tables)
- `role` → `user` (role_id FK)
- `track`, `vehicle` → `session` (track_id FK) → `session_vehicle` (junction) → `booking` (session_id, user_id FKs)
- `session.session_status`: ENUM `'scheduled' | 'ongoing' | 'completed' | 'cancelled'`

### CSS / Theming
Tailwind v4 with custom theme tokens defined in `input.css` under `@theme`:
- `--color-midnight-blue` — primary dark background
- `--color-fusion-orange` — CTA/accent color
- `--color-grey-blue-primary/secondary`, `--color-dark-blue-steel`
- Default font: `Racing Sans One` (Google Fonts)

Use these tokens as Tailwind classes (e.g., `bg-midnight-blue`, `text-fusion-orange`).

### Flatpickr (date/time picker)
Loaded via CDN in `_header.php`. Used in `views/pages/calendar.php` for session booking date selection. Configuration: French locale, time range 10:00–22:00, 20-minute increments.

### View Include Paths
Page files in `views/pages/` include components with relative paths:
```php
include '../components/_header.php';   // from views/pages/
include_once '../../models/SomeModel.php';  // back to project root
```
Note: some files inconsistently reference `footer.php` vs `_footer.php` — the correct filename is `_footer.php`.

### Sessions & Auth
Authentication uses PHP sessions. On login: `session_regenerate_id(true)`, then stores `$_SESSION['user_id']`, `$_SESSION['role_id']`, `$_SESSION['firstname']`. Passwords hashed with `PASSWORD_BCRYPT` (cost 12).
