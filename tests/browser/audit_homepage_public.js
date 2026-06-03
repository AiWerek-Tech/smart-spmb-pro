const { chromium } = require('playwright');

const BASE_URL = process.env.BASE_URL || 'http://localhost:8080';
const OUTPUT_DIR = process.env.AUDIT_SCREENSHOT_DIR || null;

async function collectHomeLayout(page) {
  return page.evaluate(() => {
    const doc = document.documentElement;
    const hero = document.querySelector('.sp-hero-card');
    const heroTitle = document.querySelector('.sp-hero-title');
    const heroMessage = document.querySelector('.sp-hero-eyebrow-title');
    const heroSubtitle = document.querySelector('.sp-hero-subtitle');
    const sectionTitle = document.querySelector('.sp-section-title-sm');
    const primaryCard = document.querySelector('.sp-primary-card');
    const trustBar = document.querySelector('.sp-trust-bar');
    const statsSection = document.querySelector('.sp-stats-section-new');
    const testimonialStrip = document.querySelector('.sp-testimonial-strip');
    const testimonialCards = [...document.querySelectorAll('.sp-testimonial-card')];
    const testimonialRects = testimonialCards.slice(0, 2).map((card) => card.getBoundingClientRect());
    const footer = document.querySelector('.sp-public-footer');
    const footerCta = document.querySelector('.sp-footer-cta-col');
    const footerAddress = document.querySelector('.footer-contact-item--address');
    const footerCopyMeta = document.querySelector('.sp-footer-copy-meta');
    const footerLinks = [...document.querySelectorAll('.footer-links a')];
    const footerRect = footer?.getBoundingClientRect();
    const footerLinkRows = [...new Set(footerLinks.map((link) => Math.round(link.getBoundingClientRect().top)))].length;
    const heroActionButtons = [...document.querySelectorAll('.sp-hero-actions .btn')].slice(0, 2);
    const heroActionRects = heroActionButtons.map((button) => button.getBoundingClientRect());
    const heroRect = hero?.getBoundingClientRect();
    const visibleButtons = [...document.querySelectorAll('a, button')]
      .filter((el) => {
        const rect = el.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0 && rect.top < window.innerHeight && rect.bottom > 0 && getComputedStyle(el).visibility !== 'hidden';
      })
      .slice(0, 50);
    const num = (value) => Number.parseFloat(value || '0');

    return {
      h1Text: heroTitle?.textContent?.trim() || '',
      hasHero: !!hero,
      hasHeroImage: hero ? getComputedStyle(hero).backgroundImage.includes('uploads/') : false,
      heroRadius: hero ? num(getComputedStyle(hero).borderRadius) : 0,
      primaryCardRadius: primaryCard ? num(getComputedStyle(primaryCard).borderRadius) : 0,
      h1Size: heroTitle ? num(getComputedStyle(heroTitle).fontSize) : 0,
      messageSize: heroMessage ? num(getComputedStyle(heroMessage).fontSize) : 0,
      subtitleSize: heroSubtitle ? num(getComputedStyle(heroSubtitle).fontSize) : 0,
      sectionTitleSize: sectionTitle ? num(getComputedStyle(sectionTitle).fontSize) : 0,
      heroBottom: heroRect?.bottom || 0,
      nextSectionHint: heroRect ? heroRect.bottom < window.innerHeight - 20 || !!trustBar : false,
      hasStatsSection: !!statsSection,
      hasTestimonialStrip: !!testimonialStrip,
      testimonialCount: testimonialCards.length,
      testimonialHorizontal: testimonialRects.length < 2 || Math.abs(testimonialRects[0].top - testimonialRects[1].top) < 4,
      hasFooter: !!footer,
      footerHeight: footerRect?.height || 0,
      footerCtaVisible: !!footerCta && getComputedStyle(footerCta).display !== 'none',
      footerAddressVisible: !!footerAddress && getComputedStyle(footerAddress).display !== 'none',
      footerCopyMetaVisible: !!footerCopyMeta && getComputedStyle(footerCopyMeta).display !== 'none',
      footerLinkRows,
      overflowX: doc.scrollWidth - doc.clientWidth,
      mockupVisible: !!document.querySelector('.sp-hero-visual, .sp-hero-mockup')
        && getComputedStyle(document.querySelector('.sp-hero-visual, .sp-hero-mockup')).display !== 'none',
      heroActionsSameRow: heroActionRects.length === 2 && Math.abs(heroActionRects[0].top - heroActionRects[1].top) < 3,
      heroActionWidths: heroActionRects.map((rect) => rect.width),
      buttonHeights: visibleButtons.map((el) => el.getBoundingClientRect().height),
    };
  });
}

async function auditViewport(page, label, width, height) {
  const problems = [];
  await page.setViewportSize({ width, height });
  const response = await page.goto(`${BASE_URL}/`, { waitUntil: 'domcontentloaded', timeout: 30000 });
  await page.waitForTimeout(900);

  if ((response?.status() || 0) !== 200) problems.push(`HTTP ${response?.status() || 0}`);

  const layout = await collectHomeLayout(page);
  if (!layout.hasHero) problems.push('missing homepage hero');
  if (!layout.hasHeroImage) problems.push('hero does not use uploaded image background');
  if (!layout.h1Text || layout.h1Text.includes('Wujudkan Masa Depan')) problems.push('hero H1 is not the school identity');
  if (layout.heroRadius > 8) problems.push(`hero radius ${layout.heroRadius}px exceeds 8px`);
  if (layout.primaryCardRadius > 8) problems.push(`primary action card radius ${layout.primaryCardRadius}px exceeds 8px`);
  if (layout.subtitleSize >= layout.h1Size) problems.push('hero subtitle is not smaller than hero title');
  if (layout.messageSize >= layout.h1Size) problems.push('hero supporting title is not smaller than hero title');
  if (layout.sectionTitleSize >= layout.h1Size) problems.push('section title competes with hero H1');
  if (!layout.nextSectionHint) problems.push('hero does not leave a hint of next content');
  if (layout.hasStatsSection) problems.push('duplicate statistics section is still visible');
  if (layout.testimonialCount && !layout.hasTestimonialStrip) problems.push('testimonial horizontal strip is missing');
  if (layout.testimonialCount > 1 && !layout.testimonialHorizontal) problems.push('testimonials are not laid out horizontally');
  if (!layout.hasFooter) problems.push('missing public footer');
  if (width < 768 && layout.footerHeight > 380) problems.push(`mobile footer height ${Math.round(layout.footerHeight)}px is too tall`);
  if (width < 768 && layout.footerCtaVisible) problems.push('mobile footer CTA should be hidden');
  if (width < 768 && layout.footerAddressVisible) problems.push('mobile footer address should be hidden');
  if (width < 768 && layout.footerCopyMetaVisible) problems.push('mobile footer copyright metadata should be hidden');
  if (width < 768 && layout.footerLinkRows > 3) problems.push(`mobile footer links use ${layout.footerLinkRows} rows`);
  if (layout.overflowX > 2) problems.push(`horizontal overflow ${layout.overflowX}px`);
  if (layout.mockupVisible) problems.push('legacy split hero mockup is visible');
  if (width < 768 && !layout.heroActionsSameRow) problems.push('mobile hero action buttons are not on one row');
  if (width < 768 && layout.heroActionWidths.some((buttonWidth) => buttonWidth > width * 0.8)) problems.push('mobile hero action button is too wide');
  if (layout.buttonHeights.some((buttonHeight) => buttonHeight < (width < 768 ? 40 : 32))) problems.push('visible button target below minimum size');

  if (OUTPUT_DIR) {
    await page.screenshot({ path: `${OUTPUT_DIR}/homepage-${label}.png`, fullPage: false });
  }

  return { label, pass: problems.length === 0, problems, layout };
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  const results = [
    await auditViewport(page, 'desktop', 1366, 900),
    await auditViewport(page, 'mobile', 390, 844),
  ];
  await browser.close();

  for (const result of results) {
    console.log(`[${result.pass ? 'PASS' : 'FAIL'}] Homepage ${result.label}`);
    result.problems.forEach((problem) => console.log(`       issue: ${problem}`));
  }

  const failed = results.filter((result) => !result.pass);
  console.log(`\nSUMMARY: ${results.length - failed.length}/${results.length} passed`);
  if (failed.length) process.exitCode = 1;
})();
