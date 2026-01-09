# AUD-040 — Settings / Company / Consult

## Header
- **Screen:** Settings / Company / Consult
- **Objective:** list companies registered in the system and provide access to create new companies or edit existing ones.
- **Access Profiles:** Authorized users with company management permissions
- **Module:** Settings / Company

---

## Overview
This screen is the **entry point for company management** in the system.

It allows users to:
- consult registered companies,
- search companies by key identifiers,
- navigate to the company creation screen,
- edit existing company records.

Unlike report screens, this screen **allows write operations** (create and update).

---

## Search Behavior
### Search Field
- Label: `Search Here...`
- Type: text input
- Behavior: asynchronous (live search)

### Search Scope
The search must match companies by:
- **Legal name**
- **Commercial name**
- **CNPJ**

The search is:
- case-insensitive,
- triggered on each typed character,
- applied only to the fields listed above.

---

## Table Columns
1. **Name**
    - Legal company name.

2. **Commercial Name**
    - Commercial/trade name of the company.

3. **CNPJ**
    - Official tax identifier.

4. **Publicity Trade**
    - Indicates whether the company is publicly traded.
    - Displayed as a status badge.

5. **Status**
    - System-level status of the company record.

6. **Actions**
    - Available management actions for each company.

---

## Actions

### New
**Description:** navigates the user to the company creation screen.  
**Behavior:**
- Opens the **New Company** registration form.
- The form is initially empty.

---

### Edit
**Description:** allows editing an existing company record.  
**Visibility Rule:**
- Available for all listed companies.

**Behavior:**
- Navigates to the company edit screen.
- Loads the selected company data.

---

## Flow
### Entry
- User accesses **Settings / Company / Consult**.

### User Steps
1. View the list of registered companies.
2. Use the search field to filter companies.
3. Click **New** to create a new company.
4. Click **Edit** to update an existing company.

### Output
- Navigation to company creation or editing screens.
- Updated company data reflected upon return.

---

## Business Rules

### AUD-040-RN-01 — Search scope restriction
**Description:** the search must only consider name, commercial name, and CNPJ.  
**Condition:** other fields must not affect search results.  
**Exceptions:** —

---

### AUD-040-RN-02 — Company visibility
**Description:** all companies registered in the system must be listed, regardless of status.  
**Condition:** no filtering by company status is applied by default.  
**Exceptions:** future filters may be introduced.

---

### AUD-040-RN-03 — Management permissions
**Description:** only users with appropriate permissions may access this screen and perform actions.  
**Condition:** permission validation must be enforced before allowing create or edit actions.  
**Exceptions:** —

---

## Validations & Display Rules
- Company names may be truncated for layout purposes.
- Status and publicity trade badges must follow system standards.
- Pagination must be applied to large datasets.
- The **New** button must be clearly visible and accessible.

---

## Performance Considerations
- Search must be debounced to prevent excessive requests.
- Queries should be indexed on:
    - name
    - commercial_name
    - CNPJ
- Pagination is required for scalability.

---

## Audit & Logs
- Recommended audit events:
    - `COMPANY_CREATED`
    - `COMPANY_UPDATED`
- Minimum fields:
    - `user_id`
    - `company_id`
    - `timestamp`
    - `action`

---

## Acceptance Criteria (QA)
- **AC-01:** Given a registered company, when opening the screen, then it must be listed.
- **AC-02:** Given a partial legal name, when typing in the search field, then matching companies must be displayed.
- **AC-03:** Given a partial commercial name, when typing in the search field, then matching companies must be displayed.
- **AC-04:** Given a CNPJ value, when typing in the search field, then matching companies must be displayed.
- **AC-05:** Given the **New** button, when clicked, then the system must navigate to the company creation screen.
- **AC-06:** Given a listed company, when clicking **Edit**, then the system must navigate to the company edit screen with data loaded.
- **AC-07:** Given a user without proper permission, when attempting access, then access must be denied.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
