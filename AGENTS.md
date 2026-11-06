# AGENTS.md

## HOW_WE_WORK

Development is driven by the human's requests and discussion with the agent.
Documents do not drive development.

- The human states what they want, often roughly. The agent asks when the direction is unclear, proposes when there are options, and implements once agreed.
- `.replworks/` documents are notes, not gates:
  - `PRODUCT_SPEC.md`: short brief of what the product is. Updated when the human asks or when a decision is worth recording.
  - `TECH_STACK.md`: stack constraints. Updated only on explicit human approval.
  - `ARCHITECTURE.md`: data relationships. Updated when the schema changes.
  - `TASKS.md`: empty unless the human asks to track something. Never a work queue on its own.
  - `.replworks/docs/`: ignored. Never treated as requirements.
- No document blocks implementation. If a change conflicts with a document, the agent reports it, and the human decides whether to change the code or the document.
- Ambiguity is resolved by asking the human, not by inventing requirements.

---

## COMMUNICATION

- Responses are short and concise.
- Use output text to communicate. All text outside tool use is shown to the human. Never use tools like `shell` or code comments to communicate.
- Focus on facts and problem-solving, no superlatives or emotional validation.
- No emojis unless requested.
- When referencing code, use `file_path:line_number`.

---

## TRUTHFULNESS

- Never generate or guess URLs unless confident they exist and are useful.
- Apply the same rigorous standards to all ideas, including the human's. Disagree when warranted; do not silently comply with a worse approach.
- Evidence before synthesis: inspect the code before stating facts.

---

## VERIFICATION

- Verify through execution whenever reasonable: run code, tests, and sanity checks.
- After behavior changes: `pint`, Larastan, and the full test suite must pass.
- CI must pass before merging.

---

## TOOL_USE

- Prefer dedicated file tools over `shell` for file operations (`read`, `edit`, `write`).
- One-line computations via `shell` with `python3 -c` are fine.
- Reserve `shell` for actual commands, builds, and test runs.

---

## DELIVERY

- Work on a branch, open a PR, squash-merge only after CI passes, then pull main.
- Commit author/committer: the agent that wrote the code (name + noreply email).
- Add a `Co-authored-by` trailer for the human account only when the human specified the change or answered a question about it.
- PR creator / assignee and merger: as confirmed with the human.
- Human-requested changes are committed only after human review of the diff and test results.

---

## IDENTITIES (confirmed)

- Commit author/committer: Muse Spark 1.3 <noreply@meta.com>
- Co-authored-by: Samgu Lee <cable8mm@gmail.com>
- PR creator / assignee: replworks-bot
- Merger: replworks-bot
