const { chromium } = require('playwright');

const BASE_URL = process.env.BASE_URL || 'http://localhost:8080';
const OUTPUT_DIR = process.env.AUDIT_SCREENSHOT_DIR || null;

const ROUTES = [
  { name: 'Content', path: '/admin/content', heading: 'Profil Sekolah', required: ['form[action*="/admin/content/save"]'] },
  { name: 'Teachers', path: '/admin/teachers', heading: 'Tenaga Pendidik', required: ['form[action*="/admin/content/teachers/store"]'] },
  { name: 'Gallery', path: '/admin/gallery', heading: 'Galeri Sekolah', required: ['form[action*="/admin/content/gallery/upload"]'] },
  { name: 'Banners', path: '/admin/banners', heading: 'Banner Hero', required: ['form[action*="/admin/banners/store"]'] },
  { name: 'Testimonials', path: '/admin/testimonials', heading: 'Testimoni', required: ['form[action*="/admin/testimonials/store"]'] },
  { name: 'Statistics', path: '/admin/statistics', heading: 'Statistik', required: ['form[action*="/admin/statistics/store"]'] },
  { name: 'FAQ', path: '/admin/faq', heading: 'FAQ', required: ['form[action*="/admin/faq/store"]'] },
  { name: 'Announcements', path: '/admin/announcements', heading: 'Kelola Pengumuman', required: ['#announcementsTable'] },
  { name: 'Announcements Create', path: '/admin/announcements/create', heading: 'Buat Pengumuman Baru', required: ['form[action*="/admin/announcements/store"]'] },
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
    const doc = document.documentElement;
    const header = document.querySelector('.admin-page-header');
    const h1 = header?.querySelector('h1');
    const subtitle = header?.querySelector('.admin-page-subtitle, p:not(.admin-panel__kicker)');
    const sectionTitle = document.querySelector('.admin-section-title');
    const sectionSubtitle = document.querySelector('.admin-section-subtitle');
    const buttons = [...document.querySelectorAll('#main-content .btn, #main-content button')]
      .filter((el) => {
        const rect = el.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0;
      })
      .slice(0, 40);
    return {
      shell: !!document.querySelector('.admin-page-shell'),
      header: !!header,
      panelCount: document.querySelectorAll('.admin-secondary-panel, .admin-filter-panel').length,
      h1Size: h1 ? parseFloat(getComputedStyle(h1).fontSize) : 0,
      subtitleSize: subtitle ? parseFloat(getComputedStyle(subtitle).fontSize) : 0,
      sectionTitleSize: sectionTitle ? parseFloat(getComputedStyle(sectionTitle).fontSize) : 0,
      sectionSubtitleSize: sectionSubtitle ? parseFloat(getComputedStyle(sectionSubtitle).fontSize) : 0,
      overflowX: doc.scrollWidth - doc.clientWidth,
      buttonHeights: buttons.map((el) => el.getBoundingClientRect().height),
    };
  });
}

async function auditRoute(page, route) {
  const problems = [];
  const jsErrors = [];
  const consoleHandler = (msg) => {
    const text = msg.text();
    if (msg.type() === 'error' && !text.includes('favicon') && !text.includes('id.json')) jsErrors.push(text);
  };
  const pageErrorHandler = (error) => jsErrors.push(error.message);
  page.on('console', consoleHandler);
  page.on('pageerror', pageErrorHandler);

  await page.setViewportSize({ width: 1366, height: 900 });
  const response = await page.goto(`${BASE_URL}${route.path}`, { waitUntil: 'domcontentloaded', timeout: 30000 });
  await page.waitForTimeout(900);
  if ((response?.status() || 0) !== 200) problems.push(`HTTP ${response?.status() || 0}`);

  const heading = await page.getByRole('heading', { name: route.heading, exact: true }).first().isVisible().catch(() => false);
  if (!heading) problems.push(`missing heading "${route.heading}"`);

  for (const selector of route.required) {
    if (!(await page.locator(selector).count())) problems.push(`missing required selector ${selector}`);
  }

  const desktop = await collectLayout(page);
  if (!desktop.shell) problems.push('missing .admin-page-shell');
  if (!desktop.header) problems.push('missing .admin-page-header');
  if (desktop.panelCount < 1) problems.push('missing admin panels');
  if (desktop.overflowX > 2) problems.push(`desktop horizontal overflow ${desktop.overflowX}px`);
  if (desktop.subtitleSize && desktop.h1Size && desktop.subtitleSize >= desktop.h1Size) problems.push('page subtitle is not smaller than title');
  if (desktop.sectionSubtitleSize && desktop.sectionTitleSize && desktop.sectionSubtitleSize >= desktop.sectionTitleSize) problems.push('section subtitle is not smaller than section title');
  if (desktop.buttonHeights.some((height) => height < 32)) problems.push('desktop button target below 32px');

  if (OUTPUT_DIR) await page.screenshot({ path: `${OUTPUT_DIR}/${route.name.toLowerCase().replaceAll(' ', '-')}-desktop.png`, fullPage: false });

  await page.setViewportSize({ width: 390, height: 844 });
  await page.reload({ waitUntil: 'load' });
  await page.waitForTimeout(900);
  const mobile = await collectLayout(page);
  if (mobile.overflowX > 2) problems.push(`mobile horizontal overflow ${mobile.overflowX}px`);
  if (mobile.buttonHeights.some((height) => height < 40)) problems.push('mobile button target below 40px');
  if (OUTPUT_DIR) await page.screenshot({ path: `${OUTPUT_DIR}/${route.name.toLowerCase().replaceAll(' ', '-')}-mobile.png`, fullPage: false });

  page.off('console', consoleHandler);
  page.off('pageerror', pageErrorHandler);
  return { name: route.name, pass: problems.length === 0 && jsErrors.length === 0, problems, jsErrors };
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  await login(page);

  const allRoutes = [...ROUTES];
  await page.goto(`${BASE_URL}/admin/announcements`, { waitUntil: 'load' });
  const editHref = await page.locator('a[href*="/admin/announcements/"][href*="/edit"]').first().getAttribute('href').catch(() => null);
  if (editHref) {
    allRoutes.push({
      name: 'Announcements Edit',
      path: editHref.replace(BASE_URL, ''),
      heading: 'Edit Pengumuman',
      required: ['form[action*="/admin/announcements/"][action*="/update"]'],
    });
  }

  const results = [];
  for (const route of allRoutes) {
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
