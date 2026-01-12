# AUD-058 — Settings / Register / File States / Edit

## Header
- **Screen:** Settings / Register / File States / Edit
- **Objective:** edit an existing File Status.
- **Access Profiles:** Authorized users with register/settings permissions
- **Module:** Settings / Register

---

## Overview
This screen updates an existing File States record. Fields are pre-filled.

---

## Form Fields
1. **Name \***
    - Required: Yes
    - Must remain unique (excluding current record).

2. **Name Conf \***
    - Required: Yes
    - Must remain unique (excluding current record).

3. **Active**
    - Pre-filled with existing value.

---

## Actions
### Save Changes
Validates and persists changes.

### Cancel
Discards changes and returns to consult list.

---

## Business Rules

### AUD-058-RN-01 — Required fields
Name and name_conf cannot be empty.

### AUD-058-RN-02 — Conditional uniqueness
Name and name_conf must remain unique excluding the current record.

---

## Validations & Messages
- Duplicate Name: “Another record with this name already exists.”
- Duplicate name_conf: “Another record with this configuration name already exists.”

---

## Audit & Logs
- Recommended event: `FILE_STATUS_UPDATED`

---

## Acceptance Criteria (QA)
- **AC-01:** Fields are pre-filled.
- **AC-02:** Duplicate values are blocked.
- **AC-03:** Status can be toggled and saved.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
