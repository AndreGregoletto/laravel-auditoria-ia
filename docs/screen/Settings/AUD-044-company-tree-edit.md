# AUD-044 — Settings / Company Tree / Edit

## Header
- **Screen:** Settings / Company Tree / Edit
- **Objective:** manage an existing company tree by viewing the hierarchical structure, activating/inactivating nodes, and adding new companies into the tree.
- **Access Profiles:** Authorized users with company-tree management permissions
- **Module:** Settings / Company Tree

---

## Overview
This screen allows authorized users to **edit a company tree** in a controlled way.

The tree is displayed as a structured list, ordered by:
- hierarchy level (Level 1, Level 2, Level 3, …),
- and parent–child relationship (children displayed under their parent).

Users can:
- **activate/inactivate** companies within the tree,
- **add** a company below a selected node (parent).

This screen directly affects the structure and active visibility of the company tree used in reports and organizational charts.

---

## List / Table Display

### Ordering & Hierarchy
- Companies are displayed in tree order, grouped by parent.
- Each row represents a company within the tree.
- Visual indentation indicates hierarchy depth (child under parent).

### Columns
1. **Company**
    - Legal company name.

2. **Commercial Name**
    - Commercial/trade name.

3. **Levels**
    - Tree hierarchy level:
        - Level 1 = root company (top)
        - Level 2 = direct subsidiaries
        - Level 3+ = deeper subsidiaries

4. **Holding**
    - Control relationship indicator:
        - `Controller`
        - `Controlled`

5. **Status**
    - Tree-level status of the node:
        - `Active`
        - `Inactive`

6. **Actions**
    - Add company below current level (icon `+`)
    - Activate/Inactivate toggle (status control)

---

## Actions

### Add Company Below Current Level (`+`)
Opens a modal that allows adding a new company as a **child** of the selected node.

### Activate / Inactivate Node
Toggles the selected node status within the tree:
- Active → Inactive
- Inactive → Active

> These changes affect tree structure behavior and downstream reports.

---

## Modal — Add Company Below the Current Level

### Purpose
Allow the user to select a company and add it as a **controlled** child under the selected parent node.

### Fields
1. **Select the controlled company**
    - Type: select
    - Source: eligible companies only (see rules below)

### Modal Actions
- Confirm / Add (creates the relationship)
- Cancel (closes modal without changes)

---

## Business Rules

### AUD-044-RN-01 — Tree ordering
**Description:** the list must be shown in hierarchical order, grouped by parent and level.  
**Condition:** parent row must appear before its children.  
**Exceptions:** —

---

### AUD-044-RN-02 — Add child relationship
**Description:** when a company is added through the modal, it becomes a **child** of the selected node and must inherit the correct level.  
**Condition:**
- child parent = selected node
- child level = parent level + 1  
  **Exceptions:** —

---

### AUD-044-RN-03 — Eligibility for modal select (cannot already belong to the tree)
**Description:** the modal dropdown must not include companies that already belong to the current tree.  
**Condition:** company must not exist in this tree (any level).  
**Exceptions:** —

---

### AUD-044-RN-04 — Eligibility for modal select (cannot be inactive)
**Description:** the modal dropdown must include only **active companies**.  
**Condition:** `company.status = Active`  
**Exceptions:** —

> Summary for the modal dropdown:  
> A company appears in the combo **only if** it is **Active** and **does not already belong** to the current tree.

---

### AUD-044-RN-05 — Inactivating a node triggers child re-parenting or inactivation
**Description:** when a node is inactivated, the system must protect hierarchy consistency.  
**Rule:** children of the inactivated node are handled as follows:

1) **If the child has no alternative parent**
- The child becomes **Inactive**.

2) **If the child has another valid parent**
- The child is **re-parented** to the parent of the inactivated node.

**Example (as provided):**
- Company **X** has parent **1**
- Company **Y** has parent **X**
- If **X** is inactivated, then **Y** becomes child of **1** (re-parenting)

---

### AUD-044-RN-06 — Parent reassignment preserves hierarchy levels
**Description:** when a child is re-parented due to a parent being inactivated, hierarchy levels must be recalculated.  
**Condition:** the child’s level becomes `(new parent level + 1)` and all descendants must be adjusted accordingly.  
**Exceptions:** —

---

### AUD-044-RN-07 — Holding indicator consistency
**Description:** Holding indicator must remain consistent with the tree structure.  
**Rules:**
- A node is `Controller` if it has at least one child in the tree.
- A node is `Controlled` if it has a parent in the tree.

---

### AUD-044-RN-08 — Read/write governance
**Description:** only authorized users can modify company trees (add, activate, inactivate).  
**Condition:** permission validation must be enforced before performing changes.  
**Exceptions:** —

---

## Flow

### Entry
- User navigates to **Settings / Company Tree / Edit** for a selected tree.

### User Steps
1. Review the hierarchical list of companies in the tree.
2. Optionally inactivate/activate a node.
3. Optionally click `+` to add a new company below a selected parent.
4. Confirm changes.

### Output
- Tree structure updates and is reflected in reports and org chart views.

---

## Validations & Messages
- **No eligible companies in modal:** “No companies available to add.”
- **Attempt to add existing company:** “This company already belongs to the tree.”
- **Attempt to add inactive company:** “Only active companies can be added.”
- **Inactivation with cascade:** system should notify if children were re-parented/inactivated (recommended).

---

## Audit & Logs
- Recommended audit events:
    - `COMPANY_TREE_NODE_ADDED`
    - `COMPANY_TREE_NODE_ACTIVATED`
    - `COMPANY_TREE_NODE_INACTIVATED`
    - `COMPANY_TREE_NODE_REPARENTED`
- Minimum fields:
    - `user_id`
    - `tree_id`
    - `company_id`
    - `parent_company_id` (before/after if changed)
    - `timestamp`
    - `action`

---

## Acceptance Criteria (QA)
- **AC-01:** Given an existing tree, when opening the screen, then nodes must be ordered by parent and level.
- **AC-02:** Given a node, when clicking `+`, then the add-company modal must open.
- **AC-03:** Given a company already in the tree, when opening the modal dropdown, then it must not appear as an option.
- **AC-04:** Given an inactive company, when opening the modal dropdown, then it must not appear as an option.
- **AC-05:** Given an eligible company selected in the modal, when confirming, then it must be added as a child of the selected node and level must be updated.
- **AC-06:** Given a node with children, when inactivating the node, then children must either be re-parented to the node’s parent or be inactivated if no alternative parent exists.
- **AC-07:** Given the example X → Y with parent 1, when X is inactivated, then Y’s parent becomes 1 and levels are recalculated.
- **AC-08:** Given unauthorized user, when accessing the screen, then access must be denied.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
