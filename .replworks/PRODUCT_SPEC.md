# PRODUCT_SPEC

## Purpose

- The product is a single-user vault for secret credentials used by the user's software services.
- Item: one stored credential record.
- Project: a named group to which every Item belongs.
- Value: the secret text of an Item.
- Fingerprint: derived text shown for an Item that identifies the stored Value without revealing it.
- Clipboard: the clipboard of the operating system of the user's computer.
- Message Slot: a region of a screen that displays a confirmation or error message.
- Persisted: available after the product is stopped and started again.
- The product stores Items grouped by Project.
- The product finds Items by text and by filter.
- The product displays the Fingerprint of each Item.
- The product copies a Value to the Clipboard.

## Problem

- A Value entered into a service is sometimes not the intended Value.
- The user does not identify the wrong Value at the moment of entry.
- Values are currently kept in an unstructured notepad.

## Product Goals

- The user stores Values grouped by Project.
- The user finds an Item by entering text.
- The user identifies a stored Value by its Fingerprint without revealing the Value.
- The user copies a Value to the Clipboard without leading or trailing whitespace.
- The user changes any field of an Item at any time.

## Users

- One user.
- The user is a software developer who operates multiple services.
- The product has no concept of multiple users.

## Inputs

- Project Name: single-line text; required.
- Item Name: single-line text; required.
- Item Project: one existing Project; required.
- Item Service: single-line text; optional.
- Item Environment: single-line text; optional.
- Item Value: multi-line text with any characters; required.
- Item Usage Location: multi-line text; optional.
- Item Issued Date: calendar date; optional.
- Item Memo: multi-line text; optional.
- Search text: single-line text; optional.
- Project filter selection: one Project or no selection.
- Environment filter selection: one Environment value or no selection.
- Delete confirmation: confirm or cancel.

## Outputs

- Item rows on SCR-001.
- Fingerprint of each Item.
- Clipboard text.
- Message Slot content: copy confirmation, copy error, validation errors, persistence errors.
- Persisted Projects and Items.

## Constraints

- The product MUST run on the user's own computer.
- The product MUST be reachable only from the computer on which it runs.
- Supported client: a desktop web browser on the computer on which the product runs.
- Specific browsers: None stated.
- Data retention: Projects and Items MUST remain Persisted until the user deletes them.
- Values MUST NOT be stored as readable plain text in the Persisted data.
- Performance: None stated.
- Offline behavior: None stated.
- Accessibility: None stated.

## External Systems [UNVERIFIED]

- Clipboard of the operating system.
- Behavior relied on: the product writes text to the Clipboard and the user pastes that text into another application.
- Dependent IDs: FR-027, FR-028, FR-029, FR-031, EL-005, EL-107, ERR-006.

## User Interface [UNVERIFIED]

Global rules:

- Arrangement details not specified in this section are chosen by the implementing agent.
- The implementing agent MUST choose the simplest arrangement that satisfies every FR.
- The implementing agent MUST NOT add screens, elements, fields, or behaviors that are not listed in this document.
- Wording of labels and messages is supplied by the human owner.
- The implementing agent MUST use placeholder wording until the human owner replaces it.

### SCR-001

- ID: SCR-001
- Purpose: find Items, identify Items by Fingerprint, copy Values.
- Arrangement:
  - EL-006 and EL-007 are grouped at the top.
  - EL-001, EL-002, EL-003 are grouped below EL-006 and EL-007.
  - EL-010 is placed above EL-004.
  - EL-004 is placed below EL-001, EL-002, EL-003.
  - EL-005 is placed inside each row of EL-004.
- Elements:
  - EL-001: search input; purpose: restrict rows by text; user actions: type text, clear text.
  - EL-002: Project filter; purpose: restrict rows to one Project; user actions: select one Project, select no Project.
  - EL-003: Environment filter; purpose: restrict rows to one Environment value; user actions: select one Environment value, select no Environment value.
  - EL-004: Item row list; purpose: show Items; user actions: activate a row.
  - EL-005: row copy control; purpose: copy the Value of the row Item; user actions: activate.
  - EL-006: new Item control; purpose: start creating an Item; user actions: activate.
  - EL-007: Projects control; purpose: open Project management; user actions: activate.
  - EL-008: no-Items message slot; purpose: indicate that no Item is stored; user actions: none.
  - EL-009: no-match message slot; purpose: indicate that no Item matches search and filters; user actions: none.
  - EL-010: Message Slot; purpose: show copy confirmation and copy error; user actions: none.
  - EL-011: loading indicator; purpose: indicate that Items are being retrieved; user actions: none.
- States:
  - Initial: EL-001 is empty; EL-002 and EL-003 have no selection; EL-004 shows every stored Item.
  - Empty: no Item is stored; EL-008 is shown; EL-004 is not shown; EL-006 is enabled.
  - No match: at least one Item is stored and no Item satisfies search and filters; EL-009 is shown; EL-004 is not shown.
  - Loading: EL-011 is shown in place of EL-004.
  - Populated: EL-004 shows one row per matching Item.
  - Error: Items cannot be retrieved; EL-010 shows the error of ERR-008; EL-004 is not shown.
  - Disabled: none.
- Navigation:
  - activate a row of EL-004 outside EL-005 -> SCR-002 in edit mode for that Item
  - activate EL-006 -> SCR-002 in create mode
  - activate EL-007 -> SCR-003
- Interactions:
  - type text in EL-001 -> EL-004 shows only rows satisfying FR-021, FR-022, FR-026
  - select a Project in EL-002 -> EL-004 shows only rows satisfying FR-023
  - select an Environment value in EL-003 -> EL-004 shows only rows satisfying FR-024
  - activate EL-005 -> the Clipboard receives the Value per FR-027 and FR-028; EL-010 shows the confirmation of FR-031

### SCR-002

- ID: SCR-002
- Purpose: create an Item, edit an Item, reveal and copy a Value, delete an Item.
- Modes: create, edit.
- Arrangement:
  - EL-101 through EL-111 are arranged in one vertical group in this order: EL-101, EL-102, EL-103, EL-104, EL-105, EL-109, EL-110, EL-111.
  - EL-106, EL-107, EL-108 are grouped adjacent to EL-105.
  - EL-112 and EL-113 are grouped below the vertical group.
  - EL-114 is separated from EL-112 and EL-113.
  - EL-118 is placed above the vertical group.
- Elements:
  - EL-101: Item Name input; purpose: enter Item Name; user actions: type text.
  - EL-102: Project selection; purpose: choose the Project of the Item; user actions: select one Project.
  - EL-103: Service input; purpose: enter Service; user actions: type text.
  - EL-104: Environment input; purpose: enter Environment; user actions: type text.
  - EL-105: Value input; purpose: enter Value; user actions: type text, paste text.
  - EL-106: reveal toggle; purpose: switch EL-105 between masked and unmasked; user actions: activate.
  - EL-107: Value copy control; purpose: copy the content of EL-105; user actions: activate.
  - EL-108: Fingerprint display; purpose: show the Fingerprint of the stored Value; user actions: none.
  - EL-109: Usage Location input; purpose: enter where the Value is used; user actions: type text.
  - EL-110: Issued Date input; purpose: enter the date the Value was issued; user actions: select a date, clear the date.
  - EL-111: Memo input; purpose: enter a Memo; user actions: type text.
  - EL-112: save control; purpose: store the Item; user actions: activate.
  - EL-113: cancel control; purpose: leave without storing; user actions: activate.
  - EL-114: delete control; purpose: start deleting the Item; user actions: activate; shown in edit mode only.
  - EL-115: delete confirm control; purpose: confirm deletion; user actions: activate; shown after EL-114 is activated.
  - EL-116: delete cancel control; purpose: abort deletion; user actions: activate; shown after EL-114 is activated.
  - EL-117: Projects control; purpose: open Project management; user actions: activate; shown only when no Project exists.
  - EL-118: Message Slot; purpose: show copy confirmation, copy error, validation errors, persistence errors; user actions: none.
- States:
  - Initial in create mode: every input is empty; EL-105 is masked; EL-108 shows nothing; EL-114 is not shown.
  - Initial in edit mode: every input shows the stored field content; EL-105 is masked; EL-108 shows the stored Fingerprint.
  - Revealed: EL-105 is unmasked.
  - Loading: in edit mode, inputs are disabled while the Item is being retrieved.
  - Error: a validation error or persistence error is shown in EL-118; entered input content is kept.
  - No Project: no Project exists; EL-102 has no options; EL-117 is shown.
  - Disabled: EL-107 is disabled while EL-105 is empty.
- Navigation:
  - activate EL-112 and the Item is stored -> SCR-001
  - activate EL-113 -> SCR-001 without storing changes
  - activate EL-115 and the Item is deleted -> SCR-001
  - activate EL-116 -> remain on SCR-002
  - activate EL-117 -> SCR-003
- Interactions:
  - open SCR-002 -> EL-105 is masked
  - activate EL-106 -> EL-105 changes between masked and unmasked
  - activate EL-107 -> the Clipboard receives the content of EL-105 per FR-027, FR-028, FR-029; EL-118 shows the confirmation of FR-031
  - activate EL-112 with invalid input -> the matching ERR is shown
  - activate EL-114 -> EL-115 and EL-116 are shown
  - activate EL-115 -> the Item is deleted per FR-011

### SCR-003

- ID: SCR-003
- Purpose: create, rename, and delete Projects.
- Arrangement:
  - EL-201 and EL-202 are grouped at the top.
  - EL-203 is placed below EL-201 and EL-202.
  - EL-204, EL-205, EL-206 are placed inside each row of EL-203.
  - EL-209 is placed above EL-201.
  - EL-211 is placed above EL-203.
- Elements:
  - EL-201: new Project Name input; purpose: enter the Project Name of a new Project; user actions: type text.
  - EL-202: create control; purpose: store the new Project; user actions: activate.
  - EL-203: Project row list; purpose: show every Project; user actions: none.
  - EL-204: row Project Name input; purpose: edit the Project Name; user actions: type text.
  - EL-205: row rename control; purpose: store the edited Project Name; user actions: activate.
  - EL-206: row delete control; purpose: start deleting the Project; user actions: activate.
  - EL-207: delete confirm control; purpose: confirm deletion; user actions: activate; shown after EL-206 is activated.
  - EL-208: delete cancel control; purpose: abort deletion; user actions: activate; shown after EL-206 is activated.
  - EL-209: back control; purpose: return to the Item list; user actions: activate.
  - EL-210: no-Projects message slot; purpose: indicate that no Project exists; user actions: none.
  - EL-211: Message Slot; purpose: show validation errors and persistence errors; user actions: none.
- States:
  - Initial: EL-201 is empty; EL-203 shows every Project.
  - Empty: no Project exists; EL-210 is shown; EL-203 is not shown.
  - Populated: EL-203 shows one row per Project.
  - Loading: none.
  - Error: a validation error or persistence error is shown in EL-211.
  - Disabled: none.
- Navigation:
  - activate EL-209 -> SCR-001
- Interactions:
  - activate EL-202 with a non-empty EL-201 -> the Project is created per FR-001
  - activate EL-205 with a non-empty EL-204 -> the Project is renamed per FR-003
  - activate EL-206 -> EL-207 and EL-208 are shown for that row
  - activate EL-207 -> the Project is deleted per FR-005 and FR-006
  - activate EL-208 -> no deletion occurs per FR-007

## Functional Requirements [UNVERIFIED]

Projects:

- FR-001: The product MUST create a Project when the user enters a Project Name in EL-201 and activates EL-202.
- FR-002: A created Project MUST appear in EL-203, EL-002, and EL-102.
- FR-003: The product MUST change the Project Name of a Project when the user edits EL-204 and activates EL-205.
- FR-004: A renamed Project MUST keep every Item associated with it.
- FR-005: The product MUST delete a Project when the user activates EL-206 and then EL-207.
- FR-006: The product MUST NOT delete a Project that has at least one Item.
- FR-007: The product MUST NOT delete a Project when the user activates EL-208.

Items:

- FR-008: The product MUST create an Item when the user activates EL-112 in create mode with valid input.
- FR-009: Each Item MUST belong to exactly one Project.
- FR-010: The product MUST update the Item when the user activates EL-112 in edit mode with valid input.
- FR-011: The product MUST delete an Item when the user activates EL-114 and then EL-115.
- FR-012: The product MUST NOT delete an Item when the user activates EL-116.
- FR-013: Projects and Items MUST remain Persisted until the user deletes them.
- FR-014: The product MUST store a Value exactly as entered, including leading and trailing whitespace.

Fingerprint:

- FR-015: The Fingerprint of an Item MUST be the last 4 characters of the Value after leading and trailing whitespace is removed.
- FR-016: The Fingerprint MUST be empty when the Value after leading and trailing whitespace is removed has fewer than 8 characters.
- FR-017: The product MUST recompute the Fingerprint each time the Value is stored.

List:

- FR-018: SCR-001 MUST show one row in EL-004 for each stored Item that satisfies the search text, the Project filter selection, and the Environment filter selection.
- FR-019: Each row MUST show Item Name, Service, Project Name, Environment, and Fingerprint.
- FR-020: Rows MUST be ordered by Project Name ascending, then by Item Name ascending, with case-insensitive comparison.

Search and filters:

- FR-021: A row MUST satisfy the search text when the search text is contained, case-insensitively, in at least one of: Item Name, Service, Project Name, Environment, Usage Location, Memo.
- FR-022: Search MUST NOT match against the Value.
- FR-023: When a Project is selected in EL-002, only Items of that Project MUST be shown.
- FR-024: When an Environment value is selected in EL-003, only Items whose Environment equals that value exactly MUST be shown.
- FR-025: The options of EL-003 MUST be the distinct non-empty Environment values of stored Items, ordered ascending with case-insensitive comparison.
- FR-026: Search text, Project filter selection, and Environment filter selection MUST combine with logical AND.

Copy:

- FR-027: The Clipboard MUST receive the Value with leading and trailing whitespace removed.
- FR-028: The Clipboard text MUST preserve every character between the first and last non-whitespace characters of the Value, including line breaks.
- FR-029: Activating EL-107 MUST copy the current content of EL-105, including unsaved edits.
- FR-030: Copying MUST NOT change the stored Value.
- FR-031: After a successful copy, a Message Slot MUST show a confirmation.

Value display:

- FR-032: EL-105 MUST be masked each time SCR-002 is opened.
- FR-033: Activating EL-106 MUST switch EL-105 between masked and unmasked.
- FR-034: The product MUST NOT display a Value on any screen except in EL-105 while EL-105 is unmasked.

## User Flows [UNVERIFIED]

- UF-001: Find and copy a Value
  - open SCR-001 -> EL-004 shows Items per FR-018
  - type text in EL-001 -> EL-004 shows matching rows per FR-021
  - activate EL-005 on a row -> the Clipboard receives the Value per FR-027; EL-010 shows the confirmation per FR-031
- UF-002: Identify a Value by Fingerprint
  - open SCR-001 -> each row shows the Fingerprint per FR-019
  - read the Fingerprint of a row -> the user compares it outside the product
- UF-003: Add an Item
  - activate EL-007 on SCR-001 and create a Project per UF-006 when no Project exists
  - activate EL-006 on SCR-001 -> SCR-002 in create mode
  - fill EL-101, EL-102, EL-105 -> inputs are filled
  - activate EL-112 -> the Item is created per FR-008; SCR-001 is shown
- UF-004: Edit an Item
  - activate a row on SCR-001 -> SCR-002 in edit mode
  - change inputs -> inputs show the changes
  - activate EL-112 -> the Item is updated per FR-010; SCR-001 is shown
- UF-005: Delete an Item
  - activate a row on SCR-001 -> SCR-002 in edit mode
  - activate EL-114 -> EL-115 and EL-116 are shown
  - activate EL-115 -> the Item is deleted per FR-011; SCR-001 is shown
- UF-006: Manage Projects
  - activate EL-007 on SCR-001 -> SCR-003
  - enter a Project Name in EL-201 and activate EL-202 -> the Project is created per FR-001
  - edit EL-204 and activate EL-205 -> the Project is renamed per FR-003
  - activate EL-206 then EL-207 -> the Project is deleted per FR-005 when no Item belongs to it

## Error Conditions [UNVERIFIED]

- ERR-001: Item Name is empty when EL-112 is activated.
  - The product MUST NOT store the Item.
  - EL-118 MUST show a validation error that refers to EL-101.
  - The product MUST remain on SCR-002.
- ERR-002: Value is empty or contains only whitespace when EL-112 is activated.
  - The product MUST NOT store the Item.
  - EL-118 MUST show a validation error that refers to EL-105.
  - The product MUST remain on SCR-002.
- ERR-003: No Project is selected in EL-102 when EL-112 is activated.
  - The product MUST NOT store the Item.
  - EL-118 MUST show a validation error that refers to EL-102.
  - The product MUST remain on SCR-002.
- ERR-004: Project Name is empty or contains only whitespace when EL-202 or EL-205 is activated.
  - The product MUST NOT create or rename the Project.
  - EL-211 MUST show a validation error.
- ERR-005: EL-207 is activated for a Project that has at least one Item.
  - The product MUST NOT delete the Project.
  - EL-211 MUST show an error that states the Project has Items.
- ERR-006: Writing to the Clipboard fails.
  - EL-010 or EL-118 MUST show a copy error.
  - The product MUST NOT show the confirmation of FR-031.
- ERR-007: Storing, updating, renaming, or deleting fails.
  - The product MUST show a persistence error in the Message Slot of the current screen.
  - The product MUST NOT show the action as completed.
  - Entered input content MUST remain on screen.
- ERR-008: Items cannot be retrieved on SCR-001.
  - EL-010 MUST show a retrieval error.
  - EL-004 MUST NOT be shown.

## Non-Goals

- Integration with continuous integration or deployment systems.
- Injection of Values into development environments.
- Sharing Items between users or teams.
- Synchronization across devices or platforms.
- Access from a phone or a tablet.
- Deployment to any host other than the user's own computer.

## Acceptance Criteria [UNVERIFIED]

- AC-001: Creating a Project named alpha on SCR-003 results in a row for alpha in EL-203, an option in EL-002, and an option in EL-102. (FR-001, FR-002, SCR-003)
- AC-002: Renaming the Project alpha to beta results in EL-203 showing beta and every Item of alpha showing Project Name beta on SCR-001. (FR-003, FR-004)
- AC-003: Deleting a Project with no Item through EL-206 and EL-207 removes it from EL-203. (FR-005)
- AC-004: Activating EL-207 for a Project with at least one Item leaves the Project in EL-203 and shows the error of ERR-005. (FR-006, ERR-005)
- AC-005: Activating EL-208 leaves the Project in EL-203. (FR-007)
- AC-006: Saving an Item with Item Name, Project, and Value creates a row on SCR-001. (FR-008, FR-009, SCR-002)
- AC-007: Stopping and starting the product leaves every stored Project and Item present. (FR-013)
- AC-008: Saving with an empty Item Name shows the error of ERR-001 and stores nothing. (ERR-001)
- AC-009: Saving with a whitespace-only Value shows the error of ERR-002 and stores nothing. (ERR-002)
- AC-010: Saving without a selected Project shows the error of ERR-003 and stores nothing. (ERR-003)
- AC-011: Creating a Project with an empty Project Name shows the error of ERR-004 and stores nothing. (ERR-004)
- AC-012: A stored Value with leading spaces is shown with the same leading spaces in EL-105 after unmasking. (FR-014)
- AC-013: A Value of abcdefgh1234 produces the Fingerprint 1234. (FR-015)
- AC-014: A Value of abcdefg produces an empty Fingerprint. (FR-016)
- AC-015: Changing the Value from abcdefgh1234 to abcdefgh5678 changes the Fingerprint from 1234 to 5678. (FR-017)
- AC-016: Each row on SCR-001 shows Item Name, Service, Project Name, Environment, and Fingerprint. (FR-019)
- AC-017: No row on SCR-001 shows the Value. (FR-034)
- AC-018: Items of Projects b and a with Item Names z and y are ordered a/y, a/z, b/y, b/z. (FR-020)
- AC-019: Search text matching only the Memo of one Item shows only that Item. (FR-021)
- AC-020: Search text equal to a stored Value and matching no other field shows no rows and shows EL-009. (FR-022)
- AC-021: Selecting a Project in EL-002 shows only Items of that Project. (FR-023)
- AC-022: Selecting an Environment value in EL-003 shows only Items with exactly that Environment. (FR-024)
- AC-023: EL-003 lists each distinct non-empty Environment value once. (FR-025)
- AC-024: Search text together with a Project filter selection shows only rows satisfying both. (FR-026)
- AC-025: Activating EL-005 on a row whose Value is " abc " leaves the Clipboard text equal to abc. (FR-027)
- AC-026: Activating EL-005 on a row whose Value has three lines leaves the Clipboard text with the same three lines and the same line breaks. (FR-028)
- AC-027: Activating EL-107 after editing EL-105 without saving leaves the Clipboard text equal to the edited content. (FR-029)
- AC-028: After copying, the stored Value is unchanged. (FR-030)
- AC-029: After a successful copy, a Message Slot shows a confirmation. (FR-031, EL-010, EL-118)
- AC-030: When the Clipboard write fails, a copy error is shown and the confirmation is not shown. (ERR-006)
- AC-031: Opening SCR-002 for any Item shows EL-105 masked. (FR-032)
- AC-032: Activating EL-106 twice returns EL-105 to masked. (FR-033)
- AC-033: Deleting an Item through EL-114 and EL-115 removes its row from SCR-001. (FR-011)
- AC-034: Activating EL-116 leaves the Item on SCR-001. (FR-012)
- AC-035: With no stored Item, SCR-001 shows EL-008 and hides EL-004. (SCR-001)
- AC-036: With no Project, SCR-003 shows EL-210. (SCR-003)
- AC-037: With no Project, SCR-002 shows EL-117 and EL-102 has no options. (SCR-002)
- AC-038: Opening the Persisted data shows no Value as readable plain text. (Constraints)
- AC-039: A request from a computer other than the one running the product does not reach the product. (Constraints)
