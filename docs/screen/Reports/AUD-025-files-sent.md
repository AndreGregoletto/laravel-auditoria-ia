# AUD-025 — Reports / Files / Sent

## Header
- **Screen:** Reports / Files / Sent
- **Objective:** provide a comprehensive report of all files sent to the system, with advanced filtering and full processing visibility.
- **Access Profiles:** Authorized users with reporting and audit permissions
- **Module:** Audit / Reports / Files

---

## Overview
This screen provides a **global view of file submissions** across the system.

It lists all submitted files **except cancelled ones**, allowing users to:
- track processing status,
- filter records by multiple criteria,
- access generated outputs when available.

This screen is intended for **audit, monitoring, and operational analysis**.

---

## Excluded Records
- Files with `FileStep = Cancelled` **must not be listed**
- Cancelled files must also be **excluded from FileStep filter options**

This exclusion is **intentional and permanent** for this screen.

---

## Filter Section
Each visible column has a corresponding filter.

### Available Filters
1. **File Name**
    - Type: text
    - Behavior: partial match

2. **User**
    - Type: text
    - Behavior: partial match (user name)

3. **Company**
    - Type: text
    - Behavior: partial match (company name)

4. **Destination Service**
    - Type: select
    - Values: available destination services (e.g., Balance)

5. **File Step**
    - Type: select
    - Values: all FileSteps **except `Cancelled`**

6. **File States**
    - Type: select
    - Values: system-level states (e.g., Active, Inactive)

7. **Reference Month**
    - Type: numeric select or input
    - Values: 1–12

8. **Reference Year**
    - Type: numeric select or input

9. **Extension**
    - Type: select or text
    - Values: file extensions (e.g., XLSX, CSV)

### Clear Filters
- **Clear filters** resets all filters and reloads the default dataset.

---

## Table Columns
1. **Destination Service**
    - Target processing service.

2. **File Name**
    - Original uploaded file name.

3. **User**
    - User who submitted the file.

4. **Company**
    - Company selected at submission time.

5. **Reference Month**
    - Month associated with the submission.

6. **Reference Year**
    - Year associated with the submission.

7. **Extension**
    - File extension.

8. **File Size**
    - Size of the uploaded file.

9. **File Step**
    - Current processing stage of the file.

10. **File States**
    - System-level usability state.

11. **Send In**
    - Timestamp when the file was submitted.

12. **Updated In**
    - Timestamp of the last status update.

13. **Actions**
    - Available actions based on FileStep and FileStates.

---

## Actions

### Download
**Description:** allows downloading the file or generated output.  
**Visibility Rule:**
- Displayed when a downloadable artifact exists.
- May be available even if the file is still in processing, depending on system rules.

**Exceptions:**
- If no downloadable file exists, the action must not be displayed.

---

## Business Rules

### AUD-025-RN-01 — Global visibility
**Description:** the screen must list files submitted by all users, respecting access permissions.  
**Condition:** data is not scoped to a single user.  
**Exceptions:** role-based restrictions may apply.

---

### AUD-025-RN-02 — Cancelled exclusion
**Description:** cancelled files must never appear in this screen.  
**Condition:** `FileStep != Cancelled`  
**Applies to:**
- table results
- FileStep filter options

---

### AUD-025-RN-03 — Column-based filtering
**Description:** each filter must affect only its corresponding column.  
**Condition:** filters are cumulative and combinable.  
**Exceptions:** —

---

### AUD-025-RN-04 — FileStep and FileStates consistency
**Description:** FileStep and FileStates must reflect the current lifecycle and system usability.  
**Condition:** mapping must follow the **File Lifecycle Reference**.  
**Reference:** `AUD-file-lifecycle.md`

---

### AUD-025-RN-05 — Read-only report
**Description:** this screen is strictly read-only.  
**Condition:** no create, edit, cancel, or delete actions are allowed.  
**Exceptions:** —

---

## Flow
### Entry
- User accesses **Reports / Files / Sent**.

### User Steps
1. View the list of sent files.
2. Apply one or more filters.
3. Inspect processing and system statuses.
4. Download available artifacts.

### Output
- Filtered, paginated list of file records.

---

## Performance Considerations
- Filters should be applied server-side.
- Queries should be indexed on:
    - file name
    - user
    - company
    - reference month/year
    - FileStep
    - FileStates
- Pagination is mandatory for large datasets.

---

## Audit & Logs
- No audit events are required for read-only listing.
- Optional: log download actions for traceability.

---

## Acceptance Criteria (QA)
- **AC-01:** Given a cancelled file, when opening the screen, then it must not be listed.
- **AC-02:** Given the FileStep filter, when opening its options, then `Cancelled` must not be available.
- **AC-03:** Given a filter applied, when combined with others, then results must reflect all criteria.
- **AC-04:** Given a matching record, when filters are applied, then the record must appear in the list.
- **AC-05:** Given no matching records, when filters are applied, then the table must show an empty state.
- **AC-06:** Given a record with downloadable output, when viewing Actions, then **Download** must be available.
- **AC-07:** Given a record without downloadable output, when viewing Actions, then **Download** must not be displayed.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
