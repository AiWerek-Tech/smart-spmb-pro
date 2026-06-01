const { chromium } = require('playwright');

const VIEWPORTS = [
  { name: 'mobile', width: 390, height: 844, expectsBottomNav: true },
  { name: 'tablet', width: 768, height: 1024, expectsBottomNav: true },
  { name: 'desktop', width: 1366, height: 900, expectsBottomNav: false },
];

const PAGES = [
  { name: 'Home', path: '/', bottom: '.sp-mobile-bottom-nav', shell: 'public' },
  { name: 'Admin Dashboard', path: '/admin/dashboard', auth: ['admin@smartspmbpro.sch.id', 'Admin@12345'], bottom: '.dashboard-mobile-bottom-nav', shell: 'dashboard' },
  { name: 'Operator Dashboard', path: '/operator/dashboard', auth: ['operator2@smartspmbpro.sch.id', 'Operator@12345'], bottom: '.dashboard-mobile-bottom-nav', shell: 'dashboard' },
  { name: 'Pendaftar Dashboard', path: '/pendaftar/dashboard', auth: ['siswa1@gmail.com', 'Siswa@12345'], bottom: '.dashboard-mobile-bottom-nav', shell: 'dashboard' },
  { name: 'Pendaftar Wizard', path: '/pendaftar/daftar/step/1', auth: ['siswa1@gmail.com', 'Siswa@12345'], bottom: '.wizard-footer', shell: 'wizard' },
];

async function login(page, email, password) {
  await page.goto('http://localhost:8080/auth/logout', { waitUntil: 'domcontentloaded' }).catch(() => {});
  await page.goto('http://localhost:8080/auth/login', { waitUntil: 'domcontentloaded' });
  await page.fill('#email', email);
  await page.fill('#password', password);
  await page.click('button[type="submit"]');
  await page.waitForTimeout(1200);
  return !page.url().includes('/auth/login');
}

async function inspectPage(page, cfg, viewport) {
  const issues = [];
  const response = await page.goto(`http://localhost:8080${cfg.path}`, { waitUntil: 'load', timeout: 30000 });
  await page.waitForTimeout(1000);

  const data = await page.evaluate(({ bottom, shell }) => {
    const bottomEl = document.querySelector(bottom);
    const sidebar = document.querySelector('.sidebar');
    const topbar = document.querySelector('.top-navbar, .sp-public-navbar, .wizard-header');
    const critical = [...document.querySelectorAll([
      'button',
      '.btn',
      '.menu-link',
      '.theme-toggle-btn',
      '.menu-toggle-btn',
      '.sp-nav-icon-btn',
      '.sp-nav-cta',
      '.sp-secondary-item',
      '.sp-primary-card',
      '.mobile-nav-item',
      '.dashboard-bottom-item',
      '.wizard-footer .btn',
      'input:not([type="radio"]):not([type="checkbox"])',
      'select',
      'textarea',
      '[role="button"]',
    ].join(','))];

    const smallTargets = critical.filter((el) => {
      const rect = el.getBoundingClientRect();
      const style = getComputedStyle(el);
      const visible = rect.width > 0 && rect.height > 0 && rect.top >= 0 && rect.top < innerHeight && style.display !== 'none' && style.visibility !== 'hidden';
      return visible && (rect.width < 44 || rect.height < 44);
    }).slice(0, 8).map((el) => {
      const rect = el.getBoundingClientRect();
      return {
        tag: el.tagName.toLowerCase(),
        className: String(el.className).slice(0, 80),
        text: (el.innerText || el.value || el.getAttribute('aria-label') || '').trim().slice(0, 60),
        width: Math.round(rect.width),
        height: Math.round(rect.height),
      };
    });

    return {
      path: location.pathname,
      horizontalOverflow: document.documentElement.scrollWidth - innerWidth,
      bottomVisible: !!bottomEl && getComputedStyle(bottomEl).display !== 'none' && bottomEl.getBoundingClientRect().height > 0,
      bottomHeight: bottomEl ? Math.round(bottomEl.getBoundingClientRect().height) : 0,
      topbarVisible: !!topbar && getComputedStyle(topbar).display !== 'none' && topbar.getBoundingClientRect().height > 0,
      sidebarVisibleOnDesktop: shell === 'dashboard' && !!sidebar && getComputedStyle(sidebar).display !== 'none' && sidebar.getBoundingClientRect().x >= 0 && sidebar.getBoundingClientRect().width > 0,
      smallTargets,
    };
  }, { bottom: cfg.bottom, shell: cfg.shell });

  if (response && response.status() !== 200) issues.push(`HTTP ${response.status()}`);
  if (Math.abs(data.horizontalOverflow) > 2) issues.push(`Horizontal overflow ${data.horizontalOverflow}px`);
  if (!data.topbarVisible) issues.push('Top app bar/header missing');
  if (viewport.expectsBottomNav && !data.bottomVisible) issues.push('Mobile/tablet bottom navigation missing');
  if (!viewport.expectsBottomNav && cfg.shell === 'dashboard' && !data.sidebarVisibleOnDesktop) issues.push('Desktop dashboard sidebar missing');
  if (!viewport.expectsBottomNav && cfg.shell !== 'wizard' && data.bottomVisible) issues.push('Desktop bottom navigation should be hidden');
  if (data.smallTargets.length > 0) issues.push(`Small critical touch targets: ${JSON.stringify(data.smallTargets)}`);

  return { viewport: viewport.name, page: cfg.name, pass: issues.length === 0, issues, data };
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const results = [];

  for (const viewport of VIEWPORTS) {
    const page = await browser.newPage({
      viewport: { width: viewport.width, height: viewport.height },
      isMobile: viewport.width < 600,
      hasTouch: viewport.width < 992,
    });

    for (const cfg of PAGES) {
      if (cfg.auth) {
        const loggedIn = await login(page, cfg.auth[0], cfg.auth[1]);
        if (!loggedIn) {
          results.push({ viewport: viewport.name, page: cfg.name, pass: false, issues: [`Login failed for ${cfg.auth[0]}`], data: {} });
          continue;
        }
      }
      results.push(await inspectPage(page, cfg, viewport));
    }

    await page.close();
  }

  console.log('\n=== MOBILE NATIVE UI AUDIT ===');
  for (const result of results) {
    console.log(`[${result.pass ? 'PASS' : 'FAIL'}] ${result.viewport} / ${result.page}`);
    result.issues.forEach((issue) => console.log(`       issue: ${issue}`));
  }

  const failed = results.filter((result) => !result.pass);
  console.log(`\nSUMMARY: ${results.length - failed.length}/${results.length} passed`);
  if (failed.length) process.exitCode = 1;

  await browser.close();
})();
