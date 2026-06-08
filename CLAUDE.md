# Voiles & Company Website

Single-page PHP site for Voiles & Company, an accounting firm in Knoxville, TN. No build system, no dependencies — pure PHP, HTML, CSS, and vanilla JS.

## Local development

- **URL:** https://voilesandco.test (Laravel Valet)
- **Browser testing:** `agent-browser` is available for visual review

## Structure

```
app/
├── config.php          # Secret config — git-ignored, one level above webroot
├── config.example.php  # Template showing required keys
└── public/             # Webroot (Valet points here)
    ├── index.php       # Entire site: markup, inline CSS, inline JS
    └── lib/scripts/
        └── contact.php # Contact form POST handler — returns JSON
```

## Configuration

`config.php` lives **one directory above** `public/` and is never committed. Copy `config.example.php` to create it. Required keys:

| Key | Purpose |
|-----|---------|
| `smtp2go_api_key` | SMTP2GO delivery API key |
| `contact_to` | Recipient email for form submissions |
| `from_email` | Sender address (must be verified in SMTP2GO) |
| `from_name` | Sender display name |
| `email_subject` | Subject template; supports `{name}` token |
| `turnstile_site_key` | Cloudflare Turnstile public site key |
| `turnstile_secret` | Cloudflare Turnstile secret for server-side verify |
| `site_name` | Site name string |
| `site_url` | Canonical URL |

## Contact form flow

1. User submits → JS intercepts and POSTs to `/lib/scripts/contact.php`
2. PHP validates name + email, then verifies Cloudflare Turnstile token via `https://challenges.cloudflare.com/turnstile/v0/siteverify`
3. On success, sends HTML + plain-text email via SMTP2GO REST API (`https://api.smtp2go.com/v3/email/send`)
4. Reply-To is set to the submitter's email; sender display name is the submitter's name
5. Returns `{"ok": true}` on success or `{"ok": false, "error": "..."}` on failure

## Styling

All CSS lives inline in `<style>` inside `public/index.php`. Key CSS custom properties:

- `--bg` / `--panel` / `--panel-2` — background layers
- `--brand` (`#41d3ff`) / `--brand2` (`#79ffa8`) — accent colors (cyan / green)
- `--text` / `--muted` / `--muted2` — text hierarchy
- `--radius` / `--radius2` — border radii
- `--max` (`1120px`) — max content width

Breakpoints: `980px` (tablet: single-column hero, hidden nav) and `640px` (mobile: full-width cards).

## Page sections (in order)

1. **Hero** — headline, subhead, CTA buttons, quick-action panel
2. **Services** (`#services`) — three cards: Tax Preparation, Financial Statements, Bookkeeping
3. **Process** (`#process`) — two side-by-side panels: "Our process" steps + "What to expect"
4. **About** (`#about`) — who we help (testimonials panel is commented out, placeholder)
5. **Contact** (`#contact`) — contact form + firm details (address, phone, email)

## What to avoid

- No build pipeline — don't introduce npm, webpack, or a CSS preprocessor
- No PHP frameworks — keep it plain PHP
- Don't split CSS out of `index.php` without confirming with the user first
- Testimonials section in `#about` is intentionally commented out pending real client quotes
