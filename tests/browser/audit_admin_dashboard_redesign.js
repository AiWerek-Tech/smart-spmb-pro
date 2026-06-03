const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8080';
const REQUIRED_ADMIN_LABELS = ['Dashboard', 'Pendaftar', 'Verifikasi', 'Seleksi', 'Lainnya'];
const VIEWPORTS = [
  { name: 'desktop-1366', width: 1366, height: 900 },
  { name: 'tablet-768', width: 768, height: 1024 },
  { name: 'mobile-390', width: 390, height: 844 },
  { name: 'mobile-360', width: 360, height: 740 },
];

async function login(page) {
  await page.goto(`${BASE_URL}/auth/login`, { waitUntil: 'domcontentloaded' });
  await page.fill('#email', 'admin@smartspmbpro.sch.id');
  await page.fill('#password', 'Admin@12345');
  await page.click('button[type="submit"]');
  await page.waitForURL(/\/admin\/dashboard|\/dashboard/, { timeout: 10000 }).catch(() => {});
}

function isUnexpectedConsoleIssue(text) {
  return ![
    'favicon',
    'Failed to load resource: the server responded with a status of 404',
  ].some((allowed) => text.includes(allowed));
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 390, height: 844 } });
  const consoleIssues = [];
  const issues = [];

  page.on('console', (msg) => {
    const text = msg.text();
    if ((msg.type() === 'warning' || msg.type() === 'error') && isUnexpectedConsoleIssue(text)) {
      consoleIssues.push(text);
    }
  });

  page.on('pageerror', (error) => {
    consoleIssues.push(`Page error: ${error.message}`);
  });

  await login(page);

  for (const viewport of VIEWPORTS) {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });
    const response = await page.goto(`${BASE_URL}/admin/dashboard`, { waitUntil: 'load' });
    await page.waitForTimeout(700);

    if (!response || response.status() >= 400) {
      issues.push(`${viewport.name}: dashboard response status ${response ? response.status() : 'missing'}`);
      continue;
    }

    const snapshot = await page.evaluate(() => {
      const bottomNav = document.querySelector('.dashboard-mobile-bottom-nav');
      const summaryCard = document.querySelector('[data-dashboard-summary-card]');
      const heroPanel = document.querySelector('.admin-hero-panel');
      const panel = document.querySelector('.admin-panel');
      const scrollDelta = document.documentElement.scrollWidth - document.documentElement.clientWidth;
      const bodyText = document.body.innerText;

      return {
        h1: document.querySelector('#admin-dashboard-title')?.textContent.trim() || '',
        navDisplay: bottomNav ? getComputedStyle(bottomNav).display : 'missing',
        navLabels: [...document.querySelectorAll('.dashboard-mobile-bottom-nav .dashboard-bottom-label')].map((el) => el.textContent.trim()),
        activeLabels: [...document.querySelectorAll('.dashboard-mobile-bottom-nav .dashboard-bottom-item.active .dashboard-bottom-label')].map((el) => el.textContent.trim()),
        summaryCards: document.querySelectorAll('[data-dashboard-summary-card]').length,
        quickActions: document.querySelectorAll('.admin-action').length,
        queueCards: document.querySelectorAll('.admin-queue-card').length,
        hasMojibake: bodyText.includes('Â') || bodyText.includes('�'),
        scrollDelta,
        cardRadius: summaryCard ? parseFloat(getComputedStyle(summaryCard).borderRadius) : null,
        heroRadius: heroPanel ? parseFloat(getComputedStyle(heroPanel).borderRadius) : null,
        panelRadius: panel ? parseFloat(getComputedStyle(panel).borderRadius) : null,
      };
    });

    if (snapshot.h1 !== 'Dashboard SPMB') {
      issues.push(`${viewport.name}: unexpected h1 "${snapshot.h1}"`);
    }

    if (snapshot.summaryCards < 5) {
      issues.push(`${viewport.name}: expected at least 5 summary cards, got ${snapshot.summaryCards}`);
    }

    if (snapshot.quickActions < 6) {
      issues.push(`${viewport.name}: expected at least 6 quick actions, got ${snapshot.quickActions}`);
    }

    if (snapshot.hasMojibake) {
      issues.push(`${viewport.name}: visible text contains mojibake/replacement characters`);
    }

    if (snapshot.scrollDelta > 1) {
      issues.push(`${viewport.name}: horizontal overflow delta ${snapshot.scrollDelta}px`);
    }

    for (const [name, value] of Object.entries({
      cardRadius: snapshot.cardRadius,
      heroRadius: snapshot.heroRadius,
      panelRadius: snapshot.panelRadius,
    })) {
      if (value === null || value > 8) {
        issues.push(`${viewport.name}: ${name} expected <= 8px, got ${value}`);
      }
    }

    if (viewport.width < 992) {
      if (JSON.stringify(snapshot.navLabels) !== JSON.stringify(REQUIRED_ADMIN_LABELS)) {
        issues.push(`${viewport.name}: admin mobile nav labels ${JSON.stringify(snapshot.navLabels)} !== ${JSON.stringify(REQUIRED_ADMIN_LABELS)}`);
      }

      if (snapshot.activeLabels.length !== 1 || snapshot.activeLabels[0] !== 'Dashboard') {
        issues.push(`${viewport.name}: dashboard active nav labels ${JSON.stringify(snapshot.activeLabels)}`);
      }

      await page.click('#dashboard-more-toggle');
      await page.waitForTimeout(250);

      const openedDrawer = await page.evaluate(() => {
        const sidebar = document.querySelector('#sidebar');
        const toggle = document.querySelector('#dashboard-more-toggle');
        const rect = sidebar ? sidebar.getBoundingClientRect() : null;
        return {
          shown: sidebar ? sidebar.classList.contains('show') : false,
          ariaExpanded: toggle ? toggle.getAttribute('aria-expanded') : null,
          visible: rect ? rect.top < window.innerHeight && rect.bottom > 0 : false,
          inViewport: rect ? rect.left >= 0 && rect.right <= window.innerWidth && rect.bottom <= window.innerHeight : false,
        };
      });

      if (!openedDrawer.shown || !openedDrawer.visible || !openedDrawer.inViewport || openedDrawer.ariaExpanded !== 'true') {
        issues.push(`${viewport.name}: mobile drawer did not open cleanly ${JSON.stringify(openedDrawer)}`);
      }

      await page.keyboard.press('Escape');
      await page.waitForTimeout(250);

      const closedDrawer = await page.evaluate(() => {
        const sidebar = document.querySelector('#sidebar');
        const toggle = document.querySelector('#dashboard-more-toggle');
        return {
          shown: sidebar ? sidebar.classList.contains('show') : false,
          ariaExpanded: toggle ? toggle.getAttribute('aria-expanded') : null,
        };
      });

      if (closedDrawer.shown || closedDrawer.ariaExpanded !== 'false') {
        issues.push(`${viewport.name}: mobile drawer did not close with Escape ${JSON.stringify(closedDrawer)}`);
      }
    } else if (snapshot.navDisplay !== 'none') {
      issues.push(`${viewport.name}: bottom nav should be hidden on desktop, display=${snapshot.navDisplay}`);
    }
  }

  await page.setViewportSize({ width: 390, height: 844 });
  const verificationResponse = await page.goto(`${BASE_URL}/operator/registrants?status=submitted`, { waitUntil: 'load' });
  await page.waitForTimeout(700);

  if (!verificationResponse || verificationResponse.status() >= 400) {
    issues.push(`verification route response status ${verificationResponse ? verificationResponse.status() : 'missing'}`);
  } else {
    const activeLabels = await page.evaluate(() => [...document.querySelectorAll('.dashboard-mobile-bottom-nav .dashboard-bottom-item.active .dashboard-bottom-label')].map((el) => el.textContent.trim()));
    if (activeLabels.length !== 1 || activeLabels[0] !== 'Verifikasi') {
      issues.push(`verification route active nav labels ${JSON.stringify(activeLabels)}`);
    }
  }

  const lucideIssues = consoleIssues.filter((issue) => issue.includes('icon name was not found'));
  if (lucideIssues.length > 0) {
    issues.push(`Lucide warnings: ${lucideIssues.join(' | ')}`);
  }

  const appConsoleIssues = consoleIssues.filter((issue) => !issue.includes('icon name was not found'));
  if (appConsoleIssues.length > 0) {
    issues.push(`Console issues: ${appConsoleIssues.join(' | ')}`);
  }

  if (issues.length > 0) {
    console.error(issues.join('\n'));
    process.exitCode = 1;
  } else {
    console.log('Admin dashboard redesign smoke passed');
  }

  await browser.close();
})();
