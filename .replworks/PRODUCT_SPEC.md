# PRODUCT_SPEC

## Purpose

- A single-user vault for secret credentials used by the user's software services.
- Items are grouped by container: user Folders, Infra, and AI Items.
- Secrets use the full field set below. Infra Secrets use name, value, url, an optional region, and an optional project folder inside an Infra. AI Items use name, value, url, and service.
- Other containers are user-created Folders (e.g. one per repository, or one for an organization).
- Archived Folders are hidden from the default list, shown in an archive area, and can be restored. Their Secrets still appear in search.

## Item fields

- name (required): shown in the list.
- value (required): the secret. Never shown in lists; never stored as readable plain text in earlier product versions (research tool: plain storage is acceptable while iterating on structure).
- service (optional): chosen from the Services list. Each service opts in per container type.
- environment (optional): live, test, dev, none, or unset.
- tags (optional): free text, comma-separated.
- url, expiry date, memo, note (all optional).
- Folders have an optional note.

## Copy

- Copying puts the stored Value on the clipboard unchanged.
- Copying never changes the stored Value.
- kind (optional): token, password, pem, or other. Decides the copy rule.

## Folder rules

- Folders can be created, renamed, deleted, archived, and restored.
- Folders can be imported from GitHub repositories (Name = owner/repo, URL, Note = repo description), individually on the create form or in bulk from the Folders list.
- A Folder with Secrets or Infra Secrets cannot be deleted.
- Archiving and restoring change no names and no Secrets.
- Infra Secrets may be global or scoped to one project Folder.
- Infra groups Infra Secrets and their optional Regions, and supports note and archive.
- An Infra with Infra Secrets cannot be deleted.

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
