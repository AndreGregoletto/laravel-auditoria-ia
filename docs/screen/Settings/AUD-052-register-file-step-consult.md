# AUD-053 — Settings / Register / File Step / Consult

## Header
- **Screen:** Settings / Register / File Step / Consult
- **Objective:** list and manage File Steps used to represent the lifecycle stage of a file.
- **Access Profiles:** Authorized users with register/settings permissions
- **Module:** Settings / Register

---

## Overview
File Steps represent the **processing stage** of files (e.g., In Queue, Processing, Processed, Error, Cancelled, RAG generated).

This screen allows:
- searching file steps,
- creating new steps,
- editing existing ones.

---

## Search Behavior
- Field: `Search Here...`
- Scope: **Name** and **name_conf**
- Behavior: asynchronous (live search), case-insensitive, partial match.

---

## Table Columns
1. **Name**
2. **Name Conf**
3. **Status**
4. **Created In**
5. **Updated In**
6. **Actions**
    - Edit

---

## Actions
### New
Navigates to the File Step creation screen.

### Edit
Navigates to the File Step edit screen.

---

## Business Rules

### AUD-053-RN-01 — Search scope
Search matches by **Name** or **name_conf**.

### AUD-053-RN-02 — Record visibility
All records must be listed (active and inactive).

### AUD-053-RN-03 — Permissions
Only authorized users can manage file steps.

---

## Acceptance Criteria (QA)
- **AC-01:** Search filters results by name or name_conf.
- **AC-02:** New navigates to creation screen.
- **AC-03:** Edit opens the selected record.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
