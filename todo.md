# Insaf CRM - Roadmap & TODO

## ✅ Completed Modules

### Search Student (All-in-One Directory & Search)
> **Status:** Implemented (`/dashboard/search-student`)
- [x] **Database Migration**: Added `remarks` (string) and `notes` (text) to `students` table (`2026_11_09_000001_add_remarks_and_notes_to_students_table.php`).
- [x] **Controller & Routes**: `StudentSearchController` with keyword search across personal, passport, and parental fields, plus country/university/payment filters.
- [x] **Instant Inline Save**: Instant AJAX auto-save for remarks dropdown and notes modal without page reloads.
- [x] **Compact 2-Line Filter Panel**: Fits search, countries, universities, remarks, payment status, and action buttons cleanly into exactly 2 lines.
- [x] **Sidebar Menu**: Top-level "Search Student" menu item and submenu entry in `sidebar.blade.php`.

### Dynamic Country Remarks & Pipeline Milestones
> **Status:** Implemented (`/dashboard/country-remarks`)
- [x] **Database Migration**: Created `country_remarks` table (`country_id`, `name`, `color`, `sort_order`, `is_active`).
- [x] **Eloquent Model**: `CountryRemark` with country relation, active/ordered scopes, and auto-fallback logic.
- [x] **Seeded Remarks**: Populated 35 exact country milestones for Malta (7), Malaysia (13 with EMGS %), Russia (8), and Default/Others (7).
- [x] **Admin Management UI**: Full CRUD interface at `/dashboard/country-remarks` with country tabs, color badges, status toggle, and 3-dot action dropdowns.
- [x] **Country Integration**: "Country Remarks" link added to sidebar and Country list header.
- [x] **Student Profile Integration**: Added current milestone remark badge and notes to student profile view (`students/{student}`).

---

## 📋 Planned Modules

### 1. Student All History (Individual)
> **Goal:** A centralized chronological timeline/audit trail tracking every event and milestone for an individual student throughout their journey.
- [ ] **Timeline View**: Visual chronological feed on the student detail page (`students/{student}`).
- [ ] **Event Logging**:
  - [ ] Stage & status transitions (Lead -> Counseling -> Application -> Visa -> Enrolled).
  - [ ] Document upload, replacement, and deletion logs.
  - [ ] Application submission & offer letter updates.
  - [ ] VFS appointment changes, checklist checks, and embassy results.
  - [ ] Payment receipts, invoices, and package adjustments.
  - [ ] Counselor / staff notes and call follow-up logs.
- [ ] *(Details to be added...)*

---

### 2. Learning Resource
> **Goal:** A resource management and distribution module for study materials, exam prep, guidelines, and templates.
- [ ] **Resource Categories**:
  - [ ] Test preparation (IELTS, PTE, TOEFL, GRE, GMAT).
  - [ ] Application writing samples (SOP, CV, Personal Statement, Recommendation Letters).
  - [ ] Embassy / Visa interview guidelines & mock questions per country.
  - [ ] Pre-departure & accommodation guides.
- [ ] **Access & Sharing**:
  - [ ] Internal consultant library vs. student portal downloadable resources.
  - [ ] File uploads (PDFs, docs, video links).
- [ ] *(Details to be added...)*

---

### 3. Search Program
> **Goal:** An intuitive course & program discovery tool to help counselors and students quickly find eligible academic programs across partner institutions.
- [ ] **Search & Filtering Engine**:
  - [ ] Destination country & university.
  - [ ] Study level (Foundation, Bachelor's, Master's, PhD, Diploma).
  - [ ] Subject / Discipline / Faculty.
  - [ ] Intake month & year.
  - [ ] Tuition fee range & currency.
  - [ ] Entry requirements (Minimum IELTS score, academic GPA / grade cutoff).
- [ ] **Comparison & Direct Apply**:
  - [ ] Compare selected programs side-by-side.
  - [ ] One-click application creation from search results for an active student.
- [ ] *(Details to be added...)*

---

## 📌 General Backlog / Notes
- [ ] **Modal Backdrop & Overlay Standardization (Full Viewport)**:
  - Ensure all modals CRM-wide have full-width/full-height backdrops that cover the entire viewport, including the top header and sidebar without leaking light shadows or leaving header/sidebar un-dimmed.
- *(Add future items and specifications here)*

