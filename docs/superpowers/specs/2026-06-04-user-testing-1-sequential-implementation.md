# User Testing 1 Sequential Implementation Design

## Goal

Implement the `user-testing-1.md` findings in the agreed order: Phase A core first, Phase B platform expansion, then Phase C stabilization and reporting.

## Context

The CI4 app already has RBAC, active academic year, configurable document requirements, applicant registration steps, public homepage/SPMB pages, and operator document verification. The working tree is dirty from earlier dashboard/admin work, so every change must be narrow, additive, and avoid reverting unrelated files.

## Phase A: Core First

Phase A makes the applicant journey reliable before adding larger modules.

- Registration validates empty email with a normal field error and shows helper text explaining acceptable family email use.
- The registration wizard keeps official school identity visible while removing public navigation distractions.
- The wizard gains a safe `Kembali ke Beranda` action. If a step has a form, it attempts the same AJAX save before navigating home.
- Draft state is made explicit in the applicant dashboard with last-save/progress language derived from existing saved step data.
- Step 8 finalization unlocks when required configured documents have uploaded records for the active academic year and selected jalur scope. It must not use hard-coded legacy document types.
- Schedule gates are enforced in UI and backend using active academic year and gelombang dates.
- Homepage/SPMB fee and payment FAQ content reads from admin-controlled data instead of static “gratis” text.
- Social links render only when configured.

## Phase B: Full Platform Expansion

Phase B adds durable product modules once the core journey is stable.

- Fee management: `fee_types` with active, visible, required, optional, pre-form-payment, auto-invoice, nominal, sort, and homepage metadata.
- Billing: `invoices`, `invoice_items`, `payments`, `payment_methods`, `payment_logs`, invoice status transitions, manual verification, partial payments, cancellation, slip PDF, and export.
- Bendahara role and permissions integrate with the existing RBAC tables.
- Registration form/payment gate supports admin/operator override with reason and audit log.
- Religion/sub-religion admin configuration replaces hard-coded religion lists while preserving national parent religion labels.
- Indonesian region tables support cascading province, regency/city, district, village dropdowns.
- Map/GPS picker stores latitude, longitude, automatic/manual distance, automatic/manual duration, transport mode, and calculation source with a routing-provider abstraction plus haversine fallback.

## Phase C: Stabilization

Phase C verifies all findings end to end.

- Run unit tests for changed services/controllers.
- Run browser checks for homepage, registration, dashboard, wizard, upload, and admin/bendahara pages.
- Test responsive widths: 360, 390, 414, 768, 1024, and 1366 pixels.
- Fix remaining blockers, console errors, mobile horizontal scroll, empty/loading states, and inconsistent status labels.
- Produce the final report requested in the user prompt.

## Testing Strategy

Use TDD for behavior changes: write failing unit/controller tests first, implement minimal code, then run targeted suites. Browser QA follows each phase, with screenshots or notes for important responsive states.

## Risks

Payment gateway, routing API, WhatsApp, and email delivery require external credentials. The implementation must provide service abstractions and safe fallbacks where credentials are absent.
