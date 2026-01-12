# Wiki — Business Rules & Functional Specification (Audit)

**Product:** Audit System  
**Version:** 1.0  
**Document Owner:** (Team / Area)  
**Last updated:** 2026-01-04

---

## How to use this Wiki
- Each screen/feature has a unique ID (e.g., `AUD-003`, `AUD-020`)
- Business rules are defined inside the screen documentation
- Cross-cutting rules are documented under **References**
- Any rule change **must update the screen page and its changelog**

---

### 📌 Core Screens
- [AUD-001 — Dashboard](screen/AUD-001-dashboard.md)
- [AUD-002 — Ask the System](screen/AUD-002-ask-the-system.md)

---

## Screen Index by Module

### 🛠 Tools
- [AUD-003 — Balance Import](screen/Tools/ImportQueue/AUD-003-balance.md)
- [AUD-004 — Processes / Generate RAG](screen/Tools/AUD-004-processes-generate-rag.md)

---

### 📊 Reports
- [AUD-020 — My Uploaded Files](screen/Reports/AUD-020-my-uploaded-files.md)
- [AUD-021 — Company](screen/Reports/AUD-021-company.md)
- [AUD-022 — Company Tree](screen/Reports/AUD-022-company-tree.md)
- [AUD-023 — Report Company Tree](screen/Reports/AUD-023-company-tree-tree.md)
- [AUD-024 — Organizational Chart](screen/Reports/AUD-024-organizational-chart.md)
- [AUD-025 —  Files / Sent](screen/Reports/AUD-025-files-sent.md)

---

### ⚙ Settings
- [AUD-040 — Company / Consult](screen/Settings/AUD-040-company-consult.md)
- [AUD-041 — Company / Edit](screen/Settings/AUD-041-company-edit.md)
- [AUD-042 — Company / New](screen/Settings/AUD-042-company-new.md)
- [AUD-043 — Company Tree / Consult](screen/Settings/AUD-043-company-tree-consult.md)
- [AUD-044 — Company Tree / Edit](screen/Settings/AUD-044-company-tree-edit.md)
- [AUD-045 — Company Tree / New](screen/Settings/AUD-045-company-tree-new.md)

#### 📝 Register
- [AUD-046 — Register / Destination Service / Consult](screen/Settings/AUD-046-register-destination-service-consult.md)
- [AUD-047 — Register / Destination Service / Edit](screen/Settings/AUD-047-register-destination-service-edit.md)
- [AUD-048 — Register / Destination Service / New](screen/Settings/AUD-048-register-destination-service-new.md)
- [AUD-049 — Register / File States / Consult](screen/Settings/AUD-049-register-file-states-consult.md)
- [AUD-050 — Register / File States / Edit](screen/Settings/AUD-050-register-file-states-edit.md)
- [AUD-051 — Register / File States / New](screen/Settings/AUD-051-register-file-states-new.md)
- [AUD-052 — Register / File Step / Consult](screen/Settings/AUD-052-register-file-step-consult.md)
- [AUD-053 — Register / File Step / Edit](screen/Settings/AUD-053-register-file-step-edit.md)
- [AUD-054 — Register / File Step / New](screen/Settings/AUD-054-register-file-step-new.md)

---

## 📚 Reference Documents
- [File Lifecycle & Status Reference](reference/AUD-file-lifecycle.md)

---

## Glossary
- **Holding:** parent company that owns subsidiaries.
- **Import file:** file submitted for processing by the system.
- **FileStep:** processing stage of an uploaded file.
- **FileStates:** system-level usability state of a file record.

---

## Governance & Quality
- This wiki is part of the project deliverables
- All new screens must have a corresponding `.md` file
- All new FileSteps or TypeFiles must be documented in References
- PR reviews should validate documentation consistency

---

## Changelog
- **2026-01-04:** structure reviewed and standardized — AndreGregoletto
