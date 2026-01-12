# AUD-048 — Settings / Register / Destination Service / New

## Header
- **Screen:** Settings / Register / Destination Service / New
- **Objective:** create a new Destination Service.
- **Access Profiles:** Authorized users with register/settings permissions
- **Module:** Settings / Register

---

## Form Fields
1. **Name**
    - Required: Yes
    - Must be unique.

2. **Active**
    - Default: enabled (Active)
    - User may change before saving.

---

## Actions
### Save
Validates and creates the record.

### Cancel
Discards changes and returns to consult list.

---

## Business Rules

### AUD-051-RN-01 — Required field
Name is mandatory.

### AUD-051-RN-02 — Unique name
Name must be unique across File Services.

### AUD-051-RN-03 — Default status
Active is enabled by default.

---

## Validations & Messages
- Missing name: “Name is required.”
- Duplicate name: “A record with this name already exists.”

---

## Audit & Logs
- Recommended event: `FILE_SERVICE_CREATED`

---

## Acceptance Criteria (QA)
- **AC-01:** Cannot save without name.
- **AC-02:** Duplicate name is blocked.
- **AC-03:** Active is checked by default.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
