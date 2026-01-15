# AUD-047 — Settings / Register / Destination Service / Edit

## Header
- **Screen:** Settings / Register / Destination Service / Edit
- **Objective:** edit an existing Destination Service.
- **Access Profiles:** Authorized users with register/settings permissions
- **Module:** Settings / Register

---

## Overview
This screen updates an existing Destination Service record. Fields are pre-filled.

---

## Form Fields
1. **Name \***
    - Required: Yes
    - Must remain unique (excluding the current record).

2. **Active**
    - Pre-filled with existing value.

---

## Actions
### Save Changes
Validates and persists changes.

### Cancel
Discards changes and returns to consult list.

---

## Business Rules

### AUD-052-RN-01 — Required field
Name cannot be empty.

### AUD-052-RN-02 — Unique name (conditional)
Name must be unique across records (excluding current record).

---

## Validations & Messages
- Missing name: “Name is required.”
- Duplicate name: “Another record with this name already exists.”

---

## Audit & Logs
- Recommended event: `FILE_SERVICE_UPDATED`

---

## Acceptance Criteria (QA)
- **AC-01:** Fields are pre-filled.
- **AC-02:** Duplicate name is blocked.
- **AC-03:** Status can be toggled and saved.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
