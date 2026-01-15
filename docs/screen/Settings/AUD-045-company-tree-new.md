# AUD-045 — Settings / Company Tree / New

## Header
- **Screen:** Settings / Company Tree / New
- **Objective:** create a new company tree by selecting a top company (tree root) and defining the initial tree status.
- **Access Profiles:** Authorized users with company-tree management permissions
- **Module:** Settings / Company Tree

---

## Overview
This screen allows authorized users to **create a new company tree**.

A company tree is defined by a **Top Company** (the tree root).  
The tree is created with an initial **status** (Active/Inactive), where **Active is the default**.

---

## Form Fields

1. **Top Company**
    - Description: the company that will become the root of the tree (Level 1).
    - Type: select
    - Required: **Yes**
    - Source: eligible companies (see rules below)

2. **Active**
    - Description: defines whether the new tree is active upon creation.
    - Type: checkbox
    - Default value: **Enabled (Active)**

---

## Default Values
- **Active:** checked by default (the tree is created as Active unless the user changes it).

---

## Actions

### Save
**Description:** creates the company tree.  
**Behavior:**
- Validates required selection.
- Creates the tree with the selected top company.
- Redirects back to the company tree consultation/list screen (recommended).

### Cancel
**Description:** aborts the creation process.  
**Behavior:**
- No data is persisted.
- Redirects back to the company tree consultation/list screen.

---

## Business Rules

### AUD-045-RN-01 — Mandatory top company
**Description:** Top Company is required to create a tree.  
**Condition:** must be selected before saving.  
**Exceptions:** —

---

### AUD-045-RN-02 — Top company eligibility (cannot already be a tree root)
**Description:** the Top Company dropdown must not include companies that are already configured as a **top/root company** of another tree.  
**Condition:** company must **not** already be assigned as `tree_root` in any existing tree.  
**Exceptions:** —

> This allows selecting companies that may exist as **non-root nodes** in other trees (i.e., they can be part of a tree but not be the root of another tree).

---

### AUD-045-RN-03 — Default tree status
**Description:** new trees must be created as Active by default.  
**Condition:** `status = Active` unless the user changes it.  
**Exceptions:** —

---

### AUD-045-RN-04 — Root node initialization
**Description:** when the tree is created, the top company must be inserted as the root node (Level 1).  
**Condition:**
- `company_id = top_company_id`
- `company_parent_id = top_company_id` (or null, depending on system model)
- `level = 1`
- `holding = Controller` (initially, root is considered controller)

**Exceptions:** implementation may vary, but the root must be represented explicitly.

---

### AUD-045-RN-05 — Permissions
**Description:** only authorized users can create company trees.  
**Condition:** permission validation must be enforced.  
**Exceptions:** —

---

## Validations & Messages
- **Missing Top Company:** “Top company is required.”
- **Top Company already used as root:** “This company is already the top company of another tree.”
- **Successful creation (suggested):** “Company tree created successfully.”

---

## Audit & Logs
- Recommended audit event: `COMPANY_TREE_CREATED`
- Minimum fields:
    - `user_id`
    - `tree_id`
    - `top_company_id`
    - `status`
    - `timestamp`

---

## Acceptance Criteria (QA)
- **AC-01:** Given no Top Company selected, when clicking Save, then the system must block creation.
- **AC-02:** Given a company already used as a tree root, when opening the Top Company dropdown, then it must not appear.
- **AC-03:** Given an eligible company selected, when clicking Save, then the tree must be created successfully.
- **AC-04:** Given the screen loads, when no user changes status, then Active must be enabled by default.
- **AC-05:** Given Cancel is clicked, then no tree must be created.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
