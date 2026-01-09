# AUD-021 — Reports / Company

## Header
- **Screen:** Reports / Company
- **Objective:** list all companies currently active in the system and allow users to search companies asynchronously by name or commercial name.
- **Access Profiles:** Authenticated users with reporting access
- **Module:** Audit / Reports

---

## Overview
This screen provides a read-only view of companies registered in the system.  
It is intended for consultation, validation, and cross-reference usage within audit and reporting flows.

Only **active companies** are listed by default.

---

## Table Columns
1. **Name**
    - Description: legal company name.

2. **Commercial Name**
    - Description: trading/commercial name of the company.

3. **CNPJ**
    - Description: official company tax identifier.

4. **Publicity Trade**
    - Description: indicates whether the company is publicly traded or flagged for publicity purposes.
    - Displayed as status badge.

5. **Status**
    - Description: system-level status of the company.
    - Only companies with status `Active` are displayed.

---

## Search Behavior
### Search Field
- Label: `Search Here...`
- Type: text input
- Behavior: **asynchronous (live search)**

### Search Rules
- The search is triggered **on each typed character**
- The search matches against:
    - `companies.name`
    - `companies.commercial_name`
- The search is **case-insensitive**
- Results update dynamically without page reload

### Empty Search
- When the search field is empty, the default list of active companies is displayed.

---

## Flow
### Entry
- User accesses **Reports / Company**.

### User Steps
1. View the list of active companies.
2. Type into the search field.
3. The system updates the list asynchronously based on the input.

### Output
- Filtered company list based on search criteria.
- Pagination updates accordingly.

---

## Business Rules

### AUD-021-RN-01 — Active companies only
**Description:** the screen must list only companies with active system status.  
**Condition:** `companies.status = Active`  
**Exceptions:** —

### AUD-021-RN-02 — Asynchronous search
**Description:** company search must be executed asynchronously as the user types.  
**Condition:** search triggered on every input change.  
**Exceptions:** —

### AUD-021-RN-03 — Search fields
**Description:** the search must match company records by **legal name** or **commercial name**.  
**Condition:** partial match (`contains`) on `name` or `commercial_name`.  
**Exceptions:** —

### AUD-021-RN-04 — Read-only screen
**Description:** this screen is for consultation only.  
**Condition:** no create, edit, delete, or action buttons are available.  
**Exceptions:** future enhancements may introduce navigation to company details.

---

## Validations & Display Rules
- Company names may be truncated for layout purposes.
- Full values should be accessible if the UI supports tooltip or detail view.
- Status badges must reflect the company status clearly and consistently.
- Pagination must adapt to search results.

---

## Performance Considerations
- Search must be debounced to avoid excessive requests.
- The result set should be paginated.
- Queries should be indexed on:
    - `name`
    - `commercial_name`
    - `status`

---

## Audit & Logs
- No audit events are required for simple list and search operations.
- Optional: log search metrics for performance monitoring (non-functional).

---

## Acceptance Criteria (QA)
- **AC-01:** Given an inactive company, when opening the screen, then it must not be listed.
- **AC-02:** Given an active company, when opening the screen, then it must be listed.
- **AC-03:** Given a partial company name, when typing in the search field, then matching results must appear asynchronously.
- **AC-04:** Given a partial commercial name, when typing in the search field, then matching results must appear asynchronously.
- **AC-05:** Given an empty search field, when the screen loads, then all active companies must be listed.
- **AC-06:** Given search results exceeding one page, when navigating pagination, then results must remain filtered.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
