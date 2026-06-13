# Kanjava Platform

A reusable WordPress foundation: the **Kanjava Base** theme (Bulma 1.0 + SCSS + Font Awesome, built with Vite)
and the **Kanjava Core** plugin (custom post types, taxonomies, shortcodes, settings, REST API).
Designed to spin up churches, nonprofits, businesses, and music/artist sites from a shared base
plus per-vertical starter packs.

## Repository layout

```
kanjava-platform/                 (this repo)
├── wordpress/
│   ├── themes/kanjava-base/      Bulma 1.0 theme, Vite build
│   └── plugins/kanjava-core/     Content engine: CPTs, taxonomies, shortcodes, REST
├── docs/                         Install / developer / client guides
├── AGENTS.md                     Conventions for AI agents and contributors
├── README.md
└── docker-compose.yml            Local LAMP stack (WordPress + MariaDB + phpMyAdmin + MailHog)
```

The theme and plugin are bind-mounted into a WordPress container; WordPress core itself lives in
a Docker volume and is **not** committed.

## Quick start

```bash
docker compose up -d
```

| Service     | URL                     | Notes                                 |
|-------------|-------------------------|---------------------------------------|
| WordPress   | http://localhost:8080   | Complete the install wizard once       |
| phpMyAdmin  | http://localhost:8081   | Server `db`, user `kanjava` / `kanjava`|
| MailHog     | http://localhost:8025   | Catches all outbound mail              |

Then activate the **Kanjava Base** theme and **Kanjava Core** plugin in `wp-admin`.

### Front-end build (theme)

```bash
cd wordpress/themes/kanjava-base
npm install
npm run dev      # Vite dev server with HMR (http://localhost:5173)
npm run build    # production bundle -> dist/
```

When the Vite dev server is running the theme auto-loads assets from it (see `inc/enqueue-assets.php`);
otherwise it serves the hashed files from `dist/` via the build manifest.

## Status

Base theme + core plugin are scaffolded (Epics 1–2, Issues 1–7). AI features, WooCommerce, and
starter packs are tracked in the roadmap and not yet implemented.
