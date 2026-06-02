# Smart SPMB Pro Admin Dashboard Mobile-First Redesign

Date: 2026-06-03
Branch: feature/admin-dashboard-mobile-first-redesign
Scope: shared dashboard shell, `/admin/dashboard`, and reusable admin UI components.

## Decision

This first implementation pass will redesign the shared admin shell and `/admin/dashboard` into a mobile-first enterprise command center. Secondary admin pages will consume the same tokens and components later, but they are not the primary rewrite target for this pass.

This keeps the blast radius controlled while still fixing the highest-impact mobile and dashboard problems.

## Current Audit Scores

- Overall UI score: 74/100
- Mobile UX score: 72/100
- Enterprise readiness score: 68/100
- Accessibility score: 78/100
- Performance score: 62/100
- Dark mode score: 73/100

## Audit Evidence

Browser audit target: `http://localhost:8080/admin/dashboard`

Authenticated as the existing browser-test admin account.

Observed viewport results:

- 1366 desktop: page loads, sidebar visible, but document has 15px horizontal overflow.
- 390 mobile: bottom navigation exists and has 44px+ targets, but primary labels are `Beranda`, `Tahun`, `Seleksi`, `Konten`, `Lainnya`, so `Pendaftar` and `Verifikasi` are missing from primary mobile admin navigation.
- 390 mobile: tapping `Lainnya` toggles sidebar classes and overlay, but the sidebar sheet remains below the viewport (`sidebarTop` around 856 on an 844px viewport), making the drawer unusable.
- Dashboard has 4 KPI cards, but the requested decision set needs 5 cards: Total Pendaftar, Menunggu Verifikasi, Perlu Perbaikan, Diterima Sementara, Siap Dapodik.
- Dashboard loads DataTables, Select2, Flatpickr, SweetAlert2, and Apex assets globally from the shared layout even when pages do not need each library.
- Dashboard has about 92 inline `style` attributes, mainly in the layout and dashboard view.
- Console warns that `data-lucide="images"` is not available in the loaded Lucide version.

## Prioritized Issues

### 1. Mobile Drawer Opens Below Viewport

Severity: Critical

Category: Mobile / UX

Issue: The `Lainnya` bottom-nav button marks the sidebar as open, but the sidebar sheet is still positioned outside the visible mobile viewport.

Evidence: Browser check at 390x844 on `/admin/dashboard`; after clicking `#dashboard-more-toggle`, `sidebarShown=true`, `overlayShown=true`, but `visibleEnough=false` and `sidebarTop` is below the viewport.

Why it matters: Mobile admins lose access to secondary navigation even though the UI says the drawer is open.

Fix: Rework mobile sidebar positioning into a true bottom sheet with stable `inset`, `transform`, `max-height`, and open/closed states. Ensure `aria-expanded`, overlay, focus return, and Escape behavior are consistent.

Expected impact: Transformative

### 2. Dashboard IA Does Not Answer the Admin's First Questions

Severity: High

Category: UX / Enterprise Readiness

Issue: The first screen shows generic stats and charting, but it does not prioritize pending verification, incomplete documents, selection approval, re-registration, quota risk, or Dapodik readiness.

Evidence: `/admin/dashboard` currently renders Total Pendaftar, Diterima, Berkas Lengkap, and Belum Lengkap only.

Why it matters: School operators need to know what to do next, not just see decorative summaries.

Fix: Replace the top dashboard with an executive command surface: 5 actionable KPI cards, a priority task panel, admission funnel, quota usage, verification queue, activity timeline, and quick actions.

Expected impact: Transformative

### 3. Mobile Bottom Navigation Is Present but Misprioritized

Severity: High

Category: Mobile / IA

Issue: Mobile nav omits the requested primary admin actions `Pendaftar` and `Verifikasi`.

Evidence: Mobile labels are `Beranda`, `Tahun`, `Seleksi`, `Konten`, `Lainnya`.

Why it matters: The highest-frequency operational actions are not thumb-reachable.

Fix: Change admin mobile bottom nav to `Dashboard`, `Pendaftar`, `Verifikasi`, `Seleksi`, `Lainnya`. Route `Pendaftar` to the operator registrants surface if no admin-specific registrants route exists, and route `Verifikasi` to the most relevant document verification queue available in this pass.

Expected impact: High

### 4. Global Asset Loading Hurts Performance

Severity: High

Category: Performance / Code Quality

Issue: The dashboard layout loads DataTables, Select2, Flatpickr, SweetAlert2, and ApexCharts globally.

Evidence: Browser asset inspection on `/admin/dashboard` shows all those scripts and styles loaded.

Why it matters: Every dashboard page pays for libraries it may not use, slowing mobile loads and increasing failure surface.

Fix: Add layout sections or per-page flags for vendor assets. Load ApexCharts only on dashboard when chart data exists, DataTables only on table pages, Select2 only when `.select2` is present, and Flatpickr only when date inputs exist.

Expected impact: High

### 5. Too Many Inline Styles

Severity: Medium

Category: Code Quality / Maintainability

Issue: Core shell and dashboard visual behavior is scattered across inline `style` attributes.

Evidence: Browser audit counted about 92 inline styled elements on `/admin/dashboard`.

Why it matters: Inline styles make dark mode, responsive rules, tokens, and component reuse harder to maintain.

Fix: Move core shell/dashboard styles into design-token and component CSS files. Keep inline style only for unavoidable dynamic values such as progress widths.

Expected impact: High

### 6. Unsupported Lucide Icon Name

Severity: Medium

Category: UI / Console Health

Issue: `data-lucide="images"` causes repeated Lucide warnings.

Evidence: Browser console shows `icon name was not found`.

Why it matters: It creates noisy console output and leaves icons inconsistent.

Fix: Replace `images` with a supported icon such as `image`, `gallery-horizontal`, or `gallery-thumbnails` depending on context.

Expected impact: Medium

### 7. Dark Mode Is Broad but Not Fully Enterprise-Layered

Severity: Medium

Category: Dark Mode / Accessibility

Issue: Dark mode exists, but dashboard surfaces still rely on many Bootstrap classes and one-off overrides.

Evidence: Dark mode tokens exist in `foundation.css` and `dashboard.css`, while markup uses many `text-dark`, `bg-light`, and inline color styles.

Why it matters: Enterprise dark mode needs deliberate surface layering, contrast, status colors, and chart adaptation.

Fix: Introduce dashboard-specific surface tokens and semantic status badges that work in both themes. Replace dashboard `text-dark` and inline color usage where practical.

Expected impact: Medium

## Redesign Approach

Recommended approach: staged command-center redesign.

Phase 1 focuses on the shared admin shell and dashboard. It preserves CodeIgniter MVC boundaries, current routes, controllers, models, and backend behavior. It improves the dashboard by adding view components, richer computed dashboard data, mobile navigation fixes, and component CSS.

This is preferred over a full admin-page rewrite because the repository has many active uncommitted changes. A full rewrite would be risky and harder to validate.

## Information Architecture

The first screen must answer:

- How many pendaftar exist today and this year?
- How many need verification?
- How many need data or document repair?
- How many are accepted, pending, or rejected?
- Is quota almost full by jalur?
- What should the admin do next?
- Which operational action is one tap away?

Dashboard sections:

1. Mobile app header / desktop command header
2. Executive summary cards
3. Priority task panel
4. Admission funnel
5. Quota usage by jalur
6. Verification queue
7. Activity timeline
8. Quick actions

## Component Architecture

Create or refactor these reusable view components:

- `app/Views/components/page_header.php`
- `app/Views/components/stat_card.php`
- `app/Views/components/task_panel.php`
- `app/Views/components/status_badge.php`
- `app/Views/components/empty_state.php`
- `app/Views/components/mobile_card_list.php`
- `app/Views/components/filter_drawer.php`
- `app/Views/components/bottom_nav.php`
- `app/Views/components/breadcrumb.php`

CSS files:

- `public/assets/css/design-tokens.css`
- `public/assets/css/components.css`
- `public/assets/css/admin-dashboard.css`
- `public/assets/css/admin-mobile.css`
- `public/assets/css/dark-mode.css`

JS files:

- `public/assets/js/admin-dashboard.js`
- `public/assets/js/mobile-navigation.js`
- `public/assets/js/theme-manager.js`
- keep `public/assets/js/command-palette.js`, but reduce repeated `lucide.createIcons()` calls.

Existing `foundation.css` and `dashboard.css` can remain during the transition, but new admin code should move toward the files above.

## Dashboard Data Contract

The controller should prepare data for the view instead of putting business logic inside the view.

Dashboard data groups:

- `summaryCards`
- `priorityTasks`
- `funnelSteps`
- `quotaUsage`
- `verificationQueue`
- `activityItems`
- `quickActions`

The first pass may use currently available model methods and conservative derived counts. If a requested metric is not available without schema changes, show a truthful empty or fallback state with a clear action rather than fake data.

## Mobile Behavior

At 360px to 414px:

- No horizontal scroll.
- Mobile header is compact: school logo/title, search, notification/profile/theme actions.
- Bottom nav shows `Dashboard`, `Pendaftar`, `Verifikasi`, `Seleksi`, `Lainnya`.
- `Lainnya` opens a visible, accessible bottom sheet.
- KPI cards stack or become a horizontal snap list.
- Verification queue is a card list, not a desktop table.
- Quick actions are a compact grid with 44px+ touch targets.
- Modals used by dashboard shell should behave like sheets where practical.

## Accessibility Requirements

- Keep skip link.
- Use semantic landmarks.
- Ensure bottom sheet has usable focus behavior and Escape/overlay dismissal.
- Add `aria-current` to active nav items.
- Keep 44px minimum interactive targets.
- Use visible `:focus-visible` states.
- Ensure status badges meet WCAG AA contrast in light and dark mode.
- Avoid icon-only buttons without accessible names.

## Performance Requirements

- Do not load DataTables globally.
- Do not load Select2 globally.
- Do not load Flatpickr globally.
- Load ApexCharts only when the dashboard chart is rendered.
- Prefer local first-party assets or existing local fallbacks where possible.
- Avoid repeated global Lucide initialization; initialize once after static render and after known dynamic palette updates.

## Phased Implementation Plan

Phase 1: Critical fixes

- Fix mobile sidebar bottom-sheet open state.
- Replace unsupported Lucide `images` icon.
- Remove desktop horizontal overflow.
- Make admin bottom nav match required labels and routes.

Phase 2: Mobile-first dashboard redesign

- Move dashboard calculations out of `app/Views/admin/dashboard.php` into `DashboardController`.
- Add reusable components for page header, stat cards, task panel, status badge, empty state, mobile card list, and bottom nav.
- Replace the current dashboard view with command-center sections.
- Add mobile-first KPI, task, queue, and quick-action layouts.

Phase 3: Premium enterprise polish

- Add dashboard-specific tokens, component classes, and dark-mode surface rules.
- Standardize badge variants for Draft, Submitted, Verified, Need Revision, Accepted, Rejected, and Dapodik Ready.
- Improve command palette visual density and mobile presentation.
- Add loading and empty states for dashboard sections.

Phase 4: Performance and accessibility hardening

- Gate vendor CSS/JS per page.
- Reduce inline styles.
- Verify keyboard navigation, focus return, and console health.
- Test 1366, 1024, 768, 414, 390, and 360 viewports.

## Test Plan

Browser and command checks:

- Login as admin.
- Visit `/admin/dashboard`.
- Verify desktop 1366 and 1024.
- Verify tablet 768.
- Verify mobile 414, 390, and 360.
- Open and close mobile `Lainnya` drawer.
- Use bottom nav links.
- Open command palette with Ctrl+K and search menu items.
- Toggle dark mode.
- Confirm no horizontal overflow.
- Confirm no relevant console errors or Lucide icon warnings.
- Run existing browser audit scripts where possible.
- Run PHP unit tests if the edited backend surface affects controller/model behavior.

## Out of Scope for First Pass

- Full redesign of every secondary admin page.
- Database schema changes.
- Replacing Bootstrap.
- Rebuilding the public website.
- Integrating Sentry if no token/configuration exists.
- CodeRabbit review if the plugin is unavailable in this environment.
- Figma import if no Figma reference is provided.

## Remaining Risks

- Some requested metrics may not exist as first-class model methods yet.
- `/admin/registrants` and `/admin/logs` are not current admin routes; equivalent operator/admin surfaces may need route decisions in a later pass.
- The worktree contains many uncommitted changes, so implementation must avoid broad refactors and commit only intentional files.
