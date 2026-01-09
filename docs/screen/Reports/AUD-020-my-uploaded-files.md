# AUD-020 — Reports / My Uploaded Files

## Header
- **Screen:** Reports / My Uploaded Files
- **Objective:** list all files previously submitted by the current user, regardless of processing status, and provide context-aware actions (e.g., Cancel, Download).
- **Access Profiles:** Authenticated users (scope: **own uploads only**)
- **Module:** Audit / Reports / Import Queue

---

## Overview
This screen displays a historical list of uploads created by the current user. It is used to:
- track file processing progress (**FileStep**),
- understand system-level usability of the record (**FileStates**),
- execute allowed actions based on current processing state (**Actions**).

---

## Table Columns
1. **Destination Service**
    - Description: target pipeline/service that will process the file (e.g., `Balance`).

2. **File Name**
    - Description: original file name as uploaded by the user.

3. **Company**
    - Description: company selected at submission time (active at that time, may later change in the system).

4. **FileStep**
    - Description: processing step/state of the pipeline (where the file is in the queue lifecycle).
    - Example values (current): `In Queue`, `Processed`, `Error`
    - Note: values may expand in the future as the pipeline grows.

5. **FileStates**
    - Description: system-level state indicating whether the record/file should be considered usable/valid by the system.
    - Example values (current): `Active`, `Inactive`

6. **Send In**
    - Description: timestamp when the file was submitted.

7. **Updated In**
    - Description: last timestamp when the record changed (status update, cancellation, processing progress).

8. **Actions**
    - Description: available actions based on current business rules and the file state.

---

## Flow
### Entry
- User opens **Reports / My Uploaded Files**.

### User Steps
1. View the list of submitted files.
2. Identify the current processing stage via **FileStep**.
3. Execute an available action (if any).

### Output
- Table reflects current records and statuses.
- If an action is executed (e.g., Cancel), the record is updated and reflected in the list.

---

## Business Rules

### AUD-020-RN-01 — User scope
**Description:** the list must show only files submitted by the **current user**.  
**Condition:** records filtered by `user_id = current_user`.  
**Exceptions:** —

### AUD-020-RN-02 — List all uploads
**Description:** the list must include all uploads from the current user **regardless of FileStep or FileStates**.  
**Condition:** no status filtering is applied by default.  
**Exceptions:** optional filters may exist in the future, but default view remains “all”.

### AUD-020-RN-03 — Cancel action availability
**Description:** the **Cancel** action is allowed only while the file is still pending processing.  
**Condition:** **Cancel is shown/enabled only when `FileStep = "In Queue"`**.  
**Exceptions:** —
- If the record is already being processed or finalized, Cancel must not be available.

**Suggested message on success:** “Upload cancelled.”  
**Suggested message on invalid action attempt:** “This file can no longer be cancelled.”

### AUD-020-RN-04 — Processing lifecycle
**Description:** the pipeline updates **FileStep** as the file progresses.  
**Current lifecycle (minimum):**
- `In Queue` → (processing) → `Processed` (success)
- `In Queue` → (processing) → `Error` (failure)

**Exceptions:** future steps may be introduced (e.g., `Processing`, `Validating`, etc.).

### AUD-020-RN-05 — Meaning of FileStates
**Description:** **FileStates** indicates the system-level usability/validity of the record/file.  
**Rules:**
- `Active` means the system considers the file record usable/valid to reference.
- `Inactive` means the system considers the record not usable (e.g., failed, cancelled, invalidated).

**Note:** the exact mapping between `FileStep` and `FileStates` must remain consistent and should be documented if changed.

### AUD-020-RN-06 — Future download action (final destination)
**Description:** in future iterations, when the final destination of a file produces an output artifact (e.g., Balance data becomes queryable or transformed into a RAG-ready artifact), the user may download the generated output from **Actions**.  
**Condition (future):** show **Download** only when the output exists and access is allowed (typically after `FileStep = "Processed"` and output is generated).  
**Exceptions:** —
- If there is no generated output, Download must not be displayed.

**Suggested label:** “Download”  
**Suggested message if unavailable:** “No output available for download.”

---

## Validations & Display Rules
- The screen must display readable statuses for both `FileStep` and `FileStates`.
- Timestamps must be shown in a consistent format (system standard).
- If values are long (e.g., company name), the UI may truncate, but the full value should be accessible (tooltip or details view), if applicable.

---

## Audit & Logs
- Recommended to log user actions:
    - `UPLOAD_CANCEL_REQUESTED`
    - (future) `UPLOAD_OUTPUT_DOWNLOADED`
- Minimum fields:
    - `user_id`, `upload_id`, `timestamp`, `action`, `ip` (optional)

---

## Acceptance Criteria (QA)
- **AC-01:** Given two users with uploads, when user A opens the screen, then only user A’s uploads are listed.
- **AC-02:** Given uploads in different statuses, when opening the screen, then all uploads are listed regardless of `FileStep`/`FileStates`.
- **AC-03:** Given a record with `FileStep = "In Queue"`, when viewing Actions, then **Cancel** is visible and enabled.
- **AC-04:** Given a record with `FileStep != "In Queue"` (e.g., `Processed` or `Error`), when viewing Actions, then **Cancel** is not available.
- **AC-05:** Given a successful pipeline outcome, when the process ends, then `FileStep` becomes `Processed`.
- **AC-06:** Given a failed pipeline outcome, when the process ends, then `FileStep` becomes `Error` and `FileStates` reflects non-usable state (e.g., `Inactive`), according to system rules.
- **AC-07 (future):** Given an upload that generated a downloadable output artifact, when viewing Actions, then **Download** is available.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
