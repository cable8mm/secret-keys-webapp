# PRODUCT_SPEC

## Purpose

- A single-user vault for secret credentials used by the user's software services.
- Items are grouped by container. Every Item belongs to exactly one container.
- Project Items use the full field set below. AI and Infra Items use name, value, and service only.
- Infra and AI are fixed containers. They exist from the first run and cannot be renamed, deleted, or archived. Items inside them behave like ordinary Items.
- Other containers are user-created Projects (e.g. one per repository).
- Archived Projects are hidden from the default list, shown in an archive area, and can be restored. Their Items still appear in search.

## Item fields

- name (required): shown in the list.
- value (required): the secret. Never shown in lists; never stored as readable plain text in earlier product versions (research tool: plain storage is acceptable while iterating on structure).
- kind (optional): token, password, pem, or other. Decides the copy rule.
- service (optional): chosen from a fixed suggestion list, free text also allowed.
- environment (optional): live, test, dev, none, or unset.
- tags (optional): free text, e.g. `scope:deploy`, `server:prod-1`.
- usage location, issued date, expiry date, memo, note (all optional).
- Projects have an optional note.
- fingerprint (derived, never stored): last 4 characters of the trimmed Value; empty when fewer than 8 characters remain.

## Copy rules

- Non-pem kinds: trim leading and trailing whitespace, preserve everything in between.
- pem: trim, convert CR/CRLF to LF, end with exactly one newline.
- Copying never changes the stored Value.

## Project rules

- Projects can be created, renamed, deleted, archived, and restored.
- A Project with Items cannot be deleted.
- Archiving and restoring change no names and no Items.

## Non-goals

- Expiry badges and notifications.
- Last-used tracking and recent-use sorting.
- Showing `project:`-tagged Items together on a Project screen.
- Repository URL field.
- More than two levels of hierarchy.
- Multi-user, sync, phone access, hosted deployment.

## Constraints

- Runs on the user's own computer, reachable only from it.
- No login page.
