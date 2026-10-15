# Scoped Search & Replace website (WordPress)

Website for the free WordPress plugin Scoped Search & Replace (code: `C:\Users\hp\Downloads\wordpress-search-replace-tool`,
GitHub `Munish86/WordPress-Search-Replace-Tool`): home, features, how it works, download, documentation, changelog,
contact, privacy and a blog with four guides. Built from `../bulk-uploader-website/` with the same structure.
Owner's guide (Hinglish): `docs/OWNER-GUIDE.md`. State and decisions: `docs/PROGRESS.md`.

## Local site

| | |
|---|---|
| Site | http://localhost:8091 (Apache vhost in `C:\xampp\apache\conf\extra\httpd-vhosts.conf`; 8090 is another session's portfolio site) |
| Admin | http://localhost:8091/wp-admin/ (login in `local-login.txt`, not for sharing) |
| WordPress | 7.1.3 in `C:\xampp\htdocs\searchreplace` (core copied from the Bulk Uploader install) |
| Database | `searchreplace_wp` on XAMPP MariaDB, user `root` (local only) |
| Demo site for screenshots | http://localhost/srdemo, `C:\xampp\htdocs\srdemo`, database `srdemo_wp`, login demo / demo-local-only |

Junctions (editing files here changes the site at once):

- `C:\xampp\htdocs\searchreplace\wp-content\themes\scoped-sr` -> `theme/scoped-sr`
- `C:\xampp\htdocs\searchreplace\wp-content\plugins\scoped-sr-site` -> `plugin/scoped-sr-site`

The real plugin (`scoped-search-replace`, from the release ZIP) is installed and active on both sites.

## What is here

```
theme/scoped-sr/          Block theme. Tokens in theme.json (teal brand #0F766E), components (sr-*) in assets/css/site.css.
  templates/, parts/      as in the Bulk Uploader theme; header nav: Features, How it works, Docs, Changelog, Blog
  patterns/               cta-band, feature-cards, faq, screenshot-split (Editor > Patterns > Scoped Search & Replace)
  assets/images/          plugin-*.webp (real plugin screenshots), logo-mark.svg, favicon.svg, og-image.png
plugin/scoped-sr-site/
  download/               scoped-search-replace.zip: the plugin release offered on /download/
  includes/settings.php   Settings > Scoped Search & Replace site (WordPress.org URL, version, ZIP link, GitHub, emails)
  includes/cta.php        Button classes: sr-cta (-> WordPress.org once set), sr-download (ZIP + version label), sr-github
  includes/leads.php      [sr_contact], [sr_support_email]; table {prefix}sr_messages
  includes/admin.php      Messages page (list, CSV, delete)
  includes/meta.php       SoftwareApplication schema on the home page; meta/OG fallback when no SEO plugin
content/                  Page and post bodies (block markup), site.php (titles, slugs, templates, meta), covers/
tools/
  wp.sh / wp-demo.sh      WP-CLI for the site / the demo site
  setup-content.php       content/ -> WordPress (only missing pages unless "force"), Yoast share image and separator
  demo-content.php        resets the demo site's sample shop content (old http:// domain in pages and post meta)
  plugin-shots.mjs        drives the real plugin on the demo site, saves 2x PNGs in tools/.shots/plugin/
  make-shots.php          those PNGs -> theme assets/images/plugin-*.webp
  make-images.php         blog covers and og-image.png (PHP GD + Windows Segoe UI)
  shots.mjs               page screenshots at desktop and phone width + console/404/overflow checks
  check-editor.mjs        opens every page in the block editor and reports invalid blocks
```

## Commands (Git Bash, from this folder)

```
tools/wp.sh eval-file tools/setup-content.php --user=sradmin                       # add missing pages/posts
tools/wp.sh eval-file tools/setup-content.php force only=docs --user=sradmin       # overwrite one page from content/
tools/wp-demo.sh eval-file tools/demo-content.php --user=demo && node tools/plugin-shots.mjs && php tools/make-shots.php
php tools/make-images.php
node tools/shots.mjs features/ docs/       # no args = home (don't pass "/": Git Bash rewrites it)
node tools/check-editor.mjs
```

## Conventions

- Facts come from the plugin code (`scoped-search-replace.php`, `readme.txt`, `uninstall.php`). Change the site when
  the plugin changes: scope options, messages quoted in the docs troubleshooting table, requirements, changelog.
- Links are root-relative; the site must live at the root of a domain or subdomain.
- Never hard-code the download or WordPress.org link in pages: use the button classes above.
- No third-party requests: self-hosted font, no analytics, no visitor cookies.
