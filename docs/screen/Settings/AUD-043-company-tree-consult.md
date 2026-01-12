# AUD-043 — Settings / Company Tree / Consult

## Header
- **Screen:** Settings / Company Tree / Consult
- **Objective:** list all active company trees in the system and allow navigation to edit tree or organizational charts.
- **Access Profiles:** Authenticated users with reporting access
- **Module:** Audit / Settings / Company Tree

---

## Overview
This screen provides a visual and navigational entry point to **company trees** registered in the system.

Each card represents a **top-level company (tree root)** and allows the user to:
- view the tree report
- access the organizational chart (org chart)

Only **active company trees** are displayed.

---

## Card Information
Each card contains the following information:

1. **Company Name**
    - Description: legal name of the top-level company (tree root).

2. **Commercial Name**
    - Description: commercial/trade name of the top-level company.

3. **CNPJ**
    - Description: official tax identifier of the top-level company.

4. **Status**
    - Description: system-level status of the company tree.
    - Displayed as a badge (e.g., `Active`).

5. **Actions**
    - **See Report** — navigates to the company tree report.
    - **Organizational Chart** — navigates to the visual org chart representation.

---

## Search Behavior
### Search Field
- Label: `Search Here...`
- Type: text input
- Behavior: **asynchronous (live search)**

### Search Rules
- Search is triggered on **each typed character**
- Search matches against:
    - top-level company `name`
    - top-level company `commercial_name`
- Search is **case-insensitive**
- Results update dynamically without page reload

### Empty Search
- When the search field is empty, all active company trees are displayed.

---

## Flow
### Entry
- User accesses **Settings / Company Tree/ Consult**.

### User Steps
1. View available company tree cards.
2. Use the search field to filter trees by company name or commercial name.
3. Select one of the available actions:
    - View the tree report
    - View the organizational chart

### Output
- Filtered list of company trees.
- Navigation to the selected destination screen.

---

## Business Rules

### AUD-022-RN-01 — Active company trees only
**Description:** only company trees marked as active must be displayed.  
**Condition:** `company_tree.status = Active`  
**Exceptions:** —

### AUD-022-RN-02 — Tree root representation
**Description:** each card represents a **tree root company**, not individual subsidiaries.  
**Condition:** displayed company must be the top-level node of a company tree.  
**Exceptions:** —

### AUD-022-RN-03 — Asynchronous search
**Description:** the search must be executed asynchronously as the user types.  
**Condition:** search triggered on every input change.  
**Exceptions:** —

### AUD-022-RN-04 — Search scope
**Description:** the search must match only the **top-level company** of each tree.  
**Condition:** partial match on `name` or `commercial_name`.  
**Exceptions:** subsidiaries must not influence search results.

### AUD-022-RN-05 — Navigation actions
**Description:** each company tree card must provide navigation to two destinations.  
**Actions:**
- **See Report:** navigates to the company tree report view.
- **Organizational Chart:** navigates to the organizational chart visualization.

**Exceptions:** —
- If a destination is unavailable, the action must not be displayed or must be disabled.

---

## Validations & Display Rules
- Company names may be truncated for layout purposes.
- Full values should remain accessible if supported by the UI.
- Status badge must clearly reflect the tree status.
- Cards should maintain consistent layout and spacing.

---

## Performance Considerations
- Search should be debounced to avoid excessive requests.
- Result set should be paginated or lazily loaded if the number of trees grows significantly.
- Indexes are recommended on:
    - `companies.name`
    - `companies.commercial_name`
    - `companies_tree.status`

---

## Audit & Logs
- No audit events are required for simple listing or navigation.
- Optional: navigation events may be logged for analytics or usage insights.

---

## Acceptance Criteria (QA)
- **AC-01:** Given an inactive company tree, when opening the screen, then it must not be displayed.
- **AC-02:** Given an active company tree, when opening the screen, then it must be displayed as a card.
- **AC-03:** Given a partial top-level company name, when typing in the search field, then matching trees must be displayed asynchronously.
- **AC-04:** Given a partial commercial name, when typing in the search field, then matching trees must be displayed asynchronously.
- **AC-05:** Given a tree card, when clicking **See Report**, then the system must navigate to the corresponding tree report.
- **AC-06:** Given a tree card, when clicking **Organizational Chart**, then the system must navigate to the corresponding org chart view.
- **AC-07:** Given an empty search field, when the screen loads, then all active company trees must be displayed.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
