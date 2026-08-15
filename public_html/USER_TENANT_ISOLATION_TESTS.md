# User and Tenant Isolation Tests

## Required setup

- User A in Tenant/Workspace A
- User B in Tenant/Workspace B
- Administrator with explicitly defined scope

## Read tests

- Change record IDs in URLs and API requests.
- Attempt to list another user's records.
- Attempt to infer another tenant's data through search, counts, exports, or errors.

## Write tests

- Attempt to update another user's record.
- Attempt to delete another user's record.
- Attempt to attach or move data into another tenant.

## Authorization tests

- Call privileged routes without authentication.
- Call them with the wrong role.
- Call them directly even when the UI hides the controls.
- Confirm ownership and tenant filters are enforced in server-side queries.

## Expected result

Every unauthorized action must fail safely without leaking whether the target record exists.

## Evidence

Record request, authenticated role, target record, expected result, actual result, response code, log entry, and remediation.
