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

- [x] T-037 Wire credential sets into AI Items
  - Satisfies: PRODUCT_SPEC.md Service manifests; Credential sets
  - External boundary: no
  - Acceptance criteria:
    - The AI Item form offers only sets whose service allows the ai_items container.
    - Selecting a set renders its keys with labels and purposes; only values are entered.
    - Each AI Item instance records its manifest key and version; editing and deleting apply to the whole set.
    - Copying is per key and leaves stored values unchanged.
    - Manifest container filtering is covered by unit tests and AI set creation by a feature test.

- [x] T-038 Add read-only manifest viewer
  - Satisfies: PRODUCT_SPEC.md Manifest viewer
  - External boundary: no
  - Acceptance criteria:
    - The admin lists services, sets, versions, and field definitions from the manifest.
    - Each field shows its key, label, purpose, and kind.
    - The viewer provides no edit or write action.
    - A feature test verifies the viewer renders manifest definitions.

- [x] T-039 Implement provider-based credential system
  - Satisfies: PRODUCT_SPEC.md Provider definitions; Credentials; Storage constraints
  - External boundary: no
  - Acceptance criteria:
    - Providers are created in the admin and stored as JSON definitions in SQLite.
    - Invalid provider definitions are rejected with useful messages.
    - Credential forms render from the selected provider definition and validate input.
    - Multiple credentials can be created for one provider.
    - Sensitive values are encrypted individually; non-sensitive values remain JSON text.
    - Adding a sample provider requires no application code change.
    - Tests cover definition validation, required fields, optional fields, sensitive fields, creation, editing, multiple instances, and a new provider.
    - `pint --test` passes.
