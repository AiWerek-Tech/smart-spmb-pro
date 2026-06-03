# User Testing 1 Sequential Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement User Testing 1 findings in order: Phase A core first, Phase B expansion, Phase C stabilization.

**Architecture:** Build on existing CodeIgniter 4 services, models, RBAC, settings, and document-requirement patterns. Add new tables/services only when existing settings or models cannot express the domain safely.

**Tech Stack:** CodeIgniter 4, PHP 8+, MySQL/MariaDB, Bootstrap 5/Sneat-style dashboard UI, JavaScript, SweetAlert2, DomPDF, PhpSpreadsheet.

---

### Task 1: Phase A Auth and Applicant Core

**Files:**
- Modify: `tests/unit/AuthControllerTest.php`
- Modify: `app/Controllers/Auth/AuthController.php`
- Modify: `app/Views/auth/register.php`
- Modify: `tests/unit/RegistrationServiceTest.php`
- Modify: `app/Services/RegistrationService.php`
- Modify: `app/Views/pendaftar/registration/wizard_layout.php`
- Modify: `app/Views/pendaftar/registration/step_8.php`
- Modify: `app/Views/pendaftar/dashboard.php`

- [ ] Add failing auth tests for empty email validation and helper text.
- [ ] Implement registration email validation messages and helper text.
- [ ] Add failing registration service tests for configured document requirements.
- [ ] Replace hard-coded Step 8 completion validation with `DocumentRequirementService`.
- [ ] Add safe back-to-home button that saves current step before navigating.
- [ ] Add official school identity to wizard header from settings.
- [ ] Add dashboard draft/progress copy from existing saved steps.
- [ ] Run targeted PHPUnit tests.

### Task 2: Phase A Schedule and Public Dynamic Content

**Files:**
- Create: `app/Services/RegistrationGateService.php`
- Modify: `tests/unit/RegistrationServiceTest.php`
- Modify: `app/Controllers/Pendaftar/RegistrationController.php`
- Modify: `app/Controllers/Pendaftar/DashboardController.php`
- Create: `app/Database/Migrations/2026-06-04-000001_CreateFeeTypesTable.php`
- Create: `app/Models/FeeTypeModel.php`
- Create: `app/Services/FeeService.php`
- Modify: `app/Controllers/Public/HomeController.php`
- Modify: `app/Controllers/Public/SpmbController.php`
- Modify: `app/Views/public/home.php`
- Modify: `app/Views/public/spmb.php`

- [ ] Add failing tests for closed/not-open gelombang gates.
- [ ] Implement backend registration schedule gate.
- [ ] Surface schedule state on dashboard and wizard.
- [ ] Add fee types migration/model/service.
- [ ] Replace static SPMB fee cards and payment FAQ with fee service output.
- [ ] Render social icons only for configured links.
- [ ] Run targeted tests and public browser smoke checks.

### Task 3: Phase B Billing and Bendahara

**Files:**
- Create migrations/models/services/controllers/views for `invoices`, `invoice_items`, `payments`, `payment_methods`, and `payment_logs`.
- Modify RBAC seeders to add Bendahara role and payment permissions.
- Modify registration flow to auto-create invoices from active fee types.
- Add slip PDF and payment export endpoints.

- [ ] Add invoice/payment status tests.
- [ ] Implement invoice generation service.
- [ ] Implement bendahara list/detail/manual verification/cancel/slip/export.
- [ ] Connect pre-form-payment fees to registration gates with override support.
- [ ] Run tests and browser checks.

### Task 4: Phase B Form Data Expansion

**Files:**
- Create migrations/models/admin controllers/views for religions, religion subgroups, and Indonesian regions.
- Modify student/address/family migrations with safe additive columns.
- Modify registration steps 1, 2, 3, 4, 5, and 6.
- Create map/distance service abstraction.

- [ ] Add tests for sub-religion selection and “Lainnya” occupation persistence.
- [ ] Add region tables/import-ready seeders and cascading API endpoints.
- [ ] Add map/GPS picker and distance/duration fallback.
- [ ] Run tests and responsive browser checks.

### Task 5: Phase C Stabilization

**Files:**
- Modify affected views/services/controllers from Tasks 1-4 as bugs are found.
- Create final report in `docs/superpowers/reports/2026-06-04-user-testing-1-report.md`.

- [ ] Run full PHPUnit or targeted suite if full DB is unavailable.
- [ ] Run browser QA for public, auth, pendaftar, upload, admin, and bendahara workflows.
- [ ] Test viewport widths 360, 390, 414, 768, 1024, and 1366.
- [ ] Fix residual issues.
- [ ] Produce final report with changed files, migrations, test steps, and risks.
