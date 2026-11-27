# ARCHITECTURE

Data relationships only. The app is built with Filament Resources on top of these tables.

## Diagram

```
providers 1 ──< credentials (provider_id, restrict on delete)
legacy: folders 1 ──< secrets, folders 1 ──< infra_secrets, infras 1 ──< infra_secrets
infras 1 ──< infra_regions, infra_regions 1 ──< infra_secrets
tags <──> secrets (legacy)
```

## providers

| Column      | Type    | Notes                                   |
| ----------- | ------- | --------------------------------------- |
| id          | id      |                                         |
| slug        | string  | unique, required, definition id         |
| name        | string  | required, searchable                    |
| description | text    | nullable                                |
| category    | string  | nullable, searchable metadata           |
| definition  | text    | required, JSON provider definition      |
| timestamps  |         |                                         |

## credentials

| Column            | Type    | Notes                                      |
| ----------------- | ------- | ------------------------------------------ |
| id                | id      |                                            |
| provider_id       | FK      | references providers, restrict on delete   |
| name              | string  | required, searchable                       |
| description       | text    | nullable                                   |
| credential_values | text    | required, JSON keyed by provider field keys |
| timestamps        |         |                                            |

## Rules

- A credential resolves its fields from the definition of its provider.
- A credential's value object holds one entry per field key of that definition.
- Sensitive field values are encrypted individually before persistence; non-sensitive values stay as JSON text.
- Encryption and decryption are performed by a storage service independent of Eloquent and the database.
- Removing a provider with credentials is prevented.
- Legacy `secrets`, `ai_items`, `infras`, `infra_secrets`, `folders`, and `tags` tables remain unchanged; no conversion is performed.
- Copy Text is derived at read time, never stored.
