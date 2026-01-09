# AUD-024 — Reports / Company / Company Tree / Organizational Chart

> **Title rule:** the screen title must display the **commercial name** of the selected top-level company when available; otherwise, the **legal company name** must be used.

---

## Header
- **Screen:** Reports / Company / Company Tree / Organizational Chart
- **Objective:** visually represent the hierarchical structure of a company tree using an interactive organizational chart.
- **Access Profiles:** Authenticated users with reporting access
- **Module:** Audit / Reports / Company

---

## Overview
This screen provides a **graphical organizational chart** representation of a company tree.

Each company is displayed as a **node (card)** connected to its parent, allowing users to visually understand:
- hierarchy and control relationships,
- active and inactive companies within the tree,
- public trading status.

The chart supports **interactive navigation**, including drag and zoom controls.

---

## Node (Company Card) Information
Each company node displays:

1. **Company Name**
    - Legal company name.

2. **Commercial Name**
    - Displayed when available.

3. **CNPJ**
    - Official tax identifier.

4. **Holding Indicator**
    - Indicates control relationship:
        - `Controller`
        - `Controlled`

5. **Publicity Trade**
    - Indicates whether the company is publicly traded.
    - Uses the same logic and visual rules as other company reports.

6. **Company Status (internal badge)**
    - **Green:** company is active in the system.
    - **Red:** company is inactive in the system.

---

## Visual Status Rules

### Border Color (Tree Status)
- **Green border:** company is **active within the tree**
- **Red border:** company is **inactive within the tree**

> The border reflects the **tree relationship status**, not the system-level company status.

---

### Internal Status Badge (Company Status)
- **Green badge:** company status = `Active`
- **Red badge:** company status = `Inactive`

> This represents the **company record status**, independent of tree positioning.

---

## Interaction Controls

### Zoom Controls
- **Plus (+):** zoom in (increase visualization scale)
- **Minus (−):** zoom out (decrease visualization scale)
- **Reset:** reset zoom to default (100%)

### Drag & Move
- The organizational chart can be **dragged freely** to navigate large hierarchies.
- Dragging does not affect data or hierarchy, only visualization.

---

## Flow
### Entry
- User navigates from **Company Tree** screen (AUD-022) via:
    - **Organizational Chart**

### User Steps
1. View the organizational chart of the selected company tree.
2. Zoom in or out to adjust visualization.
3. Drag the chart to explore different areas of the hierarchy.
4. Inspect company details via node cards.

### Output
- Interactive visual representation of the company tree.

---

## Business Rules

### AUD-024-RN-01 — Tree scope
**Description:** the organizational chart must display all companies belonging to the selected company tree.  
**Condition:** data is scoped to the selected tree root.  
**Exceptions:** —

---

### AUD-024-RN-02 — Visual hierarchy
**Description:** parent–child relationships must be visually represented with connectors.  
**Condition:** each child node must connect directly to its immediate parent.  
**Exceptions:** —

---

### AUD-024-RN-03 — Tree status vs company status
**Description:** tree-level status and company-level status must be visually distinguishable.  
**Rules:**
- Tree status → border color
- Company status → internal badge

---

### AUD-024-RN-04 — Publicity trade indicator
**Description:** publicity trade indicator must follow the same logic as other company reports.  
**Condition:** reflects whether the company is publicly traded.  
**Exceptions:** —

---

### AUD-024-RN-05 — Read-only interaction
**Description:** the organizational chart is a read-only visualization.  
**Condition:** no edit, create, or delete actions are allowed.  
**Exceptions:** future enhancements may add drill-down or detail navigation.

---

## Display Rules
- Node layout must remain consistent across all levels.
- Long company names may be truncated visually but should not break layout.
- Status colors must be consistent with system standards.
- The chart must remain usable for large trees via zoom and drag.

---

## Performance Considerations
- Tree data should be loaded efficiently to support large hierarchies.
- Rendering should handle deep trees without significant UI lag.
- Zoom and drag must remain smooth and responsive.

---

## Audit & Logs
- No audit events are required for visualization.
- Optional: log access to organizational chart for analytics.

---

## Acceptance Criteria (QA)
- **AC-01:** Given a company tree, when opening the organizational chart, then all companies in the tree must be displayed.
- **AC-02:** Given an active company in the tree, when displayed, then its node border must be green.
- **AC-03:** Given an inactive company in the tree, when displayed, then its node border must be red.
- **AC-04:** Given an active company record, when displayed, then its status badge must be green.
- **AC-05:** Given an inactive company record, when displayed, then its status badge must be red.
- **AC-06:** Given a publicly traded company, when displayed, then the publicity trade indicator must reflect this.
- **AC-07:** Given zoom controls, when clicking plus or minus, then the chart scale must update accordingly.
- **AC-08:** Given the chart canvas, when dragging, then the chart position must move without affecting data.
- **AC-09:** Given the reset control, when clicked, then zoom must return to default.

---

## Changelog
- **2026-01-04:** page created — AndreGregoletto
