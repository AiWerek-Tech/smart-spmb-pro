const { chromium } = require('playwright');

const BASE_URL = process.env.BASE_URL || 'http://localhost:8080';
const OUTPUT_DIR = process.env.AUDIT_SCREENSHOT_DIR || null;

const ROUTES = [
  {
    name: 'Users',
    path: '/admin/users',
    heading: 'Kelola Pengguna',
    interact: async (page, problems) => {
      const hasDesktopTable = await page.locator('.users-table-card').count();
      const hasMobileList = await page.locator('.admin-mobile-record-list, .users-mobile-list').count();
      if (!hasDesktopTable) problems.push('missing users desktop table panel');
      if (!hasMobileList) problems.push('missing users mobile record list');
    },
  },
  {
    name: 'Users Create',
    path: '/admin/users/create',
    heading: 'Tambah Pengguna Baru',
    interact: async (page, problems) => {
      if (!(await page.locator('form[action*="/admin/users/store"]').count())) problems.push('users create form missing');
    },
  },
  {
    name: 'Access',
    path: '/admin/access',
    heading: 'Mode & Hak Akses',
    interact: async (page, problems) => {
      const form = page.locator('.js-role-permission-form').first();
      if (!(await form.count())) {
        return;
      }

      const counter = form.locator('.js-permission-counter');
      const checkbox = form.locator('.js-permission-checkbox:not([disabled])').first();
      if (!(await counter.count()) || !(await checkbox.count())) {
        problems.push('permission counter or editable checkbox missing');
        return;
      }

      const before = await counter.innerText();
      await checkbox.click();
      await page.waitForTimeout(100);
      const after = await counter.innerText();
      if (before === after) problems.push('permission counter did not update after checkbox toggle');
      await checkbox.click();
    },
  },
  {
    name: 'Settings',
    path: '/admin/settings',
    heading: 'Konfigurasi Sistem',
    interact: async (page, problems) => {
      for (const id of ['nav-contact-tab', 'nav-accreditation-tab', 'nav-theme-tab', 'nav-app-tab']) {
        await page.click(`#${id}`);
        await page.waitForTimeout(120);
        const selected = await page.locator(`#${id}`).getAttribute('aria-selected');
        if (selected !== 'true') problems.push(`settings tab ${id} did not become active`);
      }
    },
  },
  {
    name: 'Backup',
    path: '/admin/backup',
    heading: 'Backup & Restore Database',
    interact: async (page, problems) => {
      const restoreButton = page.locator('#restore-btn');
      const confirm = page.locator('#confirm');
      if (!(await restoreButton.count()) || !(await confirm.count())) {
        problems.push('backup restore confirmation controls missing');
        return;
      }

      if (!(await restoreButton.isDisabled())) problems.push('restore button should start disabled');
      await confirm.check();
      await page.waitForTimeout(100);
      if (await restoreButton.isDisabled()) problems.push('restore button stayed disabled after confirmation');
      await confirm.uncheck();
    },
  },
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
    const pageSubtitle = header?.querySelector('.admin-page-subtitle, p:not(.admin-panel__kicker)');
    const sectionTitle = document.querySelector('.admin-section-title');
    const sectionSubtitle = document.querySelector('.admin-section-subtitle');
    const doc = document.documentElement;
    const visibleButtons = [...document.querySelectorAll('#main-content .btn')]
      .filter((el) => {
        const rect = el.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0;
      })
      .slice(0, 30);

    return {
      shell: !!shell,
      header: !!header,
      secondaryPanelCount: document.querySelectorAll('.admin-secondary-panel, .admin-filter-panel').length,
      h1Size: h1 ? parseFloat(getComputedStyle(h1).fontSize) : 0,
      pageSubtitleSize: pageSubtitle ? parseFloat(getComputedStyle(pageSubtitle).fontSize) : 0,
      sectionTitleSize: sectionTitle ? parseFloat(getComputedStyle(sectionTitle).fontSize) : 0,
      sectionSubtitleSize: sectionSubtitle ? parseFloat(getComputedStyle(sectionSubtitle).fontSize) : 0,
      overflowX: doc.scrollWidth - doc.clientWidth,
      buttonHeights: visibleButtons.map((el) => el.getBoundingClientRect().height),
      navTabOverflow: (() => {
        const tabs = document.querySelector('#settings-tabs');
        return tabs ? tabs.scrollWidth - tabs.clientWidth : 0;
      })(),
    };
  });
}

async function auditRoute(page, route) {
  const problems = [];
  const jsErrors = [];
  const consoleHandler = (msg) => {
    const text = msg.text();
    if (msg.type() === 'error' && !text.includes('favicon') && !text.includes('id.json')) {
      jsErrors.push(text);
    }
  };
  const pageErrorHandler = (error) => jsErrors.push(error.message);

  page.on('console', consoleHandler);
  page.on('pageerror', pageErrorHandler);

  await page.setViewportSize({ width: 1366, height: 900 });
  const response = await page.goto(`${BASE_URL}${route.path}`, { waitUntil: 'load', timeout: 30000 });
  await page.waitForTimeout(900);

  const status = response ? response.status() : 0;
  if (status !== 200) problems.push(`HTTP ${status}`);

  const titleVisible = await page.getByRole('heading', { name: route.heading, exact: true }).first().isVisible().catch(() => false);
  if (!titleVisible) problems.push(`missing heading "${route.heading}"`);

  const desktop = await collectLayout(page);
  if (!desktop.shell) problems.push('missing .admin-page-shell');
  if (!desktop.header) problems.push('missing .admin-page-header');
  if (desktop.secondaryPanelCount < 1) problems.push('missing secondary admin panels');
  if (desktop.overflowX > 2) problems.push(`desktop horizontal overflow ${desktop.overflowX}px`);
  if (desktop.pageSubtitleSize && desktop.h1Size && desktop.pageSubtitleSize >= desktop.h1Size) {
    problems.push('page subtitle is not smaller than page title');
  }
  if (desktop.sectionSubtitleSize && desktop.sectionTitleSize && desktop.sectionSubtitleSize >= desktop.sectionTitleSize) {
    problems.push('section subtitle is not smaller than section title');
  }
  if (desktop.buttonHeights.some((height) => height < 32)) problems.push('desktop button target below 32px');

  await route.interact(page, problems);

  if (OUTPUT_DIR) {
    await page.screenshot({ path: `${OUTPUT_DIR}/${route.name.toLowerCase()}-desktop.png`, fullPage: false });
  }

  await page.setViewportSize({ width: 390, height: 844 });
  await page.reload({ waitUntil: 'load' });
  await page.waitForTimeout(900);

  const mobile = await collectLayout(page);
  if (mobile.overflowX > 2) problems.push(`mobile horizontal overflow ${mobile.overflowX}px`);
  if (mobile.buttonHeights.some((height) => height < 40)) problems.push('mobile button target below 40px');
  if (mobile.navTabOverflow > 2) problems.push(`settings tabs horizontal overflow ${mobile.navTabOverflow}px`);

  if (OUTPUT_DIR) {
    await page.screenshot({ path: `${OUTPUT_DIR}/${route.name.toLowerCase()}-mobile.png`, fullPage: false });
  }

  page.off('console', consoleHandler);
  page.off('pageerror', pageErrorHandler);

  return { ...route, pass: problems.length === 0 && jsErrors.length === 0, problems, jsErrors, desktop, mobile };
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  await login(page);

  const results = [];
  await page.goto(`${BASE_URL}/admin/users`, { waitUntil: 'load' });
  const editHref = await page.locator('a[href*="/admin/users/"][href*="/edit"]').first().getAttribute('href').catch(() => null);
  if (editHref) {
    ROUTES.push({
      name: 'Users Edit',
      path: editHref.replace(BASE_URL, ''),
      heading: 'Edit Data Pengguna',
      interact: async (page, problems) => {
        if (!(await page.locator('form[action*="/admin/users/"][action*="/update"]').count())) problems.push('users edit form missing');
      },
    });
  }

  for (const route of ROUTES) {
    const result = await auditRoute(page, route);
    results.push(result);
    console.log(`[${result.pass ? 'PASS' : 'FAIL'}] ${result.name}`);
    result.problems.forEach((problem) => console.log(`       issue: ${problem}`));
    result.jsErrors.forEach((error) => console.log(`       js: ${error}`));
  }

  await browser.close();

  const failed = results.filter((result) => !result.pass);
  console.log(`\nSUMMARY: ${results.length - failed.length}/${results.length} passed`);
  if (failed.length) process.exitCode = 1;
})();
