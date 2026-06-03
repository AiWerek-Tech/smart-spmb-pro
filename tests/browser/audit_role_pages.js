const { chromium } = require('playwright');

const BASE_URL = process.env.BASE_URL || 'http://localhost:8080';
const OUTPUT_DIR = process.env.AUDIT_SCREENSHOT_DIR || null;
const REGISTRATION_ID = process.env.AUDIT_REGISTRATION_ID || '1';

const OPERATOR_ROUTES = [
  { name: 'Operator Dashboard', path: '/operator/dashboard', heading: 'Panel Operator', shell: 'role' },
  { name: 'Operator Registrants', path: '/operator/registrants', heading: 'Kelola Calon Peserta', shell: 'role' },
  { name: 'Operator Registrant Detail', path: `/operator/registrants/${REGISTRATION_ID}`, heading: null, shell: 'role' },
  { name: 'Operator Registrant Edit', path: `/operator/registrants/${REGISTRATION_ID}/edit`, heading: 'Koreksi Data Pendaftar', shell: 'role' },
  { name: 'Operator Documents', path: `/operator/documents/${REGISTRATION_ID}`, heading: null, shell: 'role' },
  { name: 'Operator Dapodik', path: '/operator/dapodik', heading: 'Validasi & Kesiapan Dapodik', shell: 'role' },
  { name: 'Operator Dapodik Detail', path: `/operator/dapodik/${REGISTRATION_ID}`, heading: 'Laporan Dapodik', shell: 'role' },
];

const PENDAFTAR_ROUTES = [
  { name: 'Pendaftar Dashboard', path: '/pendaftar/dashboard', heading: 'Dashboard Calon Siswa', shell: 'role' },
  { name: 'Pendaftar Documents', path: '/pendaftar/dokumen', heading: 'Berkas & Dokumen Pendukung', shell: 'role' },
  { name: 'Wizard Step 1', path: '/pendaftar/daftar/step/1', heading: 'Langkah 1: Identitas Calon Siswa', shell: 'wizard' },
  { name: 'Wizard Step 2', path: '/pendaftar/daftar/step/2', heading: 'Langkah 2: Alamat & Kontak', shell: 'wizard' },
  { name: 'Wizard Step 3', path: '/pendaftar/daftar/step/3', heading: 'Langkah 3: Data Ayah Kandung', shell: 'wizard' },
  { name: 'Wizard Step 4', path: '/pendaftar/daftar/step/4', heading: 'Langkah 4: Data Ibu Kandung', shell: 'wizard' },
  { name: 'Wizard Step 5', path: '/pendaftar/daftar/step/5', heading: 'Langkah 5: Data Wali', shell: 'wizard' },
  { name: 'Wizard Step 6', path: '/pendaftar/daftar/step/6', heading: 'Langkah 6: Data Periodik Siswa', shell: 'wizard' },
  { name: 'Wizard Step 7', path: '/pendaftar/daftar/step/7', heading: 'Langkah 7: Data Prestasi Siswa', shell: 'wizard' },
  { name: 'Wizard Step 8', path: '/pendaftar/daftar/step/8', heading: 'Langkah 8: Unggah Dokumen & Finalisasi', shell: 'wizard' },
];

async function login(page, email, password, expectedPath) {
  await page.goto(`${BASE_URL}/auth/login`, { waitUntil: 'domcontentloaded' });
  await page.fill('#email', email);
  await page.fill('#password', password);
  await page.click('button[type="submit"]');
  await page.waitForURL((url) => url.pathname.includes(expectedPath), { timeout: 10000 })
    .catch(() => page.waitForTimeout(1200));
}

async function logout(page) {
  await page.goto(`${BASE_URL}/auth/logout`, { waitUntil: 'domcontentloaded' }).catch(() => {});
}

async function collectLayout(page, shell) {
  return page.evaluate((expectedShell) => {
    const doc = document.documentElement;
    const roleShell = document.querySelector('.role-page-shell');
    const roleHeader = document.querySelector('.role-page-header');
    const wizard = document.querySelector('.wizard-container');
    const wizardHeader = document.querySelector('.wizard-header h2');
    const wizardStep = document.querySelector('.wizard-step-title');
    const h1 = roleHeader?.querySelector('h1');
    const subtitle = roleHeader?.querySelector('.role-page-header__subtitle');
    const cardTitle = document.querySelector('.role-page-shell .card-title, .role-page-shell .role-subsection-title');
    const visibleButtons = [...document.querySelectorAll('button, .btn, a.btn')]
      .filter((el) => {
        const rect = el.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0 && getComputedStyle(el).visibility !== 'hidden';
      })
      .slice(0, 50);

    return {
      expectedShell,
      roleShell: !!roleShell,
      roleHeader: !!roleHeader,
      wizard: !!wizard,
      h1Size: h1 ? parseFloat(getComputedStyle(h1).fontSize) : 0,
      subtitleSize: subtitle ? parseFloat(getComputedStyle(subtitle).fontSize) : 0,
      cardTitleSize: cardTitle ? parseFloat(getComputedStyle(cardTitle).fontSize) : 0,
      wizardHeaderSize: wizardHeader ? parseFloat(getComputedStyle(wizardHeader).fontSize) : 0,
      wizardStepSize: wizardStep ? parseFloat(getComputedStyle(wizardStep).fontSize) : 0,
      overflowX: doc.scrollWidth - doc.clientWidth,
      buttonHeights: visibleButtons.map((el) => el.getBoundingClientRect().height),
      panelCount: document.querySelectorAll('.role-page-shell .card, .role-summary-card, .admin-filter-panel, .wizard-container').length,
    };
  }, shell);
}

async function auditRoute(page, route) {
  const problems = [];
  const pageErrors = [];
  const pageErrorHandler = (error) => pageErrors.push(error.message);
  page.on('pageerror', pageErrorHandler);

  await page.setViewportSize({ width: 1366, height: 900 });
  const response = await page.goto(`${BASE_URL}${route.path}`, { waitUntil: 'domcontentloaded', timeout: 30000 });
  await page.waitForTimeout(600);
  const status = response ? response.status() : 0;
  if (status !== 200) problems.push(`HTTP ${status}`);

  if (route.heading) {
    const headingVisible = await page.getByRole('heading', { name: route.heading, exact: true }).first().isVisible().catch(() => false);
    if (!headingVisible) problems.push(`missing heading "${route.heading}"`);
  }

  const desktop = await collectLayout(page, route.shell);
  if (route.shell === 'role') {
    if (!desktop.roleShell) problems.push('missing .role-page-shell');
    if (!desktop.roleHeader) problems.push('missing .role-page-header');
    if (desktop.subtitleSize && desktop.h1Size && desktop.subtitleSize >= desktop.h1Size) problems.push('role subtitle is not smaller than page title');
    if (desktop.cardTitleSize && desktop.h1Size && desktop.cardTitleSize >= desktop.h1Size) problems.push('card/subsection title is not smaller than page title');
    if (desktop.panelCount < 1) problems.push('missing role panel/card surface');
  } else {
    if (!desktop.wizard) problems.push('missing .wizard-container');
    if (desktop.wizardStepSize && desktop.wizardHeaderSize && desktop.wizardStepSize >= desktop.wizardHeaderSize) problems.push('wizard step title is not smaller than wizard header');
  }
  if (desktop.overflowX > 2) problems.push(`desktop horizontal overflow ${desktop.overflowX}px`);
  if (desktop.buttonHeights.some((height) => height < 32)) problems.push('desktop button target below 32px');

  if (OUTPUT_DIR) {
    await page.screenshot({ path: `${OUTPUT_DIR}/${route.name.toLowerCase().replace(/[^a-z0-9]+/g, '-')}-desktop.png`, fullPage: false });
  }

  await page.setViewportSize({ width: 390, height: 844 });
  await page.reload({ waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(600);
  const mobile = await collectLayout(page, route.shell);
  if (mobile.overflowX > 2) problems.push(`mobile horizontal overflow ${mobile.overflowX}px`);
  if (mobile.buttonHeights.some((height) => height < 40)) problems.push('mobile button target below 40px');

  if (OUTPUT_DIR) {
    await page.screenshot({ path: `${OUTPUT_DIR}/${route.name.toLowerCase().replace(/[^a-z0-9]+/g, '-')}-mobile.png`, fullPage: false });
  }

  page.off('pageerror', pageErrorHandler);
  return { ...route, pass: problems.length === 0 && pageErrors.length === 0, problems, pageErrors, desktop, mobile };
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  const results = [];

  await login(page, 'operator@smartspmbpro.sch.id', 'Operator@12345', '/operator/dashboard');
  for (const route of OPERATOR_ROUTES) {
    const result = await auditRoute(page, route);
    results.push(result);
    console.log(`[${result.pass ? 'PASS' : 'FAIL'}] ${result.name}`);
    result.problems.forEach((problem) => console.log(`       issue: ${problem}`));
    result.pageErrors.forEach((error) => console.log(`       pageerror: ${error}`));
  }

  await logout(page);
  await login(page, 'siswa1@gmail.com', 'Siswa@12345', '/pendaftar/dashboard');
  for (const route of PENDAFTAR_ROUTES) {
    const result = await auditRoute(page, route);
    results.push(result);
    console.log(`[${result.pass ? 'PASS' : 'FAIL'}] ${result.name}`);
    result.problems.forEach((problem) => console.log(`       issue: ${problem}`));
    result.pageErrors.forEach((error) => console.log(`       pageerror: ${error}`));
  }

  await browser.close();
  const failed = results.filter((result) => !result.pass);
  console.log(`\nSUMMARY: ${results.length - failed.length}/${results.length} passed`);
  if (failed.length) process.exitCode = 1;
})();
