# AUD-041 — Settings / Company / New

## Header
- **Screen:** Settings / Company / New
- **Objective:** allow authorized users to create a new company record in the system.
- **Access Profiles:** Authorized users with company management permissions
- **Module:** Settings / Company

---

## Overview
This screen provides the **company creation form**.

It allows users to register a new company by entering its core identification data.  
Some fields have **default values**, and **uniqueness constraints** are enforced to prevent duplicated records.

---

## Form Fields

1. **Name**
    - Description: legal company name.
    - Type: text
    - Required: **Yes**
    - Must be unique.

2. **Commercial Name**
    - Description: commercial/trade name of the company.
    - Type: text
    - Required: No
    - Optional and configurable.

3. **CNPJ**
    - Description: official company tax identifier.
    - Type: text
    - Required: **Yes**
    - Must be unique.

4. **Publicity Trade**
    - Description: indicates whether the company is publicly traded.
    - Type: checkbox
    - Default value: **Disabled (false)**

5. **Status**
    - Description: system-level status of the company.
    - Type: checkbox
    - Default value: **Enabled (Active)**

---

## Default Values
- **Status:** Active (checked by default)
- **Publicity Trade:** Inactive (unchecked by default)

These defaults may be changed by the user before saving.

---

## Actions

### Save
**Description:** persists the new company record.  
**Behavior:**
- Validates required and unique fields.
- Saves the company if validation passes.
- Redirects the user back to the company consultation screen.

---

### Cancel
**Description:** aborts the creation process.  
**Behavior:**
- Discards all entered data.
- Redirects the user back to the company consultation screen.

---

## Flow
### Entry
- User navigates from **Settings / Company / Consult** via:
    - **New**

### User Steps
1. Fill in required fields.
2. Optionally fill in commercial name.
3. Adjust default flags if needed.
4. Click **Save** or **Cancel**.

### Output
- On success: new company is created and available in the consultation list.
- On cancel: no data is persisted.

---

## Business Rules

### AUD-041-RN-01 — Required fields
**Description:** Name and CNPJ are mandatory.  
**Condition:** both fields must be filled before saving.  
**Exceptions:** —

---

### AUD-041-RN-02 — Uniqueness constraints
**Description:** Name and CNPJ must not be duplicated.  
**Condition:**
- No existing company may have the same **Name**.
- No existing company may have the same **CNPJ**.

**Exceptions:** —

---

### AUD-041-RN-03 — Default status
**Description:** newly created companies must be active by default.  
**Condition:** Status = Active unless explicitly changed by the user.  
**Exceptions:** —

---

### AUD-041-RN-04 — Default publicity trade
**Description:** newly created companies must not be marked as publicly traded by default.  
**Condition:** Publicity Trade = false unless explicitly enabled by the user.  
**Exceptions:** —

---

### AUD-041-RN-05 — Configuration flexibility
**Description:** all default values may be overridden at creation time.  
**Condition:** user may toggle Status and Publicity Trade before saving.  
**Exceptions:** permission restrictions may apply.

---

## Validations & Messages
- **Missing Name:** “Company name is required.”
- **Missing CNPJ:** “CNPJ is required.”
- **Duplicate Name:** “A company with this name already exists.”
- **Duplicate CNPJ:** “A company with this CNPJ already exists.”

---

## Audit & Logs
- Recommended audit event: `COMPANY_CREATED`
- Minimum fields:
    - `user_id`
    - `company_id`
    - `timestamp`
    - `action`

---

## Acceptance Criteria (QA)
- **AC-01:** Given empty Name or CNPJ, when saving, then the system must block the action.
- **AC-02:** Given a duplicated Name, when saving, then the system must display a duplication error.
- **AC-03:** Given a duplicated CNPJ, when saving, then the system must display a duplication error.
- **AC-04:** Given no user interaction with defaults, when opening the screen, then Status must be Active and Publicity Trade must be Inactive.
- **AC-05:** Given modified default flags, when saving, then the system must persist the chosen values.
- **AC-06:** Given the Cancel action, when clicked, then no company must be created.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
