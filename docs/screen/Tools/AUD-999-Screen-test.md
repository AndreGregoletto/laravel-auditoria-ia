# AUD-003 — Tools / Import Queue / Balance (Updated)

## Header
- **Screen:** Balance Import  
- **Objective:** allow users to upload a balance file for asynchronous processing through the system queue (Laravel Job).  
- **Access Profiles:** (to be defined)  
- **Module:** Audit / Imports  

---

## Screen Fields

1. **User**
   - Type: select  
   - Source: **active users** in the system  
   - Required: **Yes**  
   - Purpose: link the import request to a specific responsible user  

2. **Company**
   - Type: select  
   - Source: **active** companies in the system  
   - Required: **Yes**

3. **Reference month**
   - Type: select  
   - Required: **Yes**

4. **Reference year**
   - Type: select  
   - Required: **Yes**  
   - Rule: list **current year** and **previous year** (for retroactive purposes)

5. **Selected excel file**
   - Type: file upload  
   - Required: **Yes**  
   - Allowed extensions: `.xls`, `.xlsx`, `.csv`  
   - Maximum size: **10MB**

---

## Flow

### Entry
- User accesses the **Balance Import** screen

### User Steps
1. Select an **active user**  
2. Select an **active company**  
3. Select the **reference month**  
4. Select the **reference year** (current or previous year)  
5. Select the **Excel/CSV file** (up to 10MB)  
6. Click **Send / Import**

### Output
- The system registers the import request and **dispatches it to the queue** for execution by an **internal Job**
- The system returns feedback to the user: **"File sent."**

---

## Business Rules

### AUD-003-RN-01 — Listed users
**Description:** the User field must list only **active users**.  
**Condition:** `user.status = true`  
**Exceptions:** —  

### AUD-003-RN-02 — Listed companies
**Description:** the Company field must list only **active companies**.  
**Condition:** `company.status = true`  
**Exceptions:** —  

### AUD-003-RN-03 — Allowed reference year
**Description:** the Reference year field must allow only the **current year** and the **previous year**.  
**Condition:** `{Y, Y-1}`  

### AUD-003-RN-04 — Mandatory file and size limit
**Description:** file upload is mandatory and must respect the **10MB** size limit.  
**Suggested message:** “The file must be an Excel or CSV file up to 10MB.”

### AUD-003-RN-05 — Duplicate submission prevention per user/company/reference/file
**Logical uniqueness key:**
- `linked_user_id`
- `company_id`
- `reference_month`
- `reference_year`
- `file_name`

---

## Validations
- User: required; must exist and be active  
- Company: required; must exist and be active  
- Reference month: required; valid month (1–12)  
- Reference year: required; only current and previous year allowed  
- Selected excel file: required; allowed extension; size ≤ 10MB  

---

## Processing / Queue (Job)
- Create import record and dispatch Job to queue  
- Suggested statuses:
  - `In Queue`
  - `Processing`
  - `Processed`
  - `Error`
  - `Cancelled`

---

## Audit & Logs
- Event: `BALANCE_IMPORTED_REQUESTED`

### Minimum fields:
- `requester_user_id`
- `linked_user_id`
- `company_id`
- `reference_month`
- `reference_year`
- `file_fingerprint`
- `timestamp`

---

## Acceptance Criteria (QA)
- **AC-01:** inactive user must not be listed  
- **AC-02:** inactive company must not be listed  
- **AC-03:** only current and previous year allowed  
- **AC-04:** file >10MB blocked  
- **AC-05:** duplicate submission blocked  
- **AC-06:** changing reference/company allows submission  

---

## Changelog
- **2025-12-18:** page created — AndreGregoletto  
- **2026-01-14:** page updated — AndreGregoletto  
- **2026-02-03:** added User selection field — Friday  
