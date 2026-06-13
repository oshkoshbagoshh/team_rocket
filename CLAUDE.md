# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Current state

Early-stage. The **Kanjava Base** theme (`wordpress/themes/kanjava-base/`) is wired up with its
front-end build (Bulma + SCSS + Font Awesome via Vite) and a working theme skeleton. The
**Kanjava Core** plugin and the Docker stack are still empty scaffolding — when the README
references something that isn't on disk yet (`docker-compose.yml`, `AGENTS.md`, the plugin's PHP),
it hasn't been created. Verify before assuming.

## What this is

The **Kanjava Platform**: a reusable WordPress foundation for spinning up churches, nonprofits,
businesses, and music/artist sites from a shared base plus per-vertical starter packs. Two pieces:

- **Kanjava Base** theme (`wordpress/themes/kanjava-base/`) — Bulma 1.0 + SCSS + Font Awesome 6, built with Vite.
- **Kanjava Core** plugin (`wordpress/plugins/kanjava-core/`) — the content engine: custom post
  types, taxonomies, shortcodes, settings, and REST API.

Stylistic/UX inspiration (per the brainstorm): Bulma CSS, Ionicon icons, Pokémon, Windows 98 CSS,
Free Code Camp. There's a planned **Education Module** themed as "Pokémon Trainer Sandbox
Challenges" with badge-based progression.

## Architecture (intended)

- Theme and plugin are the **only tracked WordPress code**. WordPress core lives in a Docker volume
  and is intentionally **not committed** (see `.gitignore`: `wordpress/core/`, `wp-content/uploads/`).
- The theme and plugin are bind-mounted into a WordPress container running via Docker Compose
  (WordPress + MariaDB + phpMyAdmin + MailHog).
- **Asset loading is dual-mode** (`inc/enqueue-assets.php`): the loader checks for `dist/hot` (written
  by a custom Vite plugin in `vite.config.js` while `npm run dev` runs). If present, it loads the
  `@vite/client` + entry module from the dev server (HMR); otherwise it reads `dist/.vite/manifest.json`
  and enqueues the hashed JS + CSS. Both the Vite client/entry and the prod entry are emitted as
  `type="module"` via a `script_loader_tag` filter. Keep this conditional behavior intact.

### Theme front-end (`wordpress/themes/kanjava-base/`)

- **Single JS entry** `src/js/main.js` drives the whole bundle: it imports Font Awesome's CSS, then
  `src/scss/main.scss`, then runs the Bulma navbar burger toggle (Bulma's mobile menu needs JS).
- **Palette lives in one place:** `src/scss/_variables.scss` (the five brand colors + `$brand-gradient`).
  `main.scss` passes them into Bulma via `@use 'bulma/sass' with (...)` (so `$primary` → midnight-violet,
  `$link` → vintage-grape, `$info` → frosted-blue, flowing into Bulma's `--bulma-*` vars), and
  `_custom.scss` re-exports all five as `:root` CSS custom properties (`--midnight-violet`, etc.) plus a
  `--brand-gradient`. Change brand colors there, not inline.
- **Menus** render through `Kanjava_Navbar_Walker` (`inc/class-navbar-walker.php`) so wp-admin menus
  output as Bulma `.navbar-item`s (with hoverable dropdowns). Responsiveness is Bulma's mobile-first
  grid + the burger; `_custom.scss` only adds brand pieces (gradient hero, sticky footer, hover lift).
- PHP is organized as `functions.php` (bootstrap/requires) → `inc/theme-setup.php` (supports, menus) +
  `inc/enqueue-assets.php` + the walker. Templates: `header.php`/`footer.php`, `index.php` (card grid),
  `front-page.php` (gradient hero).

## Commands

Theme front-end build (works now):

```bash
cd wordpress/themes/kanjava-base
npm install
npm run dev      # Vite dev server + HMR on :5173 (writes dist/hot)
npm run build    # production bundle + manifest -> dist/
```

Theme quality gate — PHP (works now, no Docker/DB needed):

```bash
cd wordpress/themes/kanjava-base
composer install
composer lint       # PHPCS, WordPress standard (phpcbf via `composer lint:fix`)
composer test       # PHPUnit unit tests
```

Tests are **pure unit tests** via Brain Monkey (`tests/bootstrap.php` mocks WP functions and stubs
`Walker_Nav_Menu` — no WordPress, no database). They cover the only two files with real logic:
`inc/enqueue-assets.php` (dev/prod/missing-manifest branches, the `type="module"` filter) and the
navbar walker. Templates and the burger JS are presentational — covered by visual QA later, not unit
tested. "Passable" = `composer lint` reports 0 errors and `composer test` is fully green. Integration
tests (`WP_UnitTestCase`), the `theme-unit-test-data.xml` import, and Theme Check are **deferred**
until the Docker stack exists (they need a running WordPress).

Local WordPress stack (depends on `docker-compose.yml`, not yet committed — confirm first):

```bash
docker compose up -d                              # local LAMP stack
```

| Service    | URL                   | Notes                                   |
|------------|-----------------------|-----------------------------------------|
| WordPress  | http://localhost:8080 | Complete install wizard once            |
| phpMyAdmin | http://localhost:8081 | Server `db`, user `kanjava` / `kanjava` |
| MailHog    | http://localhost:8025 | Catches all outbound mail               |

After `docker compose up`, activate the Kanjava Base theme and Kanjava Core plugin in `wp-admin`.

## Conventions

- `.gitignore` enforces what's tracked: no `node_modules/`, `vendor/`, theme `dist/`, WordPress core,
  uploads, `.env`, or test/lint caches (`.phpcs-cache`, `.phpunit.result.cache`, `.phpunit.cache/`,
  the theme's `tests/tmp/` fixtures).
- **PHP file naming follows WPCS**: class files are `class-{slugified-classname}.php` (e.g. class
  `Kanjava_Navbar_Walker` → `inc/class-kanjava-navbar-walker.php`). `composer lint` enforces this.
- Intentional WPCS deviations are marked with `// phpcs:ignore <sniff> -- <reason>` (see the local
  file reads and dev-mode `null` script versions in `inc/enqueue-assets.php`) — keep the reason.
