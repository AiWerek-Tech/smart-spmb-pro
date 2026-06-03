const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8080';
const VIEWPORTS = [
  { name: 'desktop-1366', width: 1366, height: 900 },
  { name: 'tablet-768', width: 768, height: 1024 },
  { name: 'mobile-390', width: 390, height: 844 },
  { name: 'mobile-360', width: 360, height: 740 },
];

const startYear = 2100 + (Number(String(Date.now()).slice(-3)) % 500);
const yearA = `${startYear}/${startYear + 1}`;
const yearB = `${startYear + 2}/${startYear + 3}`;
const invalidYear = `${startYear + 4}/${startYear + 8}`;

async function login(page) {
  await page.goto(`${BASE_URL}/auth/login`, { waitUntil: 'domcontentloaded' });
  await page.fill('#email', 'admin@smartspmbpro.sch.id');
  await page.fill('#password', 'Admin@12345');
  await page.click('button[type="submit"]');
  await page.waitForURL(/\/admin\/dashboard|\/dashboard/, { timeout: 10000 }).catch(() => {});
}

async function postForm(page, action, fields = {}) {
  const navigation = page.waitForNavigation({ waitUntil: 'load', timeout: 10000 }).catch(() => {});

  await page.evaluate(({ action, fields }) => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    const csrf = document.querySelector('input[name^="csrf_"]');
    if (csrf) {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = csrf.name;
      input.value = csrf.value;
      form.appendChild(input);
    }

    for (const [name, value] of Object.entries(fields)) {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = value;
      form.appendChild(input);
    }

    document.body.appendChild(form);
    window.setTimeout(() => form.submit(), 0);
  }, { action, fields });
  await navigation;
}

async function createYear(page, year, activateNow = false) {
  await postForm(page, `${BASE_URL}/admin/academic-years/store`, {
    year,
    label: `QA Tahun Pelajaran ${year}`,
    starts_at: `${year.slice(0, 4)}-07-01`,
    ends_at: `${year.slice(5, 9)}-06-30`,
    notes: `QA seed ${year}`,
    ...(activateNow ? { activate_now: '1' } : {}),
  });
}

async function getYearState(page, year) {
  return page.evaluate((year) => {
    const row = document.querySelector(`[data-academic-year-row][data-year="${year}"]`);
    const card = document.querySelector(`[data-academic-year-card][data-year="${year}"]`);
    const source = row || card;

    return source ? {
      id: source.getAttribute('data-id'),
      active: source.getAttribute('data-active') === '1',
      archived: source.getAttribute('data-archived') === '1',
      text: source.textContent,
    } : null;
  }, year);
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 390, height: 844 } });
  const issues = [];
  const consoleIssues = [];

  page.on('console', (msg) => {
    const text = msg.text();
    if ((msg.type() === 'warning' || msg.type() === 'error') && !text.includes('favicon')) {
      consoleIssues.push(text);
    }
  });
  page.on('pageerror', (error) => consoleIssues.push(`Page error: ${error.message}`));

  await login(page);

  for (const viewport of VIEWPORTS) {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });
    const response = await page.goto(`${BASE_URL}/admin/academic-years`, { waitUntil: 'load' });
    await page.waitForTimeout(700);

    if (!response || response.status() >= 400) {
      issues.push(`${viewport.name}: response status ${response ? response.status() : 'missing'}`);
      continue;
    }

    const state = await page.evaluate(() => {
      const bodyText = document.body.innerText;
      const bottomNav = document.querySelector('.dashboard-mobile-bottom-nav');
      const desktopTable = document.querySelector('[data-academic-year-table]');
      const mobileList = document.querySelector('[data-academic-year-mobile-list]');
      const pageTitle = document.querySelector('#academic-years-title');
      const panelTitle = document.querySelector('#academic-years-list-title');
      const createTrigger = document.querySelector('[data-academic-year-create-trigger]');
      const createPanel = document.querySelector('.academic-years-panel--form');
      const createModal = document.querySelector('#createAcademicYear');
      const createModalTitle = document.querySelector('#createAcademicYearTitle');

      return {
        title: document.querySelector('#academic-years-title')?.textContent.trim() || '',
        summaryCards: document.querySelectorAll('[data-academic-year-summary-card]').length,
        rows: document.querySelectorAll('[data-academic-year-row]').length,
        cards: document.querySelectorAll('[data-academic-year-card]').length,
        editControls: document.querySelectorAll('[data-academic-year-edit]').length,
        deleteControls: document.querySelectorAll('[data-academic-year-delete]').length,
        activeCount: document.querySelectorAll('[data-academic-year-row][data-active="1"], [data-academic-year-card][data-active="1"]').length,
        mobileNavDisplay: bottomNav ? getComputedStyle(bottomNav).display : 'missing',
        tableDisplay: desktopTable ? getComputedStyle(desktopTable).display : 'missing',
        tableScrollDelta: desktopTable ? desktopTable.scrollWidth - desktopTable.clientWidth : 0,
        mobileListDisplay: mobileList ? getComputedStyle(mobileList).display : 'missing',
        createPanelExists: Boolean(createPanel),
        createModalExists: Boolean(createModal),
        createTriggerIsModal: createTrigger?.getAttribute('data-bs-toggle') === 'modal' && createTrigger?.getAttribute('data-bs-target') === '#createAcademicYear',
        pageTitleSize: pageTitle ? parseFloat(getComputedStyle(pageTitle).fontSize) : 0,
        panelTitleSize: panelTitle ? parseFloat(getComputedStyle(panelTitle).fontSize) : 0,
        createModalTitleSize: createModalTitle ? parseFloat(getComputedStyle(createModalTitle).fontSize) : 0,
        scrollDelta: document.documentElement.scrollWidth - document.documentElement.clientWidth,
        hasMojibake: bodyText.includes('Â') || bodyText.includes('�'),
      };
    });

    if (state.title !== 'Tahun Pelajaran') {
      issues.push(`${viewport.name}: unexpected title "${state.title}"`);
    }
    if (state.summaryCards < 3) {
      issues.push(`${viewport.name}: expected at least 3 summary cards, got ${state.summaryCards}`);
    }
    if (state.rows < 1 || state.cards < 1) {
      issues.push(`${viewport.name}: expected desktop rows and mobile cards, got rows=${state.rows}, cards=${state.cards}`);
    }
    if (state.editControls < 1) {
      issues.push(`${viewport.name}: edit controls are missing`);
    }
    if (state.rows > 1 && state.deleteControls < 1) {
      issues.push(`${viewport.name}: delete controls are missing for non-active years`);
    }
    if (state.activeCount !== 2) {
      issues.push(`${viewport.name}: expected one active row and one active card, got ${state.activeCount}`);
    }
    if (state.scrollDelta > 1) {
      issues.push(`${viewport.name}: horizontal overflow delta ${state.scrollDelta}px`);
    }
    if (state.hasMojibake) {
      issues.push(`${viewport.name}: visible text contains mojibake/replacement characters`);
    }
    if (viewport.width < 992 && state.mobileNavDisplay === 'none') {
      issues.push(`${viewport.name}: bottom nav should be visible on mobile/tablet`);
    }
    if (viewport.width < 768 && state.mobileListDisplay === 'none') {
      issues.push(`${viewport.name}: mobile card list should be visible on phone`);
    }
    if (viewport.width >= 1024 && state.tableScrollDelta > 1) {
      issues.push(`${viewport.name}: desktop academic year table needs horizontal scroll (${state.tableScrollDelta}px)`);
    }
    if (state.createPanelExists) {
      issues.push(`${viewport.name}: add academic year form should be modal, not side panel`);
    }
    if (!state.createModalExists || !state.createTriggerIsModal) {
      issues.push(`${viewport.name}: add academic year modal or trigger is missing`);
    }
    if (state.panelTitleSize >= state.pageTitleSize * 0.8) {
      issues.push(`${viewport.name}: panel subtitle font is too large (${state.panelTitleSize}px vs page title ${state.pageTitleSize}px)`);
    }
    if (state.createModalTitleSize >= state.pageTitleSize * 0.8) {
      issues.push(`${viewport.name}: modal subtitle font is too large (${state.createModalTitleSize}px vs page title ${state.pageTitleSize}px)`);
    }
  }

  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto(`${BASE_URL}/admin/academic-years`, { waitUntil: 'load' });
  const createTriggerCount = await page.locator('[data-academic-year-create-trigger]').count();
  if (createTriggerCount !== 1) {
    issues.push(`expected one create modal trigger, got ${createTriggerCount}`);
  } else {
    await page.click('[data-academic-year-create-trigger]');
    await page.waitForSelector('#createAcademicYear.show', { timeout: 5000 }).catch(() => {});
    const createModalState = await page.evaluate(() => {
      const modal = document.querySelector('#createAcademicYear');
      return {
        visible: modal?.classList.contains('show') || false,
        labelled: modal?.getAttribute('aria-labelledby') === 'createAcademicYearTitle',
        yearFieldVisible: Boolean(document.querySelector('#create_year')),
        submitVisible: Boolean(document.querySelector('[data-academic-year-create-form] button[type="submit"]')),
      };
    });
    if (!createModalState.visible || !createModalState.labelled || !createModalState.yearFieldVisible || !createModalState.submitVisible) {
      issues.push(`create modal did not open with accessible form controls: ${JSON.stringify(createModalState)}`);
    }
    await page.keyboard.press('Escape');
  }

  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto(`${BASE_URL}/admin/academic-years`, { waitUntil: 'load' });
  await createYear(page, invalidYear, false);
  await page.goto(`${BASE_URL}/admin/academic-years`, { waitUntil: 'load' });
  const invalidState = await getYearState(page, invalidYear);
  if (invalidState) {
    issues.push(`invalid non-sequential academic year was accepted: ${invalidYear}`);
    if (invalidState.id) {
      await postForm(page, `${BASE_URL}/admin/academic-years/${invalidState.id}/delete`);
      await page.goto(`${BASE_URL}/admin/academic-years`, { waitUntil: 'load' });
    }
  }

  await createYear(page, yearA, true);
  await page.goto(`${BASE_URL}/admin/academic-years`, { waitUntil: 'load' });
  await createYear(page, yearB, false);
  await page.goto(`${BASE_URL}/admin/academic-years`, { waitUntil: 'load' });

  let stateA = await getYearState(page, yearA);
  let stateB = await getYearState(page, yearB);
  if (!stateA || !stateB) {
    issues.push(`created academic years not visible: ${JSON.stringify({ yearA: stateA, yearB: stateB })}`);
  } else if (!stateA.active || stateB.active) {
    issues.push(`initial active state wrong: ${JSON.stringify({ yearA: stateA, yearB: stateB })}`);
  }

  const bId = stateB?.id;
  if (bId) {
    await postForm(page, `${BASE_URL}/admin/academic-years/${bId}/activate`);
    await page.goto(`${BASE_URL}/admin/academic-years`, { waitUntil: 'load' });
    stateA = await getYearState(page, yearA);
    stateB = await getYearState(page, yearB);
    if (!stateB?.active || stateA?.active) {
      issues.push(`activating ${yearB} did not deactivate ${yearA}: ${JSON.stringify({ yearA: stateA, yearB: stateB })}`);
    }
  }

  if (stateA?.id) {
    await page.click(`[data-academic-year-edit="${stateA.id}"]`);
    await page.fill(`[data-academic-year-edit-form="${stateA.id}"] input[name="label"]`, `QA Tahun Pelajaran ${yearA} Edited`);
    await page.fill(`[data-academic-year-edit-form="${stateA.id}"] textarea[name="notes"]`, 'QA edited note');
    await page.click(`[data-academic-year-edit-form="${stateA.id}"] button[type="submit"]`);
    await page.waitForLoadState('load');
    await page.goto(`${BASE_URL}/admin/academic-years`, { waitUntil: 'load' });
    stateA = await getYearState(page, yearA);
    if (!stateA?.text.includes('Edited') || !stateA?.text.includes('QA edited note')) {
      issues.push(`edited academic year was not reflected in UI: ${JSON.stringify(stateA)}`);
    }
  }

  for (const state of [stateA, stateB]) {
    if (!state?.id) continue;
    await postForm(page, `${BASE_URL}/admin/academic-years/${state.id}/delete`);
  }

  const cleanup = await page.evaluate(({ yearA, yearB }) => ({
    yearAVisible: Boolean(document.querySelector(`[data-academic-year-row][data-year="${yearA}"], [data-academic-year-card][data-year="${yearA}"]`)),
    yearBVisible: Boolean(document.querySelector(`[data-academic-year-row][data-year="${yearB}"], [data-academic-year-card][data-year="${yearB}"]`)),
    activeRows: document.querySelectorAll('[data-academic-year-row][data-active="1"]').length,
  }), { yearA, yearB });

  if (cleanup.yearAVisible || cleanup.yearBVisible) {
    issues.push(`delete cleanup failed: ${JSON.stringify(cleanup)}`);
  }
  if (cleanup.activeRows !== 1) {
    issues.push(`expected exactly one active academic year after cleanup, got ${cleanup.activeRows}`);
  }

  const lucideIssues = consoleIssues.filter((issue) => issue.includes('icon name was not found'));
  if (lucideIssues.length > 0) {
    issues.push(`Lucide warnings: ${lucideIssues.join(' | ')}`);
  }

  const appIssues = consoleIssues.filter((issue) => !issue.includes('icon name was not found'));
  if (appIssues.length > 0) {
    issues.push(`Console issues: ${appIssues.join(' | ')}`);
  }

  if (issues.length > 0) {
    console.error(issues.join('\n'));
    process.exitCode = 1;
  } else {
    console.log('Admin academic years smoke passed');
  }

  await browser.close();
})();
