# AUD-054 — Settings / Register / File Step / New

## Header
- **Screen:** Settings / Register / File Step / New
- **Objective:** create a new File Step.
- **Access Profiles:** Authorized users with register/settings permissions
- **Module:** Settings / Register

---

## Form Fields
1. **Name \***
    - Required: Yes
    - Must be unique.

2. **Name Conf \***
    - Required: Yes
    - Must be unique.
    - Format: lowercase snake_case recommended.

3. **Active**
    - Default: enabled (Active)

---

## Actions
### Save
Validates and creates the record.

### Cancel
Discards changes and returns to consult list.

---

## Business Rules

### AUD-054-RN-01 — Required fields
Name and name_conf are mandatory.

### AUD-054-RN-02 — Uniqueness
Both Name and name_conf must be unique.

### AUD-054-RN-03 — Default status
Active is enabled by default.

---

## Validations & Messages
- Missing required: “This field is required.”
- Duplicate Name: “A record with this name already exists.”
- Duplicate name_conf: “A record with this configuration name already exists.”

---

## Audit & Logs
- Recommended event: `FILE_STEP_CREATED`

---

## Acceptance Criteria (QA)
- **AC-01:** Cannot save without both fields.
- **AC-02:** Duplicate fields are blocked.
- **AC-03:** Active is checked by default.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
