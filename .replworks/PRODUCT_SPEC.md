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
- service (optional): chosen from services defined in the manifest. Each service opts in per container type in the manifest.
- environment (optional): live, test, dev, none, or unset.
- tags (optional): free text, comma-separated.
- url, expiry date, memo, note (all optional).
- Folders have an optional note.

## Service manifests

- Services and the credentials they issue are defined in a version-controlled manifest, not created inside the app.
- A service defines one or more credential sets. Each set is identified by a service key and a set key, for example `x/app` and `x/api_key`.
- A credential set defines the keys it issues, in order. Each key has an identifier, a display label, its purpose, and a kind.
- A set may be available in one or more containers: Secrets or AI Items.
- Different sets of one service may issue different numbers of keys. Creating an X app issues three keys; obtaining a standalone X API key issues one.
- A service absent from the manifest is handled by a `custom` set in which the user defines keys and values directly.

## Credential sets

- A user creates a credential set by choosing a service, choosing one of its sets, naming the instance, and entering a value for each defined key.
- A credential set instance is one record. An X app produces one set with three values; a standalone X API key produces one set with one value.
- Every instance records which manifest key and version it was created from.
- Editing and deleting apply to the whole set at once.
- Copying is per key and puts that key's stored value on the clipboard unchanged.
- Set names are searchable; values are never searchable.

## Copy

- Copying puts the stored Value, or a selected key's value inside a credential set, on the clipboard unchanged.
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

## Global search

- Global search finds records by name across all containers: Secrets, Folders, Infras, Infra Secrets, AI Items, and Tags.
- Secret values are never searchable.
- Global search results open the matching record.

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
