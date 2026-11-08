# ARCHITECTURE

Data relationships only. The app is built with Filament Resources on top of these tables.

## Diagram

```
folders 1 ──< secrets (folder_id, restrict on delete)
ai_items (standalone, always in the AI container)
infra_items (standalone, always in the Infra container)
```

## folders

| Column     | Type    | Notes                          |
| ---------- | ------- | ------------------------------ |
| id         | id      |                                |
| name       | string  | required                       |
| note       | text    | nullable                       |
| url        | string  | nullable, folder homepage      |
| archived   | boolean | default false                  |
| timestamps |         |                                |

AI and Infra items live in their own tables (`ai_items`, `infra_items`) with name, value, and service only.

## secrets

| Column       | Type    | Notes                                        |
| ------------ | ------- | -------------------------------------------- |
| id           | id      |                                              |
| folder_id    | FK      | references folders, restrict on delete       |
| name         | string  | required                                     |
| service_id   | FK      | nullable, references services, null on delete |
| derived_from_id | FK   | nullable, self-reference to secrets, the source key this was derived from, null on delete |
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
`ai_items` additionally has nullable `url`.

| Column     | Type   | Notes                              |
| ---------- | ------ | ---------------------------------- |
| id         | id     |                                    |
| name       | string | required                           |
| value      | text   | required, the secret               |
| url        | text   | nullable, ai_items only            |
| service_id | FK     | nullable, references services, null on delete |
| timestamps |        |                                      |

## services

| Column     | Type    | Notes                                    |
| ---------- | ------- | ---------------------------------------- |
| id         | id      |                                          |
| name       | string  | unique, required                         |
| icon       | string  | nullable, icon URL (image later)         |
| is_secret  | boolean | default true, listed in Secrets          |
| is_ai      | boolean | default true, listed in AI Items         |
| is_infra   | boolean | default true, listed in Infra Items      |
| timestamps |         |                                          |

## Rules

- Every Secret belongs to exactly one Folder.
- A Secret may point to a Source Secret it was derived from (e.g. repo keys derived from a PAT). Deleting the source clears the link.
- A Folder with Secrets cannot be deleted.
- Archived Folders are hidden from the default list; their Secrets remain searchable.
- Copy Text is derived at read time, never stored.
