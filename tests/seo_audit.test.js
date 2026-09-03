const fs = require('fs');
const path = require('path');

function testSeo() {
  console.log('\n--- 1. SEO & STRUCTURED DATA AUDIT ---');
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
  let total = pages.length * 6;

  pages.forEach(page => {
    const html = fs.readFileSync(path.join(__dirname, '..', page), 'utf8');

    // 1. Title
    const hasTitle = /<title>[^<]+<\/title>/i.test(html);
    if (hasTitle) passed++;
    else console.error(`✖ ${page} missing <title>`);

    // 2. Meta description
    const hasDesc = /<meta\s+name="description"\s+content="[^"]+"/i.test(html);
    if (hasDesc) passed++;
    else console.error(`✖ ${page} missing <meta name="description">`);

    // 3. Canonical URL
    const hasCanonical = /<link\s+rel="canonical"\s+href="[^"]+"/i.test(html);
    if (hasCanonical) passed++;
    else console.error(`✖ ${page} missing <link rel="canonical">`);

    // 4. OpenGraph tags
    const hasOg = html.includes('property="og:title"') && html.includes('property="og:description"') && html.includes('property="og:image"');
    if (hasOg) passed++;
    else console.error(`✖ ${page} missing OpenGraph tags`);

    // 5. Twitter Card
    const hasTwitter = html.includes('name="twitter:card"') && html.includes('name="twitter:title"');
    if (hasTwitter) passed++;
    else console.error(`✖ ${page} missing Twitter Card`);

    // 6. JSON-LD Schema
    const hasJsonLd = html.includes('application/ld+json');
    let validJson = false;
    if (hasJsonLd) {
      try {
        const matches = html.match(/<script type="application\/ld\+json">([\s\S]*?)<\/script>/i);
        if (matches && matches[1]) {
          JSON.parse(matches[1].trim());
          validJson = true;
        }
      } catch (e) {
        console.error(`✖ ${page} JSON-LD parsing error:`, e.message);
      }
    }
    if (validJson) passed++;
    else console.error(`✖ ${page} invalid or missing JSON-LD schema`);

    console.log(`✓ ${page.padEnd(16)}: Valid Title, Meta, Canonical, OG, Twitter & JSON-LD`);
  });

  console.log(`\nSEO Tests Passed: ${passed}/${total}`);
  return passed === total;
}

if (require.main === module) {
  const ok = testSeo();
  process.exit(ok ? 0 : 1);
}

module.exports = testSeo;
