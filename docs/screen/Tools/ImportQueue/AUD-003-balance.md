# AUD-003 — Tools / Import Queue / Balance

## Header
- **Screen:** Balance Import
- **Objective:** allow users to upload a balance file for asynchronous processing through the system queue (Laravel Job).
- **Access Profiles:** (to be defined)
- **Module:** Audit / Imports

---

## Screen Fields
1. **Company**
    - Type: select
    - Source: **active** companies in the system
    - Required: **Yes**

2. **Reference month**
    - Type: select
    - Required: **Yes**

3. **Reference year**
    - Type: select
    - Required: **Yes**
    - Rule: list **current year** and **previous year** (for retroactive purposes)

4. **Selected excel file**
    - Type: file upload
    - Required: **Yes**
    - Allowed extensions: `.xls`, `.xlsx`, `.csv`
    - Maximum size: **10MB**

---

## Flow
### Entry
- User accesses the **Balance Import** screen

### User Steps
1. Select an **active company**
2. Select the **reference month**
3. Select the **reference year** (current or previous year)
4. Select the **Excel/CSV file** (up to 10MB)
5. Click **Send / Import**

### Output
- The system registers the import request and **dispatches it to the queue** for execution by an **internal Job**
- The system returns feedback to the user: **"File sent."**

---

## Business Rules
### AUD-003-RN-01 — Listed companies
**Description:** the Company field must list only **active companies**.  
**Condition:** `company.status = true`  
**Exceptions:** —

### AUD-003-RN-02 — Allowed reference year
**Description:** the Reference year field must allow only the **current year** and the **previous year**.  
**Condition:** `{Y, Y-1}`  
**Exceptions:** if the business requires additional retroactive years, this rule must be updated.

### AUD-003-RN-03 — Mandatory file and size limit
**Description:** file upload is mandatory and must respect the **10MB** size limit.  
**Conditions:** file is present and `size <= 10MB`.  
**Exceptions:** —  
**Suggested message:** “The file must be an Excel or CSV file up to 10MB.”

### AUD-003-RN-04 — Duplicate submission prevention per user/company/reference/file
**Description:** a user **must not submit more than once** the **same file** for the **same company** and the **same reference month/year**, as long as both records are active.

**Logical uniqueness key:**
- `user_id`
- `company_id`
- `reference_month`
- `reference_year`
- `file_name`

**Exceptions:**
- If the user changes the **company** or the **reference month/year**, the submission is allowed (the same file may be reused for a different reference, if business rules allow it).
- If the file is different, even with the same reference, the submission is allowed (if business rules allow it).

---

## Validations
- Company: required; must exist and be active
- Reference month: required; valid month (1–12)
- Reference year: required; only current and previous year allowed
- Selected excel file: required; allowed extension; size ≤ 10MB

---

## Processing / Queue (Job)
- **Action:** upon submission, create an import record and dispatch a Job to the queue
- **Idempotency:** the Job should be able to run safely without duplicating results if retried (recommended)
- **Suggested statuses:**
    - `In Queue`
    - `Processing`
    - `Processed`
    - `Error`
    - `Cancelled`

---

## Audit & Logs
- Register event: `BALANCE_IMPORTED_REQUESTED`
- Minimum fields:
    - `user_id`
    - `company_id`
    - `reference_month`
    - `reference_year`
    - `file_fingerprint`
    - `timestamp`
- Also log Job errors (if any) and failure reasons.

---

## Acceptance Criteria (QA)
- **AC-01:** Given an inactive company, when opening the Company select, then it must not be listed.
- **AC-02:** Given the current year, when opening Reference year, then only `{current year, previous year}` must be listed.
- **AC-03:** Given a file larger than 10MB, when submitting, then the system must block the action and show a size limit message.
- **AC-04:** Given that the user has already submitted the same file for the same company and the same reference month/year, when submitting again, then the system must block the submission with a duplication message.
- **AC-05:** Given that the user changes the reference month/year or the company, when submitting the same file, then the system must allow the submission (if this behavior is intended).

---

## Changelog
- **2025-12-18:** page created — AndreGregoletto
- **2026-01-14:** page updated — AndreGregoletto
