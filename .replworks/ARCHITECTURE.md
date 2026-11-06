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
| url        | string  | nullable, project homepage     |
| archived   | boolean | default false                  |
| timestamps |         |                                |

AI and Infra items live in their own tables (`ai_items`, `infra_items`) with name, value, and service only.

## project_items

| Column       | Type    | Notes                                        |
| ------------ | ------- | -------------------------------------------- |
| id           | id      |                                              |
| project_id   | FK      | references projects, restrict on delete      |
| name         | string  | required                                     |
| service_id   | FK      | nullable, references services, null on delete |
| environment  | string  | nullable, one of live / test / dev / none    |
| kind         | string  | nullable, one of token / password / pem / other |
| tags         | text    | nullable, free text, comma-separated        |
| value        | text    | required, the secret                         |
| url | text | nullable, URL |
| expires_at   | date    | nullable, record only                        |
| memo         | text    | nullable                                     |
| timestamps   |         |                                              |

## ai_items / infra_items

Minimal shape: name and value required, service optional, nothing else.

| Column     | Type   | Notes                              |
| ---------- | ------ | ---------------------------------- |
| id         | id     |                                    |
| name       | string | required                           |
| value      | text   | required, the secret               |
| service_id | FK     | nullable, references services, null on delete |
| timestamps |        |                                    |

## services

| Column     | Type    | Notes                                    |
| ---------- | ------- | ---------------------------------------- |
| id         | id      |                                          |
| name       | string  | unique, required                         |
| icon       | string  | nullable, icon URL (image later)         |
| is_project | boolean | default true, listed in Project Items    |
| is_ai      | boolean | default true, listed in AI Items         |
| is_infra   | boolean | default true, listed in Infra Items      |
| timestamps |         |                                          |

## Rules

- Every Item belongs to exactly one Project.
- A Project with Items cannot be deleted.
- Archived Projects are hidden from the default list; their Items remain searchable.
- Fingerprint and Copy Text are derived at read time, never stored.
