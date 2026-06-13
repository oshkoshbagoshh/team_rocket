# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Current state

This repo is **early-stage scaffolding**. The directory skeleton under `wordpress/` exists but
contains no code yet, and there are no git commits on `main`. The README documents the *intended*
architecture (the Kanjava Platform); treat it as the design spec to build toward, not a description
of existing code. When something the README references doesn't exist yet (e.g. `docker-compose.yml`,
`AGENTS.md`, build configs), it has not been created — verify before assuming.

## What this is

The **Kanjava Platform**: a reusable WordPress foundation for spinning up churches, nonprofits,
businesses, and music/artist sites from a shared base plus per-vertical starter packs. Two pieces:

- **Kanjava Base** theme (`wordpress/themes/kanjava-base/`) — Bootstrap 5 + SCSS, built with Vite.
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
- **Asset loading is dual-mode** (`inc/enqueue-assets.php`): when the Vite dev server is running the
  theme loads assets from it with HMR; otherwise it serves hashed files from `dist/` via the Vite
  build manifest. Keep this conditional behavior intact when touching asset enqueuing.

## Commands

Per the README (these depend on files not yet committed — confirm they exist first):

```bash
docker compose up -d                              # local LAMP stack
```

| Service    | URL                   | Notes                                   |
|------------|-----------------------|-----------------------------------------|
| WordPress  | http://localhost:8080 | Complete install wizard once            |
| phpMyAdmin | http://localhost:8081 | Server `db`, user `kanjava` / `kanjava` |
| MailHog    | http://localhost:8025 | Catches all outbound mail               |

Theme front-end build:

```bash
cd wordpress/themes/kanjava-base
npm install
npm run dev      # Vite dev server + HMR (http://localhost:5173)
npm run build    # production bundle -> dist/
```

After `docker compose up`, activate the Kanjava Base theme and Kanjava Core plugin in `wp-admin`.

## Conventions

- `.gitignore` enforces what's tracked: no `node_modules/`, `vendor/`, theme `dist/`, WordPress core,
  uploads, or `.env`. PHP tooling (`.phpcs-cache`, `.phpunit.result.cache`) implies PHPCS and PHPUnit
  are the expected lint/test tools once configured.
