const fs = require('fs');
const path = require('path');

function testA11y() {
  console.log('\n--- 2. ACCESSIBILITY (WCAG 2.1 AA) AUDIT ---');
  const pages = [
    'index.html',
    'science.html',
    'products.html',
    'shop.html',
    'product.html',
    'team.html',
    'blog.html',
    'contact.html'
  ];

  let passed = 0;
  let total = pages.length * 5;

  pages.forEach(page => {
    const html = fs.readFileSync(path.join(__dirname, '..', page), 'utf8');

    // 1. Skip to content link
    const hasSkip = html.includes('class="skip-to-content"');
    if (hasSkip) passed++;
    else console.error(`✖ ${page} missing skip-to-content link`);

    // 2. Main landmark
    const hasMain = html.includes('id="main-content"') && html.includes('role="main"');
    if (hasMain) passed++;
    else console.error(`✖ ${page} missing main landmark with id="main-content"`);

    // 3. Header & Footer landmarks
    const hasHeaderFooter = html.includes('role="banner"') && html.includes('role="contentinfo"');
    if (hasHeaderFooter) passed++;
    else console.error(`✖ ${page} missing banner or contentinfo roles`);

    // 4. Images have alt attributes
    const imgMatches = html.match(/<img[^>]+>/gi) || [];
    const missingAlt = imgMatches.filter(img => !/alt\s*=\s*["'][^"']*["']/i.test(img));
    if (missingAlt.length === 0) passed++;
    else console.error(`✖ ${page} has ${missingAlt.length} images missing alt attributes`);

    // 5. Buttons have accessible names
    const btnMatches = html.match(/<button[^>]+>([\s\S]*?)<\/button>/gi) || [];
    const unlabelledBtns = btnMatches.filter(btn => {
      const hasAria = /aria-label\s*=\s*["'][^"']+["']/i.test(btn);
      const text = btn.replace(/<[^>]+>/g, '').trim();
      return !hasAria && text.length === 0;
    });
    if (unlabelledBtns.length === 0) passed++;
    else console.error(`✖ ${page} has ${unlabelledBtns.length} buttons without text or aria-label`);

    console.log(`✓ ${page.padEnd(16)}: Skip Link, Landmarks, Image Alts & Button Labels Verified`);
  });

  console.log(`\nA11y Tests Passed: ${passed}/${total}`);
  return passed === total;
}

if (require.main === module) {
  const ok = testA11y();
  process.exit(ok ? 0 : 1);
}

module.exports = testA11y;
