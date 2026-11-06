# ARCHITECTURE

Data relationships only. The app is built with Filament Resources on top of these tables.

## Diagram

```
projects 1 ──< project_items (project_id, restrict on delete)
ai_items (standalone, always in the AI container)
infra_items (standalone, always in the Infra container)
```

## projects

| Column     | Type    | Notes                          |
| ---------- | ------- | ------------------------------ |
| id         | id      |                                |
| name       | string  | required                       |
| note       | text    | nullable                       |
| is_fixed   | boolean | default false; true for Infra, AI |
| archived   | boolean | default false                  |
| timestamps |         |                                |

Seed rows: `Infra` and `AI` with `is_fixed = true`. They are never renamed, deleted, or archived.

## project_items

| Column       | Type    | Notes                                        |
| ------------ | ------- | -------------------------------------------- |
| id           | id      |                                              |
| project_id   | FK      | references projects, restrict on delete      |
| name         | string  | required                                     |
| service      | string  | nullable, suggestion list, free text allowed |
| environment  | string  | nullable, one of live / test / dev / none    |
| kind         | string  | nullable, one of token / password / pem / other |
| tags         | text    | nullable, free text                          |
| value        | text    | required, the secret                         |
| usage_location | text  | nullable                                     |
| issued_date  | date    | nullable                                     |
| expires_at   | date    | nullable, record only                        |
| memo         | text    | nullable                                     |
| timestamps   |         |                                              |

## ai_items / infra_items

Minimal shape: name and value required, service optional, nothing else.

| Column     | Type   | Notes                |
| ---------- | ------ | -------------------- |
| id         | id     |                      |
| name       | string | required             |
| value      | text   | required, the secret |
| service    | string | nullable             |
| timestamps |        |                      |

## Rules

- Every Item belongs to exactly one Project.
- A Project with Items cannot be deleted.
- Archived Projects are hidden from the default list; their Items remain searchable.
- Fingerprint and Copy Text are derived at read time, never stored.
