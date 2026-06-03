const { chromium } = require('playwright');

const BASE_URL = process.env.BASE_URL || 'http://localhost:8080';
const OUTPUT_DIR = process.env.AUDIT_SCREENSHOT_DIR || null;

const ROUTES = [
  {
    name: 'Academic Years',
    path: '/admin/academic-years',
    heading: 'Tahun Pelajaran',
    interact: async (page, problems) => {
      const createTrigger = page.locator('[data-academic-year-create-trigger]');
      if (await createTrigger.count()) {
        await createTrigger.first().click();
        await page.waitForTimeout(250);
        if (!(await page.locator('#createAcademicYear.show').count())) problems.push('academic year create modal did not open');
        await page.keyboard.press('Escape');
      }
    },
  },
  {
    name: 'Jalur',
    path: '/admin/jalur',
    heading: 'Jalur Pendaftaran',
    interact: async (page, problems) => {
      const edit = page.locator('.edit-jalur-btn').first();
      if (await edit.count()) {
        await edit.click();
        await page.waitForTimeout(300);
        if (!(await page.locator('#editJalurModal.show').count())) problems.push('jalur edit modal did not open');
        await page.keyboard.press('Escape');
      }
    },
  },
  {
    name: 'Gelombang',
    path: '/admin/gelombang',
    heading: 'Kelola Gelombang Pendaftaran',
    interact: async (page, problems) => {
      const edit = page.locator('.edit-gelombang-btn').first();
      if (await edit.count()) {
        await edit.click();
        await page.waitForTimeout(300);
        if (!(await page.locator('#editGelombangModal.show').count())) problems.push('gelombang edit modal did not open');
        await page.keyboard.press('Escape');
      }
    },
  },
  {
    name: 'Document Requirements',
    path: '/admin/document-requirements',
    heading: 'Syarat Dokumen',
    interact: async (page, problems) => {
      const addCode = page.locator('#document_type');
      if (!(await addCode.count())) problems.push('document requirement add form missing');
    },
  },
  {
    name: 'Seleksi',
    path: '/admin/seleksi',
    heading: 'Hasil Seleksi & Kelulusan',
    interact: async (page, problems) => {
      if (!(await page.locator('#seleksiTable').count())) problems.push('seleksi table missing');
      const ranking = page.locator('form[action$="/admin/seleksi/hitung-ranking"], form[action*="/admin/seleksi/hitung-ranking"]');
      if (!(await ranking.count())) problems.push('ranking calculation form missing');
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
    const doc = document.documentElement;
    const header = document.querySelector('.admin-page-header');
    const h1 = header?.querySelector('h1');
    const subtitle = header?.querySelector('.admin-page-subtitle, p:not(.admin-panel__kicker)');
    const sectionTitle = document.querySelector('.admin-section-title, .academic-years-panel h2');
    const sectionSubtitle = document.querySelector('.admin-section-subtitle, .academic-years-panel__header > p');
    const buttons = [...document.querySelectorAll('#main-content .btn, #main-content button')]
      .filter((el) => {
        const rect = el.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0;
      })
      .slice(0, 40);

    return {
      shell: !!document.querySelector('.admin-page-shell'),
      header: !!header,
      panelCount: document.querySelectorAll('.admin-secondary-panel, .admin-filter-panel, .academic-years-panel').length,
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
  const response = await page.goto(`${BASE_URL}${route.path}`, { waitUntil: 'load', timeout: 30000 });
  await page.waitForTimeout(900);
  if ((response?.status() || 0) !== 200) problems.push(`HTTP ${response?.status() || 0}`);

  const titleVisible = await page.getByRole('heading', { name: route.heading, exact: true }).first().isVisible().catch(() => false);
  if (!titleVisible) problems.push(`missing heading "${route.heading}"`);

  const desktop = await collectLayout(page);
  if (!desktop.shell) problems.push('missing .admin-page-shell');
  if (!desktop.header) problems.push('missing .admin-page-header');
  if (desktop.panelCount < 1) problems.push('missing admin panels');
  if (desktop.overflowX > 2) problems.push(`desktop horizontal overflow ${desktop.overflowX}px`);
  if (desktop.subtitleSize && desktop.h1Size && desktop.subtitleSize >= desktop.h1Size) problems.push('page subtitle is not smaller than title');
  if (desktop.sectionSubtitleSize && desktop.sectionTitleSize && desktop.sectionSubtitleSize >= desktop.sectionTitleSize) problems.push('section subtitle is not smaller than section title');
  if (desktop.buttonHeights.some((height) => height < 32)) problems.push('desktop button target below 32px');

  await route.interact(page, problems);
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

  const results = [];
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
