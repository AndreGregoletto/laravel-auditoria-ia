# AUD-004 — Tools / Processes / Generate RAG

## Header
- **Screen:** Tools / Processes / Generate RAG
- **Objective:** generate a RAG (Retrieval-Augmented Generation) dataset from previously processed files, based on strict filtering and controlled file selection.
- **Access Profiles:** Authorized users with processing permissions
- **Module:** Tools / Processes

---

## Overview
This screen allows users to **generate a RAG dataset** by selecting multiple previously uploaded and processed files.

Due to the **critical nature** of this operation, the screen enforces:
- mandatory filters,
- controlled file selection,
- automatic ordering,
- real-time processing without background execution.

The user must remain on the screen until the process finishes.

---

## Mandatory Filters
Fields marked with `*` are **required** before listing available files.

### Required Filters
1. **Company \***
    - Type: select
    - Description: company to which the files belong.

2. **Reference start \***
    - Type: month + year
    - Description: starting reference period for file selection.

3. **Reference end \***
    - Type: month + year
    - Description: ending reference period for file selection.

---

## Optional Filters

4. **Only my files**
    - Type: checkbox
    - Default: enabled
    - Description: limits file results to those uploaded by the current user.

---

## Available Files Section
### Behavior
- Files are listed only after all **mandatory filters** are filled.
- Files displayed must match:
    - selected company,
    - reference period (start–end),
    - user scope (if enabled),
    - eligible processing status.

### Empty Result
If no files are found using the current filters, the system must display:

> **“No files were found using the current filters.”**

---

## Selected Files Section
### Selection Rules
- Files can be added individually from the **Available files** list.
- Files can be removed:
    - individually, using the **Remove** action,
    - or all at once using the **Clear** button.

---

## Selection Constraints

### Minimum Selection Requirement
**At least 2 files must be selected** to enable the **Generate RAG** button.

- With fewer than 2 files:
    - the button remains disabled.
- There is **no maximum limit** as long as the minimum is met.

---

### Automatic Ordering
**Description:** selected files are automatically ordered internally.  
**Rule:** ordering is always done by:
1. reference year
2. reference month

> The visual selection order does not affect processing order.

---

### Filter Change Reset
**Description:** changing any filter after file selection must reset the selection.  
**Rule:**
- If **any filter value changes**, the **Selected files** list is automatically cleared.
- This prevents unintended cross-dataset or cross-period processing.

---

### Clear Button Behavior
- **Clear** removes **all selected files**.
- It does **not** reset filters.
- Individual removal is also supported per file.

---

## Generate RAG Action

### Generate RAG
**Description:** starts the RAG generation process.

**Preconditions:**
- All mandatory filters filled.
- Minimum of **2 files selected**.

**Behavior:**
- The process runs **in real time** (synchronous).
- The user must **wait on the screen** until completion.
- UI must indicate that processing is ongoing.
- No background execution is used for this operation.

**Rationale:**
This is a **critical step** for downstream analysis and decision-making.  
The user must be aware of completion before continuing work.

---

## Flow
### Entry
- User accesses **Tools / Processes / Generate RAG**.

### User Steps
1. Select mandatory filters.
2. Review available files.
3. Add at least two files to selection.
4. Click **Generate RAG**.
5. Wait for the process to complete.

### Output
- RAG dataset is generated and made available for subsequent processes.
- User remains on the screen until completion.

---

## Business Rules

### AUD-004-RN-01 — Mandatory filters enforcement
**Description:** files must not be listed unless all mandatory filters are filled.  
**Exceptions:** —

---

### AUD-004-RN-02 — Minimum file selection
**Description:** RAG generation requires at least two files.  
**Condition:** `selected_files.count >= 2`  
**Exceptions:** —

---

### AUD-004-RN-03 — Automatic ordering
**Description:** file processing order must be deterministic.  
**Condition:** always sorted by reference year/month internally.  
**Exceptions:** —

---

### AUD-004-RN-04 — Filter change protection
**Description:** changing filters invalidates current file selection.  
**Condition:** selected files must be cleared on any filter change.  
**Exceptions:** —

---

### AUD-004-RN-05 — Real-time processing
**Description:** RAG generation must run synchronously.  
**Condition:** user must wait for completion.  
**Exceptions:** future architectural changes may introduce async execution.

---

## Validations & Messages
- **Missing mandatory filters:** “Please fill in all required filters.”
- **No files found:** “No files were found using the current filters.”
- **Insufficient files selected:** “Select at least two files to generate RAG.”
- **Processing in progress:** visual loading/processing indicator must be shown.

---

## Audit & Logs
- Recommended audit event: `RAG_GENERATION_STARTED`
- Recommended audit event: `RAG_GENERATION_COMPLETED`
- Minimum fields:
    - `user_id`
    - `company_id`
    - `file_ids`
    - `timestamp`
    - `duration`

---

## Acceptance Criteria (QA)
- **AC-01:** Given missing mandatory filters, when opening the screen, then files must not be listed.
- **AC-02:** Given valid filters with no matching files, when searching, then the “no files found” message must be displayed.
- **AC-03:** Given fewer than two selected files, when attempting generation, then the Generate RAG button must remain disabled.
- **AC-04:** Given two or more selected files, when clicking Generate RAG, then processing must start.
- **AC-05:** Given selected files out of order, when processing starts, then files must be ordered internally by reference month/year.
- **AC-06:** Given a filter change after selection, when the filter is updated, then the selected files list must be cleared.
- **AC-07:** Given the Clear button, when clicked, then only selected files must be cleared.
- **AC-08:** Given the Generate RAG action, when processing starts, then the user must remain on the screen until completion.

---

## Changelog
- **2026-01-09:** page created — AndreGregoletto
- **2026-02-02:** Create BP && Covert to Download — AndreGregoletto
