# Admin Batch 1 System Pages Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Polish the admin system pages (`users`, `access`, `settings`, `backup`) so they share the dashboard command-center visual system, have smaller card/table subtitles, and pass desktop/mobile UI audits.

**Architecture:** Keep changes page-scoped and CSS-driven. Reuse the existing `admin-dashboard.css` dashboard primitives by adding secondary page utilities, then apply those utilities to the four Batch 1 views without changing controller behavior.

**Tech Stack:** CodeIgniter 4 PHP views, Bootstrap 5, Lucide, jQuery, DataTables, Playwright browser audits.

---

## File Structure

- Modify: `public/assets/css/admin-dashboard.css`
  - Add shared secondary admin page primitives: `.admin-page-shell`, `.admin-page-header`, `.admin-page-grid`, `.admin-secondary-panel`, `.admin-section-title`, `.admin-section-subtitle`, `.admin-filter-panel`, `.admin-mobile-record-list`, responsive settings tab utilities, and dark mode support.
- Modify: `app/Views/admin/users/index.php`
  - Replace ad hoc header/cards with shared page shell, filter panel, table panel, and mobile records classes.
- Modify: `app/Views/admin/access/index.php`
  - Replace page header and local card styling with shared page shell and panels while preserving permission form behavior.
- Modify: `app/Views/admin/settings/index.php`
  - Normalize the header, tab shell, tab card, section headings, and sticky save footer to the shared system.
- Modify: `app/Views/admin/backup/index.php`
  - Normalize backup/restore panels, warning section, and touch-friendly destructive confirmation controls.
- Create: `tests/browser/audit_admin_batch1_system_pages.js`
  - Authenticate as admin and verify layout, no horizontal overflow, font hierarchy, mobile record behavior, settings tab usability, backup confirmation behavior, and JS console health.

## Task 1: Batch 1 Playwright Audit

**Files:**
- Create: `tests/browser/audit_admin_batch1_system_pages.js`

- [ ] **Step 1: Write the failing test**

Create a Playwright script that logs in and checks these routes:

```javascript
const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8080';
const routes = [
  { path: '/admin/users', title: 'Kelola Pengguna' },
  { path: '/admin/access', title: 'Mode & Hak Akses' },
  { path: '/admin/settings', title: 'Konfigurasi Sistem' },
  { path: '/admin/backup', title: 'Backup & Restore Database' },
];

async function login(page) {
  await page.goto(`${BASE_URL}/auth/login`, { waitUntil: 'domcontentloaded' });
  await page.fill('#email', 'admin@smartspmbpro.sch.id');
  await page.fill('#password', 'Admin@12345');
  await page.click('button[type="submit"]');
  await page.waitForURL(/\/admin\/dashboard|\/dashboard/, { timeout: 10000 }).catch(() => page.waitForTimeout(1500));
}

async function collectLayout(page) {
  return page.evaluate(() => {
    const shell = document.querySelector('.admin-page-shell');
    const header = document.querySelector('.admin-page-header');
    const h1 = header?.querySelector('h1');
    const subtitle = header?.querySelector('p');
    const sectionTitle = document.querySelector('.admin-section-title');
    const sectionSubtitle = document.querySelector('.admin-section-subtitle, .admin-panel__kicker');
    const doc = document.documentElement;
    return {
      shell: !!shell,
      header: !!header,
      h1Size: h1 ? parseFloat(getComputedStyle(h1).fontSize) : 0,
      subtitleSize: subtitle ? parseFloat(getComputedStyle(subtitle).fontSize) : 0,
      sectionTitleSize: sectionTitle ? parseFloat(getComputedStyle(sectionTitle).fontSize) : 0,
      sectionSubtitleSize: sectionSubtitle ? parseFloat(getComputedStyle(sectionSubtitle).fontSize) : 0,
      overflowX: doc.scrollWidth - doc.clientWidth,
      buttonHeights: [...document.querySelectorAll('main .btn, #main-content .btn')].slice(0, 20).map((el) => el.getBoundingClientRect().height),
    };
  });
}

async function auditRoute(page, route) {
  const problems = [];
  await page.goto(`${BASE_URL}${route.path}`, { waitUntil: 'load', timeout: 30000 });
  await page.waitForTimeout(700);

  const titleVisible = await page.getByRole('heading', { name: route.title }).first().isVisible().catch(() => false);
  if (!titleVisible) problems.push(`missing heading ${route.title}`);

  const layout = await collectLayout(page);
  if (!layout.shell) problems.push('missing .admin-page-shell');
  if (!layout.header) problems.push('missing .admin-page-header');
  if (layout.overflowX > 2) problems.push(`desktop horizontal overflow ${layout.overflowX}px`);
  if (layout.subtitleSize && layout.h1Size && layout.subtitleSize >= layout.h1Size) problems.push('page subtitle is not smaller than title');
  if (layout.sectionSubtitleSize && layout.sectionTitleSize && layout.sectionSubtitleSize >= layout.sectionTitleSize) problems.push('section subtitle is not smaller than section title');
  if (layout.buttonHeights.some((height) => height > 0 && height < 32)) problems.push('button target below 32px on desktop');

  await page.setViewportSize({ width: 390, height: 844 });
  await page.reload({ waitUntil: 'load' });
  await page.waitForTimeout(700);
  const mobile = await collectLayout(page);
  if (mobile.overflowX > 2) problems.push(`mobile horizontal overflow ${mobile.overflowX}px`);
  if (mobile.buttonHeights.some((height) => height > 0 && height < 40)) problems.push('button target below 40px on mobile');

  await page.setViewportSize({ width: 1366, height: 900 });
  return problems;
}
```

- [ ] **Step 2: Verify RED**

Run:

```powershell
node tests/browser/audit_admin_batch1_system_pages.js
```

Expected: FAIL because at least one Batch 1 page does not yet render `.admin-page-shell` and `.admin-page-header`.

## Task 2: Shared Secondary Page CSS

**Files:**
- Modify: `public/assets/css/admin-dashboard.css`
- Test: `tests/browser/audit_admin_batch1_system_pages.js`

- [ ] **Step 1: Add shared CSS primitives**

Add secondary page classes after the existing dashboard header/action definitions:

```css
.admin-page-shell {
    display: grid;
    gap: 18px;
    max-width: 1480px;
    margin: 0 auto;
}

.admin-page-header {
    align-items: flex-start;
    background: var(--sp-card-bg);
    border: 1px solid var(--sp-card-border);
    border-left: 4px solid var(--sp-primary);
    border-radius: 8px;
    box-shadow: var(--sp-shadow-card);
    display: flex;
    gap: 16px;
    justify-content: space-between;
    padding: 22px;
}

.admin-page-header h1 {
    color: var(--sp-heading-color);
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: clamp(1.42rem, 2.1vw, 1.9rem);
    font-weight: 800;
    line-height: 1.15;
    margin: 0 0 6px;
}

.admin-page-header p,
.admin-section-subtitle {
    color: var(--sp-text-muted);
    font-size: 0.88rem;
    line-height: 1.55;
    margin: 0;
}

.admin-page-actions {
    display: flex;
    flex: 0 0 auto;
    flex-wrap: wrap;
    gap: 10px;
}
```

- [ ] **Step 2: Run the audit**

Run:

```powershell
node tests/browser/audit_admin_batch1_system_pages.js
```

Expected: Still FAIL until markup uses the classes on each Batch 1 page.

## Task 3: Users Page

**Files:**
- Modify: `app/Views/admin/users/index.php`
- Test: `tests/browser/audit_admin_batch1_system_pages.js`

- [ ] **Step 1: Apply shared layout**

Wrap the page with:

```php
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-users-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Manajemen Akun</p>
            <h1 id="admin-users-title">Kelola Pengguna</h1>
            <p>Kelola hak akses dan akun untuk Administrator, Operator, dan Pendaftar.</p>
        </div>
        <div class="admin-page-actions">
            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
                <i data-lucide="user-plus" class="me-2" style="width: 16px; height: 16px;"></i> Tambah Pengguna
            </a>
        </div>
    </header>
</section>
```

- [ ] **Step 2: Normalize filter/list panels**

Use `.admin-filter-panel` for filters, `.admin-secondary-panel.users-table-card` for the table, and `.admin-mobile-record-list` for mobile cards.

- [ ] **Step 3: Run the audit**

Run:

```powershell
node tests/browser/audit_admin_batch1_system_pages.js
```

Expected: Users page passes the shared layout checks.

## Task 4: Access Page

**Files:**
- Modify: `app/Views/admin/access/index.php`
- Test: `tests/browser/audit_admin_batch1_system_pages.js`

- [ ] **Step 1: Apply shared layout**

Change the page root to `.admin-page-shell`, replace the header with `.admin-page-header`, and change role cards to `.admin-secondary-panel`.

- [ ] **Step 2: Preserve permission counter behavior**

Keep `.js-role-permission-form`, `.js-permission-counter`, and `.js-permission-checkbox` unchanged so the existing script still updates the live counters.

- [ ] **Step 3: Run the audit**

Run:

```powershell
node tests/browser/audit_admin_batch1_system_pages.js
```

Expected: Access page passes the shared layout checks and permission counters still update after checkbox changes.

## Task 5: Settings Page

**Files:**
- Modify: `app/Views/admin/settings/index.php`
- Test: `tests/browser/audit_admin_batch1_system_pages.js`

- [ ] **Step 1: Apply shared layout**

Change the root to `.admin-page-shell`, header to `.admin-page-header`, navigation container to `.admin-settings-layout`, tabs wrapper to `.admin-settings-tabs-panel`, and content card to `.admin-secondary-panel`.

- [ ] **Step 2: Normalize panel headings**

Replace tab panel heading `h5` elements with `h2.admin-section-title` and keep explanatory text as `.admin-section-subtitle`.

- [ ] **Step 3: Run the audit**

Run:

```powershell
node tests/browser/audit_admin_batch1_system_pages.js
```

Expected: Settings page passes layout checks and all five tabs remain clickable.

## Task 6: Backup Page

**Files:**
- Modify: `app/Views/admin/backup/index.php`
- Test: `tests/browser/audit_admin_batch1_system_pages.js`

- [ ] **Step 1: Apply shared layout**

Replace the Bootstrap row/card-first structure with `.admin-page-shell`, `.admin-page-header`, `.admin-page-grid.admin-page-grid--two`, and `.admin-secondary-panel`.

- [ ] **Step 2: Preserve destructive confirmation**

Keep `#confirm` and `#restore-btn` IDs unchanged. The test expects the restore button to start disabled and become enabled after checking the confirmation switch.

- [ ] **Step 3: Run the audit**

Run:

```powershell
node tests/browser/audit_admin_batch1_system_pages.js
```

Expected: Backup page passes layout checks and restore confirmation behavior passes.

## Task 7: Full Verification

**Files:**
- Verify: `app/Views/admin/users/index.php`
- Verify: `app/Views/admin/access/index.php`
- Verify: `app/Views/admin/settings/index.php`
- Verify: `app/Views/admin/backup/index.php`
- Verify: `public/assets/css/admin-dashboard.css`
- Verify: `tests/browser/audit_admin_batch1_system_pages.js`

- [ ] **Step 1: Run Batch 1 audit**

Run:

```powershell
node tests/browser/audit_admin_batch1_system_pages.js
```

Expected: PASS for all four routes.

- [ ] **Step 2: Run broader admin smoke audit**

Run:

```powershell
node tests/browser/audit_admin_pages.js
```

Expected: all existing admin page smoke checks pass.

- [ ] **Step 3: Lint changed PHP views**

Run:

```powershell
php -l app/Views/admin/users/index.php
php -l app/Views/admin/access/index.php
php -l app/Views/admin/settings/index.php
php -l app/Views/admin/backup/index.php
```

Expected: `No syntax errors detected` for each file.

- [ ] **Step 4: Browser plugin validation**

Use the in-app Browser first for `/admin/users`, `/admin/access`, `/admin/settings`, and `/admin/backup`. If Browser cannot authenticate or interact because the Browser virtual clipboard/runtime is unavailable, record the failure and use Playwright screenshots as the fallback evidence.

## Self-Review

- Spec coverage: The plan covers mobile-first admin page layout, smaller subtitles, Batch 1 admin pages, UI/UX audit, and backend-preserving behavior checks.
- Placeholder scan: No `TBD`, `TODO`, or unspecified implementation placeholders remain.
- Type/name consistency: CSS class names and test selectors are consistent across tasks.
