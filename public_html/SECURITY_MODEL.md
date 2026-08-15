# Security Model

## Request boundary

- The front controller in `index.php` catches unexpected errors and does not expose stack traces in production.
- `APP_ENV` is allow-listed; production debug mode is always disabled.
- Canonical URLs use the configured deployment origin while application assets stay same-origin.
- Apache blocks direct access to `app`, `config`, `data`, `storage`, hidden files, executable files under `assets`, backups and common project manifests.

## Submission controls

`POST /api/submissions` enforces:

- session CSRF validation;
- a hidden honeypot;
- a minimum two-second completion time;
- 10 submissions per hashed client IP per hour;
- required name, message and at least one contact method;
- fixed length limits and server-side text cleaning;
- email validation;
- an 8 MB upload limit and an explicit extension/MIME allow-list;
- private storage outside public assets;
- non-identifying IP hashes in stored records.

Successful submissions receive a generated `VSL` reference. Deterministic request intelligence does not send customer data to an external AI provider.

## Browser and transport controls

- HTTPS production responses receive HSTS.
- Apache sets `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` and a restrictive `Permissions-Policy`.
- External WhatsApp and download-tab links use `noopener noreferrer` where relevant.
- Search and content APIs return structured JSON and do not execute customer input.

## Deployment requirements

- Keep `storage/private` and `storage/logs` writable by PHP and inaccessible over HTTP.
- Keep `APP_DEBUG=0` and enforce HTTPS.
- Back up private submissions before replacement.
- Configure and verify an email/CRM notification path before promising instant staff notification.
