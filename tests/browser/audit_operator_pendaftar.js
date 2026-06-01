const { chromium } = require('playwright');

const OPERATOR_PAGES = [
  { name: 'Operator Dashboard', path: '/operator/dashboard' },
  { name: 'Registrants', path: '/operator/registrants', interact: 'datatable' },
  { name: 'Dapodik', path: '/operator/dapodik', interact: 'datatable' },
];

const PENDAFTAR_PAGES = [
  { name: 'Pendaftar Dashboard', path: '/pendaftar/dashboard' },
  { name: 'Wizard Step 1', path: '/pendaftar/daftar/step/1', interact: 'wizard' },
  { name: 'Wizard Step 2', path: '/pendaftar/daftar/step/2', interact: 'wizard' },
  { name: 'Wizard Step 3', path: '/pendaftar/daftar/step/3', interact: 'wizard' },
  { name: 'Wizard Step 4', path: '/pendaftar/daftar/step/4', interact: 'wizard' },
  { name: 'Wizard Step 5', path: '/pendaftar/daftar/step/5', interact: 'wizard' },
  { name: 'Wizard Step 6', path: '/pendaftar/daftar/step/6', interact: 'wizard' },
  { name: 'Wizard Step 7', path: '/pendaftar/daftar/step/7', interact: 'wizard' },
  { name: 'Wizard Step 8', path: '/pendaftar/daftar/step/8', interact: 'wizard' },
  { name: 'Dokumen', path: '/pendaftar/dokumen', interact: 'form' },
];

async function login(page, email, password) {
  await page.goto('http://localhost:8080/auth/logout', { waitUntil: 'domcontentloaded' }).catch(() => {});
  await page.goto('http://localhost:8080/auth/login', { waitUntil: 'domcontentloaded' });
  await page.waitForSelector('#email', { timeout: 15000 });
  await page.fill('#email', email);
  await page.fill('#password', password);
  await page.click('button[type="submit"]');
  await page.waitForTimeout(2000);
  return !page.url().includes('/auth/login');
}

async function auditPage(page, cfg, opts = {}) {
  const errors = [];
  const consoleErrors = [];
  page.on('pageerror', (e) => errors.push(e.message));
  page.on('console', (msg) => { if (msg.type() === 'error') consoleErrors.push(msg.text()); });

  const response = await page.goto(`http://localhost:8080${cfg.path}`, { waitUntil: 'load', timeout: 30000 });
  await page.waitForTimeout(opts.wizard ? 1000 : 2000);

  const data = await page.evaluate((wizard) => ({
    statusOk: true,
    jquery: typeof window.jQuery !== 'undefined',
    bootstrap: typeof window.bootstrap !== 'undefined',
    lucide: typeof window.lucide !== 'undefined',
    scriptSrcCount: document.querySelectorAll('script[src]').length,
    sidebarSvg: document.querySelectorAll('.sidebar svg').length,
    sidebarIconTags: document.querySelectorAll('.sidebar i[data-lucide]').length,
    iframeCount: document.querySelectorAll('iframe').length,
    mainContent: !!document.getElementById('main-content'),
    wizardContainer: !!document.querySelector('.wizard-container'),
    stepForm: !!document.querySelector('[id^="stepForm"]'),
    lucideSvgCount: document.querySelectorAll('svg.lucide, svg[data-lucide]').length + document.querySelectorAll('i[data-lucide] + svg, .wizard-container svg').length,
    hasForm: !!document.querySelector('form'),
    profileTabs: document.querySelectorAll('#profileTabs button').length,
  }), !!opts.wizard);

  const issues = [];
  const status = response ? response.status() : 0;
  if (status !== 200) issues.push(`HTTP ${status}`);

  if (opts.wizard) {
    if (!data.wizardContainer) issues.push('Missing wizard container');
    if (!data.lucide && !data.jquery) issues.push('Wizard JS stack incomplete');
    if (data.scriptSrcCount < 4) issues.push(`Wizard scripts ${data.scriptSrcCount}`);
  } else {
    if (!data.jquery) issues.push('jQuery missing');
    if (!data.bootstrap) issues.push('Bootstrap missing');
    if (data.scriptSrcCount < 8) issues.push(`Only ${data.scriptSrcCount} scripts`);
    if (data.sidebarSvg < 5 && data.mainContent) issues.push(`Sidebar SVG ${data.sidebarSvg}`);
    if (!data.mainContent) issues.push('Missing #main-content');
  }

  if (data.iframeCount > 0) issues.push(`Unexpected iframe ${data.iframeCount}`);
  if (cfg.interact === 'form' && !data.hasForm) issues.push('Missing form');
  if (cfg.interact === 'datatable') {
    const dt = await page.evaluate(() => typeof $ !== 'undefined' && typeof $.fn.DataTable === 'function');
    if (!dt) issues.push('DataTable unavailable');
  }
  if (cfg.interact === 'wizard') {
    const footerOk = await page.evaluate(() => !!document.getElementById('nextBtn') || !!document.getElementById('submitBtn'));
    if (!footerOk) issues.push('Wizard footer buttons missing');
  }
  if (cfg.interact === 'tabs') {
    const alamatTab = page.locator('#alamat-tab');
    if (await alamatTab.count()) {
      await alamatTab.click();
      await page.waitForTimeout(400);
      const tabOk = await page.evaluate(() => document.querySelector('#alamat')?.classList.contains('active'));
      if (!tabOk) issues.push('Profile tabs not switching');
    } else {
      issues.push('Profile tabs not found');
    }
  }

  const jsErrors = [...errors, ...consoleErrors.filter((e) => !e.includes('favicon') && !e.includes('id.json'))];
  page.removeAllListeners('pageerror');
  page.removeAllListeners('console');

  return { name: cfg.name, path: cfg.path, pass: issues.length === 0 && jsErrors.length === 0, issues, jsErrors, data };
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  const results = [];

  // OPERATOR
  const operatorLoggedIn = await login(page, 'operator@smartspmbpro.sch.id', 'Operator@12345');
  if (!operatorLoggedIn) {
    await login(page, 'admin@smartspmbpro.sch.id', 'Admin@12345');
  }
  for (const cfg of OPERATOR_PAGES) {
    results.push(await auditPage(page, cfg));
  }

  await page.goto('http://localhost:8080/operator/registrants', { waitUntil: 'load' });
  const showHref = await page.locator('a[href*="/operator/registrants/"]:not([href*="/edit"])').first().getAttribute('href').catch(() => null);
  const editHref = await page.locator('a[href*="/operator/registrants/"][href*="/edit"]').first().getAttribute('href').catch(() => null);

  if (showHref) {
    const path = showHref.replace('http://localhost:8080', '');
    results.push(await auditPage(page, { name: 'Registrant Show', path, interact: 'tabs' }));
  }
  if (editHref) {
    const path = editHref.replace('http://localhost:8080', '');
    results.push(await auditPage(page, { name: 'Registrant Edit', path, interact: 'form' }));
  }

  await page.goto('http://localhost:8080/operator/dapodik', { waitUntil: 'load' });
  const dapShowHref = await page.locator('a[href*="/operator/dapodik/"]').first().getAttribute('href').catch(() => null);
  if (dapShowHref && !dapShowHref.endsWith('/dapodik')) {
    results.push(await auditPage(page, { name: 'Dapodik Show', path: dapShowHref.replace('http://localhost:8080', '') }));
  }

  if (showHref) {
    const regId = showHref.match(/registrants\/(\d+)/)?.[1];
    if (regId) {
      results.push(await auditPage(page, { name: 'Documents Verify', path: `/operator/documents/${regId}` }));
    }
  }

  // PENDAFTAR
  await login(page, 'siswa1@gmail.com', 'Siswa@12345');
  for (const cfg of PENDAFTAR_PAGES) {
    results.push(await auditPage(page, cfg, { wizard: cfg.path.includes('/daftar/') }));
  }

  console.log('\n=== OPERATOR & PENDAFTAR AUDIT ===');
  for (const r of results) {
    console.log(`[${r.pass ? 'PASS' : 'FAIL'}] ${r.name} (${r.path})`);
    r.issues.forEach((i) => console.log(`       issue: ${i}`));
    r.jsErrors.forEach((e) => console.log(`       js: ${e}`));
  }

  const failed = results.filter((r) => !r.pass);
  console.log(`\nSUMMARY: ${results.length - failed.length}/${results.length} passed`);
  if (failed.length) process.exitCode = 1;

  await browser.close();
})();
