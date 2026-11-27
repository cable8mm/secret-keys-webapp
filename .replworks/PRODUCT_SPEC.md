# PRODUCT_SPEC

## Purpose

- A fully local, single-user vault for API keys, tokens, and other developer credentials.
- Credentials are described by user-created Provider Definitions stored as JSON in SQLite.
- Adding a provider requires no application code change.
- Existing Folders, Infras, AI Items, and Tags data is left untouched. Converting it is out of scope.

## Provider definitions

- A user creates a provider in the admin before creating credentials for it.
- A definition contains `schema_version`, `id`, `name`, optional `description` and `category`, and `credentials.fields`.
- Each field has `key`, `label`, `type`, `required`, and `sensitive`. Optional properties are `description`, `placeholder`, `default`, `validation`, `multiline`, `options`, and `group`.
- Field types are limited to text, secret, textarea, url, email, number, and select.
- Validation supports required fields, basic type checks, minimum and maximum length, patterns, and select options.
- Invalid definitions are rejected with useful error messages.

## Credentials

- A user selects a provider first, then creates a credential instance for it.
- A credential has a user-defined name, an optional description, and values keyed by the provider's field keys.
- Multiple credential instances may use the same provider.
- Editing shows the stored values; sensitive values are encrypted at rest.
- Credential names are searchable; values are never searchable.

## Storage constraints

- SQLite is the single source of truth. Provider definitions and credential values are stored as JSON; searchable metadata lives in regular columns.
- No server-side database, backend API, account system, multi-user support, or remote synchronization.
- Sensitive credential values are encrypted before persistence using the application's existing encryption mechanism. Non-sensitive values remain readable JSON.
- The encryption key stays in `.env` as `APP_KEY`, backed up separately from the SQLite file. OS keychain protection is a future consideration.
- Persistence is isolated behind a storage service so future encrypted backup and restoration can be added without changing the UI. iCloud sync is not implemented; the SQLite file is not assumed safe to sync directly.

## Folder rules

- Folders can be created, renamed, deleted, archived, and restored.
- Folders can be imported from GitHub repositories (Name = owner/repo, URL, Note = repo description), individually on the create form or in bulk from the Folders list.
- A Folder with existing legacy Secrets or Infra Secrets cannot be deleted.
- Archiving and restoring change no names and no Secrets.
- Infra Secrets may be global or scoped to one project Folder.
- Infra groups Infra Secrets and their optional Regions, and supports note and archive.
- An Infra with Infra Secrets cannot be deleted.

## Global search

- Global search finds records by name across Providers, Credentials, Folders, Infras, Infra Secrets, and Tags.
- Credential values are never searchable.
- Global search results open the matching record.

## Non-goals

- Expiry badges and notifications.
- Last-used tracking and recent-use sorting.
- Repository URL field.
- More than two levels of hierarchy.
- Multi-user, sync, phone access, hosted deployment.
- OAuth2, SSH, JWT, and other authentication flows.
- iCloud synchronization, cloud secret management, conflict resolution, and server infrastructure.

## Constraints

- Runs on the user's own computer, reachable only from it.
- No login page.
