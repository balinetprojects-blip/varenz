# Incident Response

## Severity

- **SEV-1:** Security breach, data exposure, complete outage
- **SEV-2:** Major feature unavailable, payment or form failure
- **SEV-3:** Partial degradation, broken asset, isolated error
- **SEV-4:** Cosmetic or low-impact issue

## Workflow

1. Detect and record.
2. Preserve logs and evidence.
3. Contain impact.
4. Restore service or roll back.
5. Identify the root cause.
6. Validate the fix.
7. Record prevention actions.
8. Test the backup or rollback path.

## Logging rules

- Never log passwords, tokens, API keys, or full sensitive records.
- Include timestamps, route, correlation ID, severity, and safe error context.
- Rotate logs and keep them outside public web access.
