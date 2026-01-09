# AUD-042 — Settings / Company / Edit

## Header
- **Screen:** Settings / Company / Edit
- **Objective:** allow authorized users to update an existing company record.
- **Access Profiles:** Authorized users with company management permissions
- **Module:** Settings / Company

---

## Overview
This screen allows users to **edit an existing company** registered in the system.

All editable fields are **pre-filled** with the current company data.  
Updates are validated to preserve **data integrity**, **uniqueness constraints**, and **auditability**.

---

## Form Fields

1. **Name**
    - Description: legal company name.
    - Type: text
    - Required: **Yes**
    - Pre-filled with existing value.
    - Must remain unique across the system.

2. **Commercial Name**
    - Description: commercial/trade name of the company.
    - Type: text
    - Required: No
    - Pre-filled with existing value.

3. **CNPJ**
    - Description: official company tax identifier.
    - Type: text
    - Required: **Yes**
    - Pre-filled with existing value.
    - Must remain unique across the system.

4. **Publicity Trade**
    - Description: indicates whether the company is publicly traded.
    - Type: checkbox
    - Pre-filled with existing value.

5. **Status**
    - Description: system-level status of the company.
    - Type: checkbox
    - Pre-filled with existing value.

---

## Actions

### Save Changes
**Description:** persists updates made to the company record.  
**Behavior:**
- Validates required fields and uniqueness constraints.
- Saves changes if validation passes.
- Redirects the user back to the company consultation screen.

---

### Cancel
**Description:** aborts the edit operation.  
**Behavior:**
- Discards all changes.
- Redirects the user back to the company consultation screen.

---

## Flow
### Entry
- User navigates from **Settings / Company / Consult** via:
    - **Edit** action on a company row.

### User Steps
1. Review pre-filled company data.
2. Modify allowed fields.
3. Click **Save Changes** or **Cancel**.

### Output
- On success: updated company data is persisted and visible in consultation.
- On cancel: no data is changed.

---

## Business Rules

### AUD-042-RN-01 — Required fields
**Description:** Name and CNPJ must always be present.  
**Condition:** fields cannot be empty on save.  
**Exceptions:** —

---

### AUD-042-RN-02 — Conditional uniqueness
**Description:** Name and CNPJ must remain unique across companies.  
**Condition:**
- The current company record is excluded from uniqueness checks.
- No other company may share the same Name or CNPJ.

**Exceptions:** —

---

### AUD-042-RN-03 — Editable fields
**Description:** only company identification and status fields are editable.  
**Condition:** Name, Commercial Name, CNPJ, Publicity Trade, and Status are editable.  
**Exceptions:** future fields may be added.

---

### AUD-042-RN-04 — Status change impact
**Description:** changing the company status may affect visibility in reports and trees.  
**Condition:** inactive companies may no longer appear in report screens.  
**Exceptions:** historical data must remain accessible where applicable.

---

### AUD-042-RN-05 — Configuration flexibility
**Description:** all editable fields may be updated at any time.  
**Condition:** user may toggle Status and Publicity Trade freely.  
**Exceptions:** permission-based restrictions may apply.

---

## Validations & Messages
- **Missing Name:** “Company name is required.”
- **Missing CNPJ:** “CNPJ is required.”
- **Duplicate Name:** “Another company with this name already exists.”
- **Duplicate CNPJ:** “Another company with this CNPJ already exists.”

---

## Audit & Logs
- Recommended audit event: `COMPANY_UPDATED`
- Minimum fields:
    - `user_id`
    - `company_id`
    - `timestamp`
    - `action`
    - `changed_fields`

---

## Acceptance Criteria (QA)
- **AC-01:** Given an existing company, when opening the edit screen, then all fields must be pre-filled.
- **AC-02:** Given valid changes, when saving, then the company record must be updated.
- **AC-03:** Given an unchanged Name or CNPJ, when saving, then uniqueness validation must not fail.
- **AC-04:** Given a Name or CNPJ that matches another company, when saving, then the system must block the action.
- **AC-05:** Given the Cancel action, when clicked, then no changes must be persisted.
- **AC-06:** Given a status change to inactive, when saving, then the company may no longer appear in active-only reports.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
