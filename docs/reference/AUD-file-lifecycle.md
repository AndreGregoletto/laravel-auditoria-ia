# File Lifecycle Reference — Audit System

## Purpose
This document defines the **official lifecycle reference** for uploaded files in the Audit system.  
It establishes a **single source of truth** for:
- processing steps (`FileStep`)
- system usability (`FileStates`)
- allowed user actions
- future extensibility (e.g., RAG pipelines)

This reference must be updated whenever a new processing step or file type is introduced.

---

## FileStep × Actions × FileStates

| FileStep | name_conf | Description | Allowed Actions | FileStates | Notes |
|--------|-----------|-------------|-----------------|-----------|------|
| **In Queue** | `in_queue` | File has been submitted and is waiting for processing. | Cancel | Active | Only state where cancellation is allowed. |
| **Processing** | `processing` | File is actively being processed by the system. | None | Active | Actions are locked to prevent inconsistencies. |
| **Processed** | `processed` | File processing finished successfully. | — | Active | Final state for Balance imports. |
| **RAG generated** | `rag_generated` | Processing produced a derived artifact for RAG or AI usage. | Download | Active | Output artifact must exist to enable download. |
| **Error** | `error` | Processing failed due to validation or system error. | None | Inactive | Error details must be auditable. |
| **Cancelled** | `cancelled` | Submission was cancelled by the user before processing. | None | Inactive | Final and irreversible state. |

---

## FileStates Semantics

| FileStates | Meaning | System Behavior |
|-----------|--------|----------------|
| **Active** | Record is valid and usable by the system. | Can be referenced and produce outputs. |
| **Inactive** | Record is invalid or deprecated. | Must not be processed or reused. |

---

## Action Rules Summary

| Action | Allowed When | Forbidden When |
|------|-------------|----------------|
| **Cancel** | FileStep = `In Queue` | All other steps |
| **Download** | FileStep = `RAG generated` | All other steps |

---

## TypeFile Reference

| TypeFile | Description | Status |
|--------|-------------|--------|
| **balance** | Balance sheet input files for financial processing. | Active |
| **rag** | Generated artifacts for RAG / AI pipelines. | Active |

---

## Lifecycle Rules
- `FileStep` defines **where the file is in the pipeline**
- `FileStates` defines **if the system considers the record usable**
- A file **must never transition from Inactive back to Active**
- Any new FileStep **must be documented here before implementation**

---

## Audit Expectations

| FileStep | Required Audit Event |
|--------|----------------------|
| In Queue | Upload requested |
| Processing | Processing started |
| Processed | Processing completed |
| RAG generated | Artifact generated |
| Error | Failure logged |
| Cancelled | Upload cancelled by user |

---

## Governance
- This document is **normative**
- Screens and Jobs must comply with these rules
- Tests and reviews should reference this file

---

## Changelog
- **2026-01-04:** initial version — AndreGregoletto
