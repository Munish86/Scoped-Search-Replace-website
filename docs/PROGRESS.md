# Scoped Search & Replace website: progress and handoff

Read this first when starting a new session. Last updated: 2026-10-15 (built locally).
Technical overview: `../README.md`. Owner's guide (Hinglish): `OWNER-GUIDE.md`.

## Goal

A clean, professional website for the owner's free WordPress plugin Scoped Search & Replace
(`C:\Users\hp\Downloads\wordpress-search-replace-tool`, v1.1.0, GitHub public), in WordPress so the owner can edit pages
and write posts. Local first; real hosting once a domain is chosen.

## Decisions

| Topic | Decision |
|---|---|
| Base | Copy of `../bulk-uploader-website/` (theme, site plugin, tools); prefixes `bu-`/`bus_` -> `sr-`/`srs_`, brand teal `#0F766E`, new logo (scope corners + amber magnifier) |
| Local URL | http://localhost:8091 (8090 was already used by another session's "portfolio" site) |
| Database | `searchreplace_wp`, XAMPP `root`; admin user `sradmin` |
| CTA | Plugin is free: "Download free" -> /download/ (class `sr-cta`); switches to WordPress.org once its URL is set. ZIP bundled in the site plugin, button label shows the version |
| Forms | Contact only (no waitlist), table `{prefix}sr_messages`, admin page Messages |
| Screenshots | Real plugin on a separate demo WordPress (http://localhost/srdemo), driven by `tools/plugin-shots.mjs` |
| Pages | home, features, how-it-works, download, docs, changelog, contact, privacy-policy, blog. No pricing, no terms (GPL plugin) |
| Copy | English; every claim checked against the plugin code |

## State on 2026-10-15

- All pages + 4 posts in place. `tools/shots.mjs`: every page 200 at 1440px and 390px, no console errors, no
  horizontal overflow. `tools/check-editor.mjs`: 28 pages/templates/parts/patterns, all blocks valid.
- Download link serves the ZIP (200, application/zip, 7 files). Contact form submitted once: row saved, redirect ok,
  test row deleted. Yoast titles/descriptions/share image checked. Schema on home page.

## Open points for the owner

1. WordPress.org: the slug `scoped-search-replace` is free (API 404 on 2026-10-15). Has the plugin been submitted?
   The site only says it is "prepared for the directory".
2. Promises to confirm: reply within one working day, contact messages deleted within 12 months.
3. Plugin `readme.txt` FAQ says menus are not searched; with "All content types" the code includes nav menu items
   and revisions (no post_type filter). The site follows the code. Owner may want the readme corrected.
4. Domain + hosting, then All-in-One WP Migration (OWNER-GUIDE.md section 7).

## Gotchas

- Same as `../bulk-uploader-website/docs/PROGRESS.md`.
- Junctions made with `cmd mklink /J` from Git Bash got a wrong target (`\C:\...`); use PowerShell
  `New-Item -ItemType Junction`.
- WordPress moves admin notices to the top of the page; `plugin-shots.mjs` moves the result notice into the results
  card (after load) before the screenshot, and hides the admin bar.
- The replace step in `plugin-shots.mjs` changes the demo content; run `demo-content.php` again before the next run.
