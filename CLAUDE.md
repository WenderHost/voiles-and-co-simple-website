# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Single-page PHP site for Voiles & Company, an accounting firm in Knoxville, TN. No build system, no package manager, no dependencies — plain PHP, inline CSS, and vanilla JS.

## Local development

Served via Laravel Valet at **https://voilesandco.test**. No build step required; Valet serves `public/` directly.

`agent-browser` is available for visual review of the local site.

There are no tests, no linter, and no compilation step.

## File structure

```
app/                        # Repo root
├── config.php              # Runtime secrets — git-ignored, must exist above webroot
├── config.example.php      # Documents all required config keys
├── data/                   # Git-ignored; created automatically on first form submit
│   └── submissions.sqlite  # SQLite store for contact form submissions
├── lib/
│   └── db.php              # get_db($app_root): opens PDO, creates table if absent
└── public/                 # Webroot (Valet document root)
    ├── index.php           # Entire site: markup, all CSS (inline), all JS (inline)
    ├── admin/
    │   └── index.php       # HTTP Basic Auth admin UI — lists/expands/deletes submissions
    └── lib/scripts/
        └── contact.php     # Contact form POST endpoint — returns JSON only
```

## Architecture: config loading

`config.php` lives **one directory above `public/`** and is never web-accessible. Both entry points load it by walking up the directory tree at runtime — the path is never hardcoded:

- `index.php` resolves it as `dirname(__DIR__) . '/config.php'`
- `contact.php` resolves it as `dirname(__DIR__, 3) . '/config.php'` (up from `public/lib/scripts/`)

This means the config path logic differs between the two files. When adding new PHP files, derive the config path from `__DIR__` rather than assuming a fixed path.

## Architecture: contact form flow

The form in `index.php` submits via `fetch()` (not a page reload). The full pipeline:

1. JS POSTs `FormData` to `/lib/scripts/contact.php`
2. `contact.php` validates name + email, then verifies the Cloudflare Turnstile token against `https://challenges.cloudflare.com/turnstile/v0/siteverify`
3. On valid token, sends HTML + plain-text email via SMTP2GO REST API (`https://api.smtp2go.com/v3/email/send`) with `Reply-To` set to the submitter's email
4. After a successful send, inserts the submission into SQLite via `lib/db.php` — this is non-fatal; a DB error is logged but does not change the `{"ok": true}` response
5. Returns `{"ok": true}` or `{"ok": false, "error": "..."}` — the JS in `index.php` reads this to update the inline status message

All error states (`config missing`, `validation failed`, `Turnstile failed`, `send failed`) follow the same JSON shape. `turnstile_reset: true` in the response body signals the JS to call `window.turnstile.reset()`.

## Architecture: admin UI

`public/admin/index.php` is protected by HTTP Basic Auth — credentials come from `admin_user` / `admin_password` in `config.php`. If either key is missing, the page returns 503 rather than prompting for credentials.

Actions (mark read/unread, delete) are plain HTML form POSTs back to the same page, followed by a `Location:` redirect (PRG pattern) to prevent duplicate submissions on refresh. Inline row expansion uses native `<details>`/`<summary>` — no JavaScript except the delete confirmation `confirm()` dialog.

## Config keys

| Key | Purpose |
|-----|---------|
| `smtp2go_api_key` | SMTP2GO delivery API key |
| `contact_to` | Recipient address for form submissions |
| `from_email` | Sender address (must be verified in SMTP2GO) |
| `from_name` | Fallback sender display name |
| `email_subject` | Subject template; `{name}` is replaced with the submitter's name |
| `turnstile_site_key` | Cloudflare Turnstile public key (rendered in the form) |
| `turnstile_secret` | Cloudflare Turnstile secret (server-side verify only) |
| `site_name` | Site name string |
| `site_url` | Canonical URL |
| `admin_user` | HTTP Basic Auth username for `/admin` |
| `admin_password` | HTTP Basic Auth password for `/admin` |

## Styling notes

All CSS is in a single `<style>` block inside `index.php`. Key custom properties:

- `--bg` / `--panel` / `--panel-2` — dark background layers
- `--brand` (`#41d3ff`) / `--brand2` (`#79ffa8`) — cyan / green accents
- `--text` / `--muted` / `--muted2` — text opacity hierarchy
- `--max` (`1120px`) — content width cap

Breakpoints: `980px` (single-column layout, hamburger nav) and `640px` (full-width cards).

## Constraints

- No build pipeline — do not introduce npm, webpack, or a CSS preprocessor
- No PHP frameworks or Composer packages
- The testimonials panel inside `#about` is intentionally commented out pending real client quotes — do not remove the comment block
