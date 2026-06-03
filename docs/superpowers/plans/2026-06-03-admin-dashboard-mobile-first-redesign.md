# Admin Dashboard Mobile-First Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the approved first-pass mobile-first admin command center without changing database schema or breaking existing routes.

**Architecture:** Keep CodeIgniter 4 MVC boundaries by preparing dashboard data in `DashboardController`, rendering with small view components, and styling with scoped admin dashboard classes layered on the existing design system. Fix the shared dashboard shell only where required for mobile navigation and console health.

**Tech Stack:** CodeIgniter 4, PHP 8.2, Bootstrap 5, Lucide icons, ApexCharts, existing browser audit scripts.

---

### Task 1: Shell Critical Fixes

**Files:**
- Modify: `app/Views/layouts/dashboard.php`
- Modify: `public/assets/css/dashboard.css`

- [ ] Replace unsupported Lucide `images` with `image`.
- [ ] Change admin mobile bottom nav labels to Dashboard, Pendaftar, Verifikasi, Seleksi, Lainnya.
- [ ] Route Pendaftar to `operator/registrants` and Verifikasi to `operator/registrants?status=submitted` until dedicated admin routes exist.
- [ ] Fix mobile sidebar bottom sheet visibility when `.sidebar.show` is set.
- [ ] Remove desktop horizontal overflow from the dashboard shell.
- [ ] Verify with browser at 390px and 1366px.

### Task 2: Dashboard Data Contract

**Files:**
- Modify: `app/Controllers/Admin/DashboardController.php`

- [ ] Prepare `summaryCards`, `priorityTasks`, `funnelSteps`, `quotaUsage`, `verificationQueue`, `activityItems`, and `quickActions`.
- [ ] Use current tables and model methods only.
- [ ] Avoid fake metrics; use conservative derived values and truthful fallback labels.
- [ ] Verify `php -l app/Controllers/Admin/DashboardController.php`.

### Task 3: Reusable Dashboard Components

**Files:**
- Create: `app/Views/components/stat_card.php`
- Create: `app/Views/components/status_badge.php`
- Create: `app/Views/components/empty_state.php`

- [ ] Add escaped, parameterized stat card component.
- [ ] Add semantic status badge component.
- [ ] Add dashboard empty-state component.

### Task 4: Command-Center Dashboard View

**Files:**
- Modify: `app/Views/admin/dashboard.php`

- [ ] Replace the generic four-card dashboard with command-center sections.
- [ ] Render five executive cards.
- [ ] Render priority tasks, admission funnel, quota usage, verification queue, activity timeline, and quick actions.
- [ ] Keep ApexCharts only in the dashboard section script when trend data exists.
- [ ] Verify `php -l app/Views/admin/dashboard.php`.

### Task 5: Scoped Admin Dashboard Styling

**Files:**
- Create: `public/assets/css/admin-dashboard.css`
- Modify: `app/Views/layouts/dashboard.php`

- [ ] Add scoped premium dashboard styles.
- [ ] Add mobile card/list/funnel rules for 360px to 414px.
- [ ] Add dark mode surface/badge rules.
- [ ] Include the new CSS after `dashboard.css`.

### Task 6: Verification

**Files:**
- No production edits unless failures are found.

- [ ] Run PHP syntax checks on touched PHP files.
- [ ] Run relevant browser checks for `/admin/dashboard`.
- [ ] Test desktop 1366, tablet 768, mobile 390 and 360.
- [ ] Test mobile `Lainnya` drawer open/close.
- [ ] Check console warnings/errors.
- [ ] Report remaining gaps.
