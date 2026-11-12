# ARCHITECTURE

Data relationships only. The app is built with Filament Resources on top of these tables.

## Diagram

```
folders 1 ──< secrets (folder_id, restrict on delete)
infras 1 ──< infra_secrets (infra_id, restrict on delete)
infras 1 ──< infra_regions
infra_regions 1 ──< infra_secrets (region_id, null on delete)
ai_items (standalone, always in the AI container)
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

AI items live in their own table (`ai_items`). Infra secrets are grouped by `infras`.

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

## ai_items

Minimal shape: name and value required, service optional.

| Column     | Type   | Notes                              |
| ---------- | ------ | ---------------------------------- |
| id         | id     |                                    |
| name       | string | required                           |
| value      | text   | required, the secret               |
| url        | text   | nullable                           |
| service_id | FK     | nullable, references services, null on delete |
| timestamps |        |                                      |

## infras

| Column     | Type    | Notes                          |
| ---------- | ------- | ------------------------------ |
| id         | id      |                                |
| name       | string  | required                       |
| note       | text    | nullable                       |
| archived   | boolean | default false                  |
| timestamps |         |                                |

## infra_regions

| Column     | Type   | Notes                              |
| ---------- | ------ | ---------------------------------- |
| id         | id     |                                    |
| infra_id   | FK     | references infras, cascade on delete |
| name       | string | required, unique per infra        |
| timestamps |        |                                    |

## infra_secrets

Minimal shape: name and value required, infra required, region optional.

| Column     | Type   | Notes                              |
| ---------- | ------ | ---------------------------------- |
| id         | id     |                                    |
| infra_id   | FK     | references infras, restrict on delete |
| region_id  | FK     | nullable, references infra_regions, null on delete |
| name       | string | required                           |
| value      | text   | required, the secret               |
| url        | text   | nullable                           |
| timestamps |        |                                    |

## services

| Column     | Type    | Notes                                    |
| ---------- | ------- | ---------------------------------------- |
| id         | id      |                                          |
| name       | string  | unique, required                         |
| icon       | string  | nullable, icon URL (image later)         |
| is_secret  | boolean | default true, listed in Secrets          |
| is_ai      | boolean | default true, listed in AI Items         |
| timestamps |         |                                          |

## Rules

- Every Secret belongs to exactly one Folder.
- A Secret may point to a Source Secret it was derived from (e.g. repo keys derived from a PAT). Deleting the source clears the link.
- A Folder with Secrets cannot be deleted.
- Archived Folders are hidden from the default list; their Secrets remain searchable.
- Every Infra Secret belongs to exactly one Infra.
- An Infra Secret may optionally point to one Region of its Infra.
- An Infra with Infra Secrets cannot be deleted.
- Copy Text is derived at read time, never stored.
