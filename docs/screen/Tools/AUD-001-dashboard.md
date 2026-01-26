# AUD-001 — Dashboard

## Header
- **Screen:** Dashboard
- **Objective:** present a daily operational overview of the system, showing only metrics from the current day and the user’s participation in each metric.
- **Access Profiles:** authenticated users
- **Module:** Audit / Dashboard

---

## Screen Overview
The Dashboard screen is responsible for displaying a **summary of system activity for the current day only**.

This screen allows the user to:
- Monitor daily imports and generated files
- Quickly identify errors or abnormal volumes
- Understand how much of the daily activity was performed by the logged-in user

No historical data or date filters are available on this screen.

---

## Screen Sections

### 1. Daily Summary — “Arquivos (Hoje)”

#### Description
Main dashboard block responsible for showing the daily status of imports and file generation.

#### Header elements
- **Title:** Archives (Today)
- **Reference date/time:** current system date and time


---

## Business Rules

### AUD-001-RN-01 — Daily scope
**Description:** all dashboard metrics must be calculated using only records from the current day.  
**Condition:** `created_at = today`.

---

### AUD-001-RN-02 — User-based calculation
**Description:** user participation must be calculated using the authenticated user.  
**Condition:** `user_id = logged_user_id`.

---

### AUD-001-RN-03 — Read-only screen
**Description:** the Dashboard screen is informational only and must not allow data changes.  
**Exceptions:** none.

---

## Validations
- Metrics must always return numeric values.
- Null values must be rendered as zero.
- Negative values are not allowed.

---

## Audit & Logs
- No transactional records are created on this screen.
- Optional access logging:
- `DASHBOARD_VIEWED`

---

## Acceptance Criteria (QA)

- **AC-01:** Given a new day, when accessing the Dashboard, only today’s data must be displayed.
- **AC-02:** KPI totals must match the system database counts for the day.
- **AC-03:** User participation must reflect only records created by the logged-in user.
- **AC-04:** Percentages must not be displayed when the system total equals zero.
- **AC-05:** Chart values must match KPI values.

---

## Changelog
- **2026-01-26:** page created — Andre Gregoletto
