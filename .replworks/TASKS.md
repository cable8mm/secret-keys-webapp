# TASKS

- [x] T-035 Enable global search
  - Satisfies: PRODUCT_SPEC.md Global search
  - External boundary: no
  - Acceptance criteria:
    - Global search finds records by name across Secrets, Folders, Infras, Infra Secrets, AI Items, and Tags.
    - Secret values are never searchable.
    - Global search results open the matching record.
    - A feature test verifies the global search behavior.

- [x] T-036 Manifest-driven credential sets
  - Satisfies: PRODUCT_SPEC.md Service manifests; Credential sets
  - External boundary: no
  - Acceptance criteria:
    - Services and sets are defined in a version-controlled manifest containing no secret values.
    - Selecting a service and set renders its keys with labels and purposes; only values are entered.
    - An X app set stores three values; a standalone X API key set stores one.
    - Each instance records its manifest key and version; editing and deleting apply to the whole set.
    - Copying is per key and leaves stored values unchanged.
    - Services absent from the manifest are handled by a custom set.
    - Manifest validation and resolution are covered by unit tests.
    - `pint --test` passes.
