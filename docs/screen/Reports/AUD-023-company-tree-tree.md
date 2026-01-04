# AUD-023 — Reports / Company / Company Tree / Tree: {Company Name}

> **Title rule:** the screen title must display the **commercial name** of the top-level company when available; otherwise, the **legal company name** must be used.

---

## Header
- **Screen:** Reports / Company / Company Tree / Tree
- **Objective:** display the hierarchical structure of a selected company tree, organized by levels and parent–child relationships.
- **Access Profiles:** Authenticated users with reporting access
- **Module:** Audit / Reports / Company

---

## Overview
This screen presents a **hierarchical report of a company tree**, starting from the top-level company (root) and expanding through its subsidiaries.

The structure combines:
- **explicit hierarchy levels** (levels 1 and 2),
- followed by **children grouped under their direct parents**.

The report also clearly indicates whether each company is a **controller** or **controlled** entity.

---

## Title Resolution Rule
- If the top-level company has a **commercial name**, display it in the title.
- Otherwise, display the **legal company name**.

**Example:**
- `Tree: Coca-Cola Brasil`
- `Tree: The Coca-Cola Company Brasil Ltda`

---

## Table Columns
1. **Levels**
    - Description: hierarchical level of the company within the tree.
    - Level definitions:
        - `1`: tree root (top-level company)
        - `2`: direct subsidiaries of the root
        - `3+`: subsidiaries grouped under their immediate parent

2. **Company**
    - Description: legal name of the company.

3. **Commercial Name**
    - Description: commercial/trade name of the company.

4. **Company Holding**
    - Description: legal name of the direct parent company in the hierarchy.
    - For level 1, this field is empty or not displayed.

5. **Holding**
    - Description: indicates the control relationship of the company.
    - Possible values:
        - `Controller` — company controls one or more subsidiaries
        - `Controlled` — company is controlled by a parent

6. **Status**
    - Description: system-level status of the company record.
    - Expected value: `Active`

---

## Hierarchy Rules

### AUD-023-RN-01 — Root level
**Description:** the top-level company must always be displayed as **Level 1**.  
**Condition:** company has no parent within the selected tree.  
**Holding:** `Controller`

---

### AUD-023-RN-02 — Level 2 companies
**Description:** direct subsidiaries of the root company must be displayed as **Level 2**.  
**Condition:** `parent_company_id = root_company_id`  
**Holding:** `Controller` or `Controlled` depending on whether they have children.

---

### AUD-023-RN-03 — Child grouping
**Description:** companies at level 3 or deeper must be listed **directly under their immediate parent**, maintaining logical grouping.  
**Condition:** child rows must follow their parent row in the report.  
**Exceptions:** —

---

### AUD-023-RN-04 — Holding indicator
**Description:** the **Holding** column must reflect the control relationship.  
**Rules:**
- `Controller` if the company has at least one child.
- `Controlled` if the company has a parent.

---

### AUD-023-RN-05 — Active records only
**Description:** only companies with active status must be displayed in the report.  
**Condition:** `companies.status = Active`  
**Exceptions:** —

---

## Flow
### Entry
- User navigates from **Company Tree** screen (AUD-022) via:
    - **See Report**

### User Steps
1. View the hierarchical list of companies.
2. Identify control relationships via the **Holding** column.
3. Scroll through grouped levels to understand the organizational structure.

### Output
- Read-only hierarchical report of the selected company tree.

---

## Display Rules
- Level indentation or visual grouping must clearly reflect hierarchy.
- Parent companies must appear **before** their children.
- Status and holding badges must use consistent visual styles.
- Empty or non-applicable fields must not break table alignment.

---

## Performance Considerations
- Tree data should be loaded efficiently to avoid N+1 queries.
- Ordering must preserve parent–child relationships.
- Large trees may require pagination or virtual scrolling in the future.

---

## Audit & Logs
- No audit events are required for read-only visualization.
- Optional: log report access for usage analytics.

---

## Acceptance Criteria (QA)
- **AC-01:** Given a selected company tree, when opening the screen, then the root company must appear as level 1.
- **AC-02:** Given a root company with subsidiaries, when viewing the report, then direct subsidiaries must appear as level 2.
- **AC-03:** Given a company with children, when displayed, then its holding status must be `Controller`.
- **AC-04:** Given a company without children but with a parent, when displayed, then its holding status must be `Controlled`.
- **AC-05:** Given a tree with multiple hierarchy levels, when displayed, then children must be listed directly under their respective parents.
- **AC-06:** Given a company with a commercial name, when opening the screen, then the title must display the commercial name.
- **AC-07:** Given a company without a commercial name, when opening the screen, then the title must display the legal name.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
