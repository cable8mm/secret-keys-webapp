# ARCHITECTURE

## 1. Purpose

- This document defines the internal structure of the product specified in PRODUCT_SPEC.md.
- This document defines responsibility ownership, information flow, failure boundaries, and invariants.
- This document refers to PRODUCT_SPEC.md items by ID (FR, SCR, EL, ERR, AC).
- This document contains no technology, language, library, deployment, convention, or directory information.
- Every ambiguity found and its resolution are recorded in section 12.

## 2. Core Concepts

- Item: one stored credential record as defined in PRODUCT_SPEC.md.
- Project: a named group of Items as defined in PRODUCT_SPEC.md.
- Item Identity: an identifier assigned to an Item by CMP-09 at creation and never changed.
- Project Identity: an identifier assigned to a Project by CMP-09 at creation and never changed.
- Value: the secret text of an Item.
- Readable Value: a Value held as plain text during an operation.
- Protected Value: a Value in a form that is not readable as plain text.
- Fingerprint: derived text defined by FR-015 and FR-016.
- Copy Text: text derived from a Value by FR-027 and FR-028.
- Screen State: transient state of the current screen, including entered input content, masked or unmasked state of EL-105, visible delete prompts, selected filters, and Message Slot content.
- Result: the typed outcome of an operation. A Result is a Success or exactly one of: ValidationFailure, Rejection, PersistenceFailure, RetrievalFailure.

## 3. System Flow

- All user interaction enters through CMP-01.
- CMP-01 forwards each action to one of CMP-02, CMP-03, CMP-04, or CMP-05.
- CMP-02, CMP-03, and CMP-04 use CMP-05, CMP-06, CMP-07, CMP-08, and CMP-09 to complete the action.
- Every operation returns a Result to its caller.
- CMP-01 renders the Result into Screen State.
- Components are arranged in three tiers.
  - Tier 1: CMP-01.
  - Tier 2: CMP-02, CMP-03, CMP-04.
  - Tier 3: CMP-05, CMP-06, CMP-07, CMP-08, CMP-09.
- A component MUST call only components of a lower tier.
- CMP-01 MUST call only Tier 2 components and CMP-05.
- Tier 2 components MUST NOT call each other.
- Tier 3 components MUST NOT call any component.

## 4. Components

- CMP-01: Screen Layer. Presents SCR-001, SCR-002, SCR-003 and owns Screen State.
- CMP-02: Project Manager. Executes all Project operations.
- CMP-03: Item Manager. Executes all operations on a single Item.
- CMP-04: Item Finder. Produces the filtered, ordered Item list and Environment options.
- CMP-05: Copy Text Preparer. Applies the copy rule to a Value.
- CMP-06: Fingerprint Deriver. Applies the Fingerprint rule to a Value.
- CMP-07: Text Comparison Rule. Defines case-insensitive ordering and containment.
- CMP-08: Value Protector. Converts a Value between Readable Value and Protected Value.
- CMP-09: Record Store. Persists Project records and Item records.

## 5. Component Responsibilities

### CMP-01 Screen Layer

- Responsibilities:
  - Present the elements, states, and navigation of SCR-001, SCR-002, SCR-003.
  - Hold Screen State.
  - Apply masking to EL-105 as a presentation state only.
  - Show and hide delete prompts.
  - Write Copy Text to the Clipboard.
  - Convert each Result into Message Slot content of the current screen.
  - Retain entered input content when a Result is a failure.
  - Disable controls whose disabled state is defined in PRODUCT_SPEC.md.
- Inputs:
  - User actions on EL elements.
  - Results from CMP-02, CMP-03, CMP-04.
  - Copy Text from CMP-03 and CMP-05.
- Outputs:
  - Requests to CMP-02, CMP-03, CMP-04, CMP-05.
  - Text written to the Clipboard.
  - Rendered screens.
- Owns:
  - Screen State.
  - Clipboard access.
  - Message Slot content.
  - Navigation between screens.
- Does not own:
  - Validation of any input.
  - Fingerprint computation.
  - Copy Text computation.
  - Ordering, filtering, or matching.
  - Access to persisted records.
  - Protection of Values.

### CMP-02 Project Manager

- Responsibilities:
  - List Projects ordered by Project Name ascending using CMP-07.
  - Create a Project.
  - Rename a Project.
  - Delete a Project.
  - Validate Project Name: a Project Name that is empty or whitespace-only is a ValidationFailure of ERR-004.
  - Reject deletion of a Project that has at least one Item as a Rejection of ERR-005.
- Inputs:
  - Project Name.
  - Project Identity.
- Outputs:
  - Project list of Project Identity and Project Name.
  - Result of each operation.
- Owns:
  - Project Name validation.
  - The delete guard of FR-006.
  - Project ordering for lists and options.
- Does not own:
  - Item validation.
  - Item records.
  - Screen State.

### CMP-03 Item Manager

- Responsibilities:
  - Create an Item.
  - Update an Item.
  - Delete an Item.
  - Load one Item for editing, including Readable Value and Fingerprint.
  - Produce Copy Text for one stored Item by Item Identity.
  - Validate Item input and report every failing condition in one Result.
  - Protect the Value before storing.
- Inputs:
  - Item input: Item Name, Project Identity, Service, Environment, Value, Usage Location, Issued Date, Memo.
  - Item Identity.
- Outputs:
  - Item detail: all stored fields, Readable Value, Fingerprint.
  - Copy Text.
  - Result of each operation.
- Owns:
  - Item validation: ERR-001, ERR-002, ERR-003.
  - The order of write steps: validate, protect, store.
  - The rule that the Value is stored exactly as entered.
- Does not own:
  - Project validation.
  - Item list filtering and ordering.
  - The Fingerprint rule.
  - The copy rule.
  - Reversible conversion of Values.
  - Clipboard access.

### CMP-04 Item Finder

- Responsibilities:
  - Select Items matching the search text, Project filter selection, and Environment filter selection combined with logical AND.
  - Order the selected Items by FR-020.
  - Attach the Fingerprint to each selected Item.
  - Report whether at least one Item is stored.
  - List distinct non-empty Environment values ordered ascending using CMP-07.
- Inputs:
  - Search text.
  - Project Identity or no selection.
  - Environment value or no selection.
- Outputs:
  - List rows: Item Identity, Item Name, Service, Project Name, Environment, Fingerprint.
  - Flag: at least one Item is stored.
  - Environment value list.
  - Result of each operation.
- Owns:
  - The set of fields matched by search.
  - Filter semantics.
  - Item list ordering.
- Does not own:
  - Any Value in readable or protected form in its output.
  - Item writes.
  - The Fingerprint rule.
  - The comparison rule.

### CMP-05 Copy Text Preparer

- Responsibilities:
  - Remove leading and trailing whitespace from a Value.
  - Preserve every character between the first and last non-whitespace characters.
- Inputs:
  - Value text.
- Outputs:
  - Copy Text.
- Owns:
  - The copy rule of FR-027 and FR-028.
- Does not own:
  - Retrieval of Values.
  - Clipboard access.
  - Any change to stored Values.

### CMP-06 Fingerprint Deriver

- Responsibilities:
  - Remove leading and trailing whitespace from a Value.
  - Return the last 4 characters of the result.
  - Return empty text when the result has fewer than 8 characters.
- Inputs:
  - Value text.
- Outputs:
  - Fingerprint text.
- Owns:
  - The Fingerprint rule of FR-015 and FR-016.
- Does not own:
  - Storing the Fingerprint.
  - Retrieval of Values.

### CMP-07 Text Comparison Rule

- Responsibilities:
  - Compare two texts for ascending order after case folding, by character code order.
  - Decide whether a text contains another text after case folding.
- Inputs:
  - Two texts.
- Outputs:
  - Order result.
  - Containment result.
- Owns:
  - The definition of case-insensitive comparison for FR-020, FR-021, FR-025.
- Does not own:
  - Which fields are compared.
  - Exact equality of Environment values.

### CMP-08 Value Protector

- Responsibilities:
  - Convert a Readable Value to a Protected Value.
  - Convert a Protected Value to a Readable Value.
  - Report a failure when a Protected Value cannot be converted.
- Inputs:
  - Readable Value or Protected Value.
- Outputs:
  - Protected Value or Readable Value.
  - Conversion failure.
- Owns:
  - The only conversion between Readable Value and Protected Value.
- Does not own:
  - Storage of Protected Values.
  - Deciding when a Value is revealed.

### CMP-09 Record Store

- Responsibilities:
  - Persist Project records and Item records across stops and starts of the product.
  - Assign Project Identity and Item Identity.
  - Read, create, update, and delete records.
  - Count the Items of a Project.
  - Complete each write operation completely or not at all.
  - Report read and write failures.
- Inputs:
  - Project records.
  - Item records with Protected Value.
  - Identities.
- Outputs:
  - Stored records.
  - Counts.
  - Failure reports.
- Owns:
  - Persisted state.
  - Identity assignment.
  - Atomicity of writes.
  - The requirement that a deletion and its guard check occur in one atomic operation.
- Does not own:
  - Validation.
  - Protection of Values.
  - Ordering, filtering, or matching.

## 6. Responsibility Boundaries

- Screen State: CMP-01.
- Clipboard access: CMP-01.
- Message Slot content selection: CMP-01.
- Message wording: no component. Wording is supplied by the human owner.
- Project input validation: CMP-02.
- Item input validation: CMP-03.
- Project delete guard: CMP-02.
- Fingerprint rule: CMP-06.
- Copy rule: CMP-05.
- Case-insensitive comparison: CMP-07.
- Item list filtering and ordering: CMP-04.
- Project ordering: CMP-02.
- Value conversion: CMP-08.
- Persisted state and identities: CMP-09.
- Retrieval of Readable Value for row copy: CMP-03.
- Retrieval of Readable Value for Fingerprint derivation in the list: CMP-04.
- CMP-01 MUST NOT hold a stored Value other than the content of EL-105.
- CMP-04 MUST NOT return a Value.
- CMP-02 MUST NOT read or write Item content.
- CMP-03 MUST NOT filter or order Item lists.

## 7. Data Flow

- DF-01: Show or refresh the Item list on SCR-001
  - CMP-01 sends search text, Project filter selection, and Environment filter selection to CMP-04.
  - CMP-04 reads Items and Projects from CMP-09.
  - CMP-04 selects Items per FR-021, FR-022, FR-023, FR-024, FR-026 using CMP-07.
  - CMP-04 orders Items per FR-020 using CMP-07.
  - For each selected Item, CMP-04 converts the Protected Value with CMP-08, derives the Fingerprint with CMP-06, and discards the Readable Value.
  - CMP-04 returns rows and the flag that at least one Item is stored.
  - CMP-01 renders the Initial, Empty, No match, Populated, or Error state.
- DF-02: Populate filter and selection options
  - CMP-01 requests the Project list from CMP-02 for EL-002, EL-102, EL-203.
  - CMP-01 requests the Environment value list from CMP-04 for EL-003.
- DF-03: Copy from a list row (EL-005)
  - CMP-01 sends Item Identity to CMP-03.
  - CMP-03 reads the Item from CMP-09.
  - CMP-03 converts the Protected Value with CMP-08.
  - CMP-03 passes the Readable Value to CMP-05 and discards it.
  - CMP-03 returns Copy Text.
  - CMP-01 writes Copy Text to the Clipboard.
  - CMP-01 shows the confirmation of FR-031 after a successful write.
- DF-04: Copy from the form (EL-107)
  - CMP-01 sends the current content of EL-105 to CMP-05.
  - CMP-05 returns Copy Text.
  - CMP-01 writes Copy Text to the Clipboard.
  - CMP-01 shows the confirmation of FR-031 after a successful write.
- DF-05: Open SCR-002 in create mode
  - CMP-01 requests the Project list from CMP-02.
  - CMP-01 presents empty inputs.
- DF-06: Open SCR-002 in edit mode
  - CMP-01 sends Item Identity to CMP-03.
  - CMP-03 reads the Item from CMP-09.
  - CMP-03 converts the Protected Value with CMP-08.
  - CMP-03 derives the Fingerprint from the Readable Value with CMP-06.
  - CMP-03 returns all stored fields, the Readable Value, and the Fingerprint.
  - CMP-01 presents the fields with EL-105 masked.
- DF-07: Save an Item (EL-112)
  - CMP-01 sends Item input to CMP-03.
  - CMP-03 validates every field and checks that the Project Identity names an existing Project through CMP-09.
  - On any validation failure, CMP-03 returns a ValidationFailure listing every failing ERR and performs no write.
  - CMP-03 protects the Value with CMP-08.
  - CMP-03 writes the record through CMP-09.
  - CMP-03 returns the Result.
  - CMP-01 navigates to SCR-001 on Success and shows the failure in the Message Slot otherwise.
- DF-08: Delete an Item (EL-114 then EL-115)
  - CMP-01 shows the delete prompt from Screen State.
  - CMP-01 sends Item Identity to CMP-03 after EL-115 is activated.
  - CMP-03 deletes the record through CMP-09.
  - CMP-01 navigates to SCR-001 on Success and shows the failure in the Message Slot otherwise.
- DF-09: Create, rename, or delete a Project on SCR-003
  - CMP-01 sends Project Name or Project Identity to CMP-02.
  - CMP-02 validates Project Name for create and rename.
  - CMP-02 for deletion asks CMP-09 to delete the Project only if the Item count is zero, in one atomic operation.
  - CMP-02 returns the Result.
  - CMP-01 renders the updated list on Success and shows the failure in the Message Slot otherwise.

## 8. Architectural Rules

- AR-01: Only CMP-09 reads or writes persisted records.
- AR-02: Only CMP-08 converts between Readable Value and Protected Value.
- AR-03: Only CMP-01 accesses the Clipboard.
- AR-04: Only CMP-01 holds Screen State.
- AR-05: Each rule listed in section 6 MUST be implemented in exactly one component.
- AR-06: A component MUST report every failure as a Result, not through a side channel.
- AR-07: A write operation MUST execute in this order: validate, protect, store.
- AR-08: A write operation MUST complete entirely or not at all.
- AR-09: A read operation MUST NOT modify records.
- AR-10: CMP-01 MUST NOT validate input, derive Fingerprints, prepare Copy Text, filter, order, or match.
- AR-11: Duplicated rule logic across components MUST NOT exist.

## 9. Failure Boundaries

- FB-01: A failure of CMP-09 on a read becomes a RetrievalFailure.
- FB-02: A failure of CMP-09 on a write becomes a PersistenceFailure.
- FB-03: A failure of CMP-08 when converting a Protected Value to a Readable Value becomes a RetrievalFailure.
- FB-04: A failure of CMP-08 when converting a Readable Value to a Protected Value becomes a PersistenceFailure.
- FB-05: A ValidationFailure ends the operation before any write.
- FB-06: A Rejection ends the operation before any write.
- FB-07: A Clipboard write failure is detected by CMP-01, is classified as ERR-006, and changes no record.
- FB-08: A RetrievalFailure in a list operation yields no partial list.
- FB-09: An operation that targets a record that no longer exists fails as a RetrievalFailure for reads and as a PersistenceFailure for writes.
- FB-10: A failure in one operation MUST NOT change the result of any other operation.
- Result classification:
  - ValidationFailure: ERR-001, ERR-002, ERR-003, ERR-004.
  - Rejection: ERR-005.
  - ClipboardFailure: ERR-006.
  - PersistenceFailure: ERR-007.
  - RetrievalFailure: ERR-008 on SCR-001, and the same observable behavior in the Message Slot of SCR-002 (EL-118) and SCR-003 (EL-211).

## 10. Non-Goals

- No caching component.
- No component that notifies other components of changes.
- No background processing component.
- No extension or plug-in points.
- No history or versioning of Items or Projects.
- No concurrency control between simultaneous screens.
- No component that owns user identity or authorization.
- No separate read model and write model of records.
- No partial results from list operations.

## 11. Architectural Invariants

- INV-01: Every Item references exactly one existing Project at all times.
- INV-02: An Item references its Project by Project Identity only.
- INV-03: Project Identity and Item Identity never change.
- INV-04: A Value exists in persisted records only as a Protected Value.
- INV-05: No Fingerprint, Copy Text, or fragment of a Value is persisted.
- INV-06: A Readable Value exists only during a write step, a Fingerprint derivation, a Copy Text preparation, or an Item load for editing, and is discarded when that step ends.
- INV-07: Item list output and search output never contain a Value.
- INV-08: The stored Value equals the entered Value exactly.
- INV-09: A Fingerprint always equals the derivation of the stored Value at the time it is read.
- INV-10: A failed or rejected operation leaves persisted records unchanged.
- INV-11: The system accepts user interaction only through CMP-01.

## 12. Resolved Decisions

- RD-01: Retrieval failure outside SCR-001
  - Ambiguous: PRODUCT_SPEC.md defines retrieval failure only for SCR-001 (ERR-008).
  - Decided: SCR-002 and SCR-003 show the retrieval error in their own Message Slot, and data-dependent elements are not presented.
  - Why: the user chose this behavior.
- RD-02: Whitespace-only Item Name
  - Ambiguous: ERR-001 states "empty"; ERR-002 and ERR-004 also state whitespace-only.
  - Decided: a whitespace-only Item Name is a ValidationFailure of ERR-001.
  - Why: the user chose rejection for consistency with ERR-002 and ERR-004.
- RD-03: Fingerprint persistence
  - Ambiguous: FR-017 says the Fingerprint is recomputed when the Value is stored; PRODUCT_SPEC.md forbids Values stored as readable plain text.
  - Decided: the Fingerprint is derived at read time and never persisted.
  - Why: persisting a fragment of a Value in readable form conflicts with the plain-text constraint; derivation at read time satisfies FR-015 through FR-017 and AC-013 through AC-015.
- RD-04: Which component supplies a Value for row copy
  - Ambiguous: PRODUCT_SPEC.md states the Clipboard receives the Value but not which component retrieves it.
  - Decided: CMP-03 retrieves the Value and returns only Copy Text to CMP-01.
  - Why: FR-034 limits where a Value is displayed, and CMP-01 should not receive a Value it does not need.
- RD-05: Source of Copy Text on SCR-002
  - Ambiguous: FR-029 copies the current content of EL-105, including unsaved edits.
  - Decided: CMP-01 sends that content to CMP-05, the same component used by DF-03.
  - Why: the copy rule has one owner.
- RD-06: Project reference by identity
  - Ambiguous: PRODUCT_SPEC.md does not require unique Project Names, and FR-004 requires Items to survive a rename.
  - Decided: Items reference Projects by Project Identity.
  - Why: Project Name cannot serve as a key.
- RD-07: Ordering of Project options and rows
  - Ambiguous: PRODUCT_SPEC.md orders Items (FR-020) but not Projects in EL-002, EL-102, EL-203.
  - Decided: Projects are ordered by Project Name ascending using CMP-07.
  - Why: it matches the Project ordering already defined in FR-020.
- RD-08: Definition of case-insensitive comparison
  - Ambiguous: PRODUCT_SPEC.md states case-insensitive without a rule for ordering characters.
  - Decided: texts are case-folded and then compared by character code order.
  - Why: this is deterministic and owned by CMP-07 alone.
- RD-09: Search text handling
  - Ambiguous: PRODUCT_SPEC.md does not state whether search text is trimmed or what empty search text matches.
  - Decided: search text is used exactly as entered, and empty search text matches every Item.
  - Why: no normalization is specified, and FR-018 shows every Item when no search text is set.
- RD-10: Several failing validation conditions at once
  - Ambiguous: PRODUCT_SPEC.md defines each ERR separately.
  - Decided: CMP-03 reports every failing condition in one ValidationFailure.
  - Why: each ERR states that its error MUST be shown.
- RD-11: Project reference that names no existing Project
  - Ambiguous: ERR-003 covers "no Project selected" only.
  - Decided: a Project Identity that names no existing Project is a ValidationFailure of ERR-003.
  - Why: INV-01 requires an existing Project.
- RD-12: Value that cannot be converted
  - Ambiguous: PRODUCT_SPEC.md defines no behavior when a stored Value cannot be made readable.
  - Decided: the failure is a RetrievalFailure and a list operation returns no partial list.
  - Why: showing an empty Fingerprint would contradict FR-016, which allows an empty Fingerprint only for short Values.
- RD-13: Concurrent screens and missing records
  - Ambiguous: PRODUCT_SPEC.md defines one user and no behavior for a record changed elsewhere.
  - Decided: no concurrency control exists, the last write wins, and an operation on a missing record fails per FB-09.
  - Why: this follows the single-user definition with no added behavior.
- RD-14: Owner of validation
  - Ambiguous: PRODUCT_SPEC.md does not state which layer validates.
  - Decided: CMP-02 and CMP-03 validate their own inputs, and CMP-01 only presents the Results.
  - Why: each rule has one owner (AR-05, AR-10).
- RD-15: Owner of Environment options
  - Ambiguous: FR-025 defines the options without naming a producer.
  - Decided: CMP-04 derives them from stored Items at read time and nothing is stored.
  - Why: the options must always match stored Items.
- RD-16: Fields compared by search and Value exclusion
  - Ambiguous: none beyond FR-021 and FR-022.
  - Decided: CMP-04 never reveals a Value to match search, and reveals a Value only for rows already selected.
  - Why: FR-022 and INV-07.
- RD-17: Delete guard atomicity
  - Ambiguous: FR-006 does not state how the Item count check and the deletion relate.
  - Decided: CMP-09 performs the check and the deletion in one atomic operation.
  - Why: INV-01 must hold at every moment.
