# Admin Batch 2 Data SPMB Pages Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Bring Data SPMB admin pages into the same command-center UI system used by the dashboard and Batch 1.

**Architecture:** Reuse `admin-dashboard.css` primitives from Batch 1 and add only small page-specific CSS where needed. Preserve all existing controllers, form actions, modal IDs, table IDs, and JavaScript hooks.

**Tech Stack:** CodeIgniter 4 PHP views, Bootstrap 5, Lucide, DataTables, Select2, Flatpickr, Playwright.

---

## File Structure

- Modify: `public/assets/css/admin-dashboard.css`
  - Add shared card/list helpers for Data SPMB pages.
- Modify: `app/Views/admin/academic_years/index.php`
  - Add shared shell/header/panel classes without removing the existing mature academic-year styling.
- Modify: `app/Views/admin/jalur/index.php`
  - Convert route cards into shared page shell, header, responsive cards, and improved edit modal.
- Modify: `app/Views/admin/jalur/gelombang.php`
  - Convert mixed `sp-*` wrapper into shared shell/header/panels while keeping modal JS hooks.
- Modify: `app/Views/admin/document_requirements/index.php`
  - Convert scope/add form and requirements list into shared panels.
- Modify: `app/Views/admin/announcements/seleksi.php`
  - Convert selection list into shared header/filter/table/mobile record layout.
- Create: `tests/browser/audit_admin_batch2_data_spmb_pages.js`
  - Authenticate, inspect desktop/mobile layout, modal behavior, filter/table behavior, and route interaction health.

## Tasks

- [ ] Write the failing Playwright audit requiring `.admin-page-shell`, `.admin-page-header`, and secondary panels on all Batch 2 routes.
- [ ] Run the audit and confirm RED.
- [ ] Add small shared CSS helpers for route quota cards, compact forms, and selection mobile records.
- [ ] Update `academic_years/index.php` to expose shared classes while preserving its existing modal/table behavior.
- [ ] Update `jalur/index.php`; preserve `.edit-jalur-btn`, `#editJalurModal`, and form actions.
- [ ] Update `gelombang.php`; preserve `.edit-gelombang-btn`, `#editGelombangModal`, Flatpickr fields, and delete forms.
- [ ] Update `document_requirements/index.php`; preserve GET scope filter and POST store/update/delete forms.
- [ ] Update `announcements/seleksi.php`; preserve `#seleksiTable`, ranking POST, and status update forms.
- [ ] Run `node tests/browser/audit_admin_batch2_data_spmb_pages.js`.
- [ ] Run `node tests/browser/audit_admin_pages.js`.
- [ ] Run PHP lint for all changed views and relevant controllers.
- [ ] Attempt Browser plugin validation; use Playwright screenshots if Browser login remains blocked by virtual clipboard.

## Self-Review

- Spec coverage: Covers all Batch 2 Data SPMB admin pages, mobile-first layout, smaller subtitles, modal/table UX, route-level backend smoke checks.
- Placeholder scan: No unspecified implementation placeholders remain.
- Type/name consistency: Selectors match existing route/view IDs and Batch 1 shared class names.
