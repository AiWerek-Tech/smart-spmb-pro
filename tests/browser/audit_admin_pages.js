const { chromium } = require('playwright');

const PAGES = [
  { name: 'Dashboard', path: '/admin/dashboard', checks: ['sidebarSvg', 'scripts', 'jquery'], interact: 'dashboard' },
  { name: 'Users', path: '/admin/users', checks: ['sidebarSvg', 'scripts', 'jquery', 'datatable'], interact: 'users' },
  { name: 'Users Create', path: '/admin/users/create', checks: ['sidebarSvg', 'scripts', 'jquery', 'form'], interact: 'form' },
  { name: 'Settings', path: '/admin/settings', checks: ['sidebarSvg', 'scripts', 'jquery', 'tabs'], interact: 'settings' },
  { name: 'Jalur', path: '/admin/jalur', checks: ['sidebarSvg', 'scripts', 'jquery'], interact: 'jalur' },
  { name: 'Gelombang', path: '/admin/gelombang', checks: ['sidebarSvg', 'scripts', 'jquery'], interact: 'gelombang' },
  { name: 'Seleksi', path: '/admin/seleksi', checks: ['sidebarSvg', 'scripts', 'jquery'], interact: 'form' },
  { name: 'Announcements', path: '/admin/announcements', checks: ['sidebarSvg', 'scripts', 'jquery'], interact: 'modal' },
  { name: 'Announcements Create', path: '/admin/announcements/create', checks: ['sidebarSvg', 'scripts', 'jquery', 'form'], interact: 'form' },
  { name: 'Banners', path: '/admin/banners', checks: ['sidebarSvg', 'scripts', 'jquery'], interact: 'modal' },
  { name: 'Content', path: '/admin/content', checks: ['sidebarSvg', 'scripts', 'jquery', 'form'], interact: 'content' },
  { name: 'Testimonials', path: '/admin/testimonials', checks: ['sidebarSvg', 'scripts', 'jquery'], interact: 'modal' },
  { name: 'Statistics', path: '/admin/statistics', checks: ['sidebarSvg', 'scripts', 'jquery'], interact: 'modal' },
  { name: 'FAQ', path: '/admin/faq', checks: ['sidebarSvg', 'scripts', 'jquery'], interact: 'faq' },
  { name: 'Backup', path: '/admin/backup', checks: ['sidebarSvg', 'scripts', 'jquery', 'form'], interact: 'backup' },
];

async function login(page) {
  await page.goto('http://localhost:8080/auth/login', { waitUntil: 'domcontentloaded' });
  await page.fill('#email', 'admin@smartspmbpro.sch.id');
  await page.fill('#password', 'Admin@12345');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(2000);
}

async function runInteract(page, type) {
  const notes = [];
  try {
    if (type === 'dashboard') {
      const hasChartLib = await page.evaluate(() => typeof ApexCharts !== 'undefined');
      const hasChartEl = await page.evaluate(() => !!document.querySelector('#registrationTrendChart'));
      if (hasChartEl && !hasChartLib) notes.push('Chart element exists but ApexCharts missing');
      if (hasChartEl && hasChartLib) {
        const rendered = await page.evaluate(() => !!document.querySelector('#registrationTrendChart .apexcharts-canvas'));
        if (!rendered) notes.push('ApexCharts canvas not rendered');
      }
    }
    if (type === 'settings') {
      for (const id of ['nav-contact-tab', 'nav-accreditation-tab', 'nav-theme-tab', 'nav-app-tab']) {
        await page.click(`#${id}`);
        await page.waitForTimeout(250);
      }
      const panels = await page.evaluate(() =>
        ['nav-general', 'nav-contact', 'nav-accreditation', 'nav-theme', 'nav-app'].filter((id) => !!document.getElementById(id)).length
      );
      if (panels !== 5) notes.push(`Settings panels ${panels}/5`);
    }
    if (type === 'jalur') {
      const btn = page.locator('.edit-jalur-btn').first();
      if (await btn.count()) {
        await btn.click();
        await page.waitForTimeout(400);
        const visible = await page.locator('#editJalurModal.show, #editJalurModal[style*="display: block"]').count();
        if (!visible) notes.push('Edit jalur modal did not open');
        await page.keyboard.press('Escape');
      }
    }
    if (type === 'gelombang') {
      const btn = page.locator('.edit-gelombang-btn, [data-bs-target="#editGelombangModal"]').first();
      if (await btn.count()) {
        await btn.click();
        await page.waitForTimeout(400);
        const visible = await page.locator('#editGelombangModal.show').count();
        if (!visible && await page.locator('#editGelombangModal').count()) {
          // bootstrap 5 may use class show on modal
          const open = await page.evaluate(() => document.getElementById('editGelombangModal')?.classList.contains('show'));
          if (!open) notes.push('Edit gelombang modal did not open');
        }
        await page.keyboard.press('Escape');
      }
    }
    if (type === 'faq') {
      const btn = page.locator('.edit-faq-btn').first();
      if (await btn.count()) {
        await btn.click();
        await page.waitForTimeout(400);
        const open = await page.evaluate(() => document.getElementById('editFaqModal')?.classList.contains('show'));
        if (!open) notes.push('Edit FAQ modal did not open');
        await page.keyboard.press('Escape');
      }
    }
    if (type === 'modal') {
      const trigger = page.locator('#main-content [data-bs-toggle="modal"], .col-12.mb-4 [data-bs-toggle="modal"]').first();
      if (await trigger.count()) {
        await trigger.click();
        await page.waitForTimeout(400);
        const openModal = await page.locator('.modal.show').count();
        if (!openModal) notes.push('Bootstrap modal did not open');
        await page.keyboard.press('Escape');
      }
    }
    if (type === 'backup') {
      const btn = page.locator('#restore-btn');
      if (await btn.count()) {
        const disabledBefore = await btn.isDisabled();
        const cb = page.locator('#confirm');
        if (await cb.count()) {
          await cb.check();
          const disabledAfter = await btn.isDisabled();
          if (disabledBefore === disabledAfter && disabledAfter) notes.push('Restore button stay disabled after confirm');
        }
      }
    }
    if (type === 'content') {
      const brokenImages = await page.evaluate(() => {
        const imgs = [...document.querySelectorAll('.gallery-item-wrapper img')];
        return imgs.filter((img) => img.naturalWidth === 0).length;
      });
      if (brokenImages > 0) notes.push(`${brokenImages} gallery image(s) failed to load`);
    }
    if (type === 'users') {
      const dt = await page.evaluate(() => typeof $.fn.DataTable === 'function' && $.fn.DataTable.isDataTable('#usersTable'));
      if (await page.locator('#usersTable tbody tr').count() > 1 && !dt) notes.push('DataTable not initialized');
    }
  } catch (e) {
    notes.push(`Interact error: ${e.message}`);
  }
  return notes;
}

async function auditPage(page, cfg) {
  const errors = [];
  const consoleErrors = [];
  const handler = (msg) => { if (msg.type() === 'error') consoleErrors.push(msg.text()); };
  const pageErrorHandler = (err) => errors.push(err.message);
  page.on('console', handler);
  page.on('pageerror', pageErrorHandler);

  const response = await page.goto(`http://localhost:8080${cfg.path}`, { waitUntil: 'load', timeout: 30000 });
  await page.waitForTimeout(2000);

  const status = response ? response.status() : 0;
  const data = await page.evaluate(() => ({
    jquery: typeof window.jQuery !== 'undefined',
    bootstrap: typeof window.bootstrap !== 'undefined',
    scriptSrcCount: document.querySelectorAll('script[src]').length,
    sidebarSvg: document.querySelectorAll('.sidebar svg').length,
    sidebarIconTags: document.querySelectorAll('.sidebar i[data-lucide]').length,
    iframeCount: document.querySelectorAll('iframe').length,
    mainContent: !!document.getElementById('main-content'),
    hasForm: !!document.querySelector('form'),
    hasDataTable: !!document.querySelector('#usersTable'),
    settingsPanels: ['nav-general', 'nav-contact', 'nav-accreditation', 'nav-theme', 'nav-app'].filter((id) => !!document.getElementById(id)).length,
  }));

  const interactIssues = cfg.interact ? await runInteract(page, cfg.interact) : [];

  page.off('console', handler);
  page.off('pageerror', pageErrorHandler);

  const issues = [...interactIssues];
  if (status !== 200) issues.push(`HTTP ${status}`);
  if (!data.jquery) issues.push('jQuery missing');
  if (!data.bootstrap) issues.push('Bootstrap missing');
  if (data.scriptSrcCount < 8) issues.push(`Only ${data.scriptSrcCount} script tags`);
  if (data.sidebarSvg < 10) issues.push(`Sidebar SVG ${data.sidebarSvg}`);
  if (data.iframeCount > 0) issues.push(`Unexpected iframe count ${data.iframeCount}`);
  if (!data.mainContent) issues.push('Missing #main-content');
  if (cfg.checks.includes('form') && !data.hasForm) issues.push('Missing form');
  if (cfg.checks.includes('datatable') && !data.hasDataTable) issues.push('Missing datatable');
  if (cfg.checks.includes('tabs') && data.settingsPanels !== 5) issues.push(`Settings panels ${data.settingsPanels}/5`);

  const jsErrors = [...errors, ...consoleErrors.filter((e) => !e.includes('favicon') && !e.includes('id.json'))];

  return { name: cfg.name, path: cfg.path, pass: issues.length === 0 && jsErrors.length === 0, issues, jsErrors, data };
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  await login(page);

  await page.goto('http://localhost:8080/admin/users', { waitUntil: 'load' });
  const editHref = await page.locator('a[href*="/admin/users/"][href*="/edit"]').first().getAttribute('href').catch(() => null);
  await page.goto('http://localhost:8080/admin/announcements', { waitUntil: 'load' });
  const annEditHref = await page.locator('a[href*="/admin/announcements/"][href*="/edit"]').first().getAttribute('href').catch(() => null);

  const allPages = [...PAGES];
  if (editHref) allPages.push({ name: 'Users Edit', path: editHref.replace('http://localhost:8080', ''), checks: ['sidebarSvg', 'scripts', 'jquery', 'form'], interact: 'form' });
  if (annEditHref) allPages.push({ name: 'Announcements Edit', path: annEditHref.replace('http://localhost:8080', ''), checks: ['sidebarSvg', 'scripts', 'jquery', 'form'], interact: 'form' });

  const results = [];
  for (const cfg of allPages) {
    const r = await auditPage(page, cfg);
    results.push(r);
    console.log(`[${r.pass ? 'PASS' : 'FAIL'}] ${r.name}`);
    r.issues.forEach((i) => console.log(`       issue: ${i}`));
    r.jsErrors.forEach((e) => console.log(`       js: ${e}`));
  }

  const failed = results.filter((r) => !r.pass);
  console.log(`\nSUMMARY: ${results.length - failed.length}/${results.length} passed`);
  if (failed.length) process.exitCode = 1;
  await browser.close();
})();
