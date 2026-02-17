---
title: "Voiles & Co. Website"
author: "https://github.com/mwender"
website: "https://voilesandco.com/"
created_by: "https://github.com/mwender"
created_at: "2026-02-17"
version: "0.3.0"
license: "MIT"
---

Voiles & Co. is a single-page website for a Knoxville, TN accounting firm. It highlights core services, process, and contact information in a clean, modern layout. This repo contains the static site markup, styles, and scripts powering the public-facing page.

## Changelog

### 0.3.0
- Refactored contact form script configuration lookup.
- Added config-driven email subject templates with `{name}` support.
- Set sender display name from form submissions and added Reply-To to the submitter.
- Added a top note to the email content.

### 0.2.0
- Added contact form processing with Turnstile spam protection and SMTP2GO delivery.
- Added config-driven setup for form secrets and email routing.

### 0.1.0
- Initial public site and styling.
