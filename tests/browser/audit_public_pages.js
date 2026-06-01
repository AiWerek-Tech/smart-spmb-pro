const { chromium } = require('playwright');

const PUBLIC_PAGES = [
  { name: 'Home', path: '/', interact: 'home' },
  { name: 'Profil', path: '/profil', interact: 'gallery' },
  { name: 'SPMB', path: '/spmb' },
  { name: 'Pengumuman', path: '/pengumuman', interact: 'modal' },
  { name: 'Kontak', path: '/kontak', interact: 'form' },
  { name: 'Galeri', path: '/galeri', interact: 'galleryModal' },
  { name: 'Lingkungan Kampus', path: '/lingkungan-kampus', interact: 'galleryModal' },
  { name: 'Kebijakan Privasi', path: '/kebijakan-privasi' },
  { name: 'Syarat Ketentuan', path: '/syarat-ketentuan' },
  { name: 'Redirect Profil Sejarah', path: '/profil/sejarah' },
  { name: 'Redirect Biaya', path: '/biaya' },
  { name: 'Redirect FAQ', path: '/faq' },
];

const AUTH_PAGES = [
  { name: 'Login', path: '/auth/login', interact: 'authForm' },
  { name: 'Register', path: '/auth/register', interact: 'authForm' },
  { name: 'Forgot Password', path: '/auth/forgot', interact: 'authForm' },
  { name: 'Reset Password', path: '/auth/reset', interact: 'authForm' },
];

async function runInteract(page, type) {
  const notes = [];
  try {
    if (type === 'home') {
      const broken = await page.evaluate(() => {
        const imgs = [...document.querySelectorAll('.sp-hero-mockup img, .sp-gallery-img-wrapper img, .sp-news-image img')];
        return imgs.filter((img) => {
          const rect = img.getBoundingClientRect();
          const visible = rect.width > 0 && rect.height > 0 && getComputedStyle(img).visibility !== 'hidden';
          return visible && img.complete && img.naturalWidth === 0 && !img.src.includes('placeholder');
        }).length;
      });
      if (broken > 0) notes.push(`${broken} hero/gallery/news image(s) failed to load`);
      const carousel = page.locator('#heroCarousel');
      if (await carousel.count()) {
        const nextBtn = page.locator('#heroCarousel .carousel-control-next, #heroCarousel [data-bs-slide="next"]').first();
        if (await nextBtn.count()) {
          await nextBtn.click();
          await page.waitForTimeout(400);
        }
      }
      const homeNews = page.locator('.sp-news-card[data-bs-toggle="modal"]').first();
      if (await homeNews.count()) {
        await page.locator('.sp-news-section').scrollIntoViewIfNeeded();
        await page.waitForTimeout(600);
        await homeNews.click();
        await page.waitForTimeout(400);
        if (!(await page.locator('.modal.show').count())) notes.push('Home news modal did not open');
        await page.keyboard.press('Escape');
        await page.waitForTimeout(200);
      }
    }
    if (type === 'gallery') {
      const broken = await page.evaluate(() => {
        const imgs = [...document.querySelectorAll('img.object-fit-cover, .sp-gallery-item img')];
        return imgs.filter((img) => img.naturalWidth === 0).length;
      });
      if (broken > 0) notes.push(`${broken} gallery image(s) failed to load`);
    }
    if (type === 'galleryModal') {
      const broken = await page.evaluate(() => {
        const imgs = [...document.querySelectorAll('img.object-fit-cover, .sp-gallery-card-btn img')];
        return imgs.filter((img) => img.naturalWidth === 0).length;
      });
      if (broken > 0) notes.push(`${broken} gallery image(s) failed to load`);

      const trigger = page.locator('.sp-gallery-card-btn[data-bs-toggle="modal"]').first();
      if (await trigger.count()) {
        await trigger.click();
        await page.waitForTimeout(400);
        if (!(await page.locator('.modal.show').count())) notes.push('Gallery modal did not open');
        await page.keyboard.press('Escape');
      }
    }
    if (type === 'modal') {
      const trigger = page.locator('[data-bs-toggle="modal"]').first();
      if (await trigger.count()) {
        await trigger.click();
        await page.waitForTimeout(400);
        const open = await page.locator('.modal.show').count();
        if (!open) notes.push('Announcement modal did not open');
        await page.keyboard.press('Escape');
      }
    }
    if (type === 'form') {
      const form = page.locator('form').first();
      if (!(await form.count())) notes.push('Contact form missing');
    }
    if (type === 'authForm') {
      const hasEmail = await page.locator('input[type="email"], input[name="email"]').count();
      const hasSubmit = await page.locator('button[type="submit"]').count();
      if (!hasSubmit) notes.push('Auth submit button missing');
      if (!hasEmail && !(await page.locator('input[name="password"], input[name="token"]').count())) {
        notes.push('Auth form fields missing');
      }
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
  await page.waitForTimeout(1500);

  const status = response ? response.status() : 0;
  const data = await page.evaluate(() => ({
    bootstrap: typeof window.bootstrap !== 'undefined',
    lucide: typeof window.lucide !== 'undefined',
    scriptCount: document.querySelectorAll('script').length,
    iframeCount: document.querySelectorAll('iframe').length,
    navbar: !!document.getElementById('main-navbar'),
    footer: !!document.querySelector('footer'),
    authWrapper: !!document.querySelector('.auth-wrapper'),
    lucideIcons: document.querySelectorAll('i[data-lucide]').length,
    lucideSvg: document.querySelectorAll('svg.lucide, .lucide').length,
    hasForm: !!document.querySelector('form'),
    finalUrl: window.location.pathname + window.location.hash,
  }));

  const interactIssues = cfg.interact ? await runInteract(page, cfg.interact) : [];

  page.off('console', handler);
  page.off('pageerror', pageErrorHandler);

  const issues = [...interactIssues];
  if (status !== 200) issues.push(`HTTP ${status}`);
  if (!data.bootstrap) issues.push('Bootstrap missing');
  if (data.scriptCount < 3) issues.push(`Only ${data.scriptCount} scripts`);
  const mapsIframe = await page.evaluate(() => {
    const iframe = document.querySelector('iframe');
    return iframe ? iframe.src : '';
  });
  const isValidMapsIframe = mapsIframe.includes('/maps/embed') || mapsIframe.includes('output=embed');
  const allowsMediaIframe = ['/galeri', '/lingkungan-kampus', '/profil'].includes(cfg.path) && mapsIframe.includes('youtube.com/embed/');
  if (data.iframeCount > 0 && !(cfg.path === '/kontak' && data.iframeCount === 1 && isValidMapsIframe) && !allowsMediaIframe) {
    issues.push(`Unexpected iframe count ${data.iframeCount}`);
  }
  if (cfg.path === '/kontak' && data.iframeCount === 1 && !isValidMapsIframe) {
    issues.push(`Maps iframe invalid src: ${mapsIframe}`);
  }

  const isAuth = cfg.path.startsWith('/auth/');
  if (isAuth) {
    if (!data.authWrapper) issues.push('Missing auth wrapper');
  } else {
    if (!data.navbar) issues.push('Missing navbar');
    if (!data.footer) issues.push('Missing footer');
  }

  if (data.lucideIcons > 0 && data.lucideSvg === 0 && !data.lucide) {
    issues.push('Lucide icons not rendered');
  }

  if (cfg.path === '/profil/sejarah' && !data.finalUrl.includes('profil')) {
    issues.push(`Redirect did not reach profil (${data.finalUrl})`);
  }
  if (cfg.path === '/biaya' && !data.finalUrl.includes('spmb')) {
    issues.push(`Redirect did not reach spmb (${data.finalUrl})`);
  }
  if (cfg.path === '/faq' && !data.finalUrl.includes('spmb')) {
    issues.push(`Redirect did not reach spmb (${data.finalUrl})`);
  }

  if (cfg.interact === 'authForm' && !data.hasForm) issues.push('Missing form');

  const jsErrors = [...errors, ...consoleErrors.filter((e) =>
    !e.includes('favicon') && !e.includes('id.json') && !e.includes('X-Frame-Options')
  )];

  return { name: cfg.name, path: cfg.path, pass: issues.length === 0 && jsErrors.length === 0, issues, jsErrors, data };
}

async function auditCheckResult(page) {
  const errors = [];
  const consoleErrors = [];
  page.on('pageerror', (e) => errors.push(e.message));
  page.on('console', (msg) => { if (msg.type() === 'error') consoleErrors.push(msg.text()); });

  await page.goto('http://localhost:8080/pengumuman', { waitUntil: 'load' });
  await page.waitForTimeout(1000);

  const issues = [];
  const searchForm = page.locator('form[action*="cek-hasil"]');
  if (!(await searchForm.count())) {
    issues.push('Cek hasil form not found on pengumuman page');
  } else {
    await page.fill('#search', 'NONEXISTENT_TEST_QUERY_XYZ');
    await page.click('form[action*="cek-hasil"] button[type="submit"]');
    await page.waitForTimeout(2000);

    const body = await page.content();
    if (!body.includes('Tidak Ditemukan') && !body.includes('Data Tidak Ditemukan') && !body.includes('Cek Hasil')) {
      issues.push('Check result page missing expected content');
    }
  }

  page.removeAllListeners('pageerror');
  page.removeAllListeners('console');
  const jsErrors = [...errors, ...consoleErrors.filter((e) => !e.includes('favicon') && !e.includes('X-Frame-Options'))];

  return {
    name: 'Cek Hasil POST',
    path: 'POST /pengumuman/cek-hasil',
    pass: issues.length === 0 && jsErrors.length === 0,
    issues,
    jsErrors,
  };
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  const results = [];

  for (const cfg of PUBLIC_PAGES) {
    results.push(await auditPage(page, cfg));
  }
  for (const cfg of AUTH_PAGES) {
    results.push(await auditPage(page, cfg));
  }
  results.push(await auditCheckResult(page));

  console.log('\n=== PUBLIC & AUTH AUDIT ===');
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
