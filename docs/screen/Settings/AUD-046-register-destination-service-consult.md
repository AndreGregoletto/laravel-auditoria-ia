# AUD-046 — Settings / Register / Destination Service / Consult

## Header
- **Screen:** Settings / Register / Destination Service / Consult
- **Objective:** list and manage Destination Services (destination services used by file processing pipelines).
- **Access Profiles:** Authorized users with register/settings permissions
- **Module:** Settings / Register

---

## Overview
This screen provides a management view for **Destination Service**, used to categorize and route uploaded files to a destination processing service (e.g., Balance, RAG).

It allows:
- searching existing services,
- creating new services,
- editing existing services.

---

## Search Behavior
- Field: `Search Here...`
- Scope: **Name**
- Behavior: asynchronous (live search), case-insensitive, partial match.

---

## Table Columns
1. **Name**
2. **Status**
3. **Created In**
4. **Updated In**
5. **Actions**
    - Edit

---

## Actions
### New
Navigates to the File Service creation screen.

### Edit
Navigates to the File Service edit screen for the selected record.

---

## Business Rules

### AUD-050-RN-01 — Search scope
Search must only match by **Name**.

### AUD-050-RN-02 — Record visibility
All records must be listed (active and inactive), unless a future filter is added.

### AUD-050-RN-03 — Permissions
Only authorized users can access and manage these records.

---

## Acceptance Criteria (QA)
- **AC-01:** Search filters results by name as user types.
- **AC-02:** New navigates to creation screen.
- **AC-03:** Edit opens the selected record.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
