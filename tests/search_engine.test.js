const fs = require('fs');
const path = require('path');

function testSearch() {
  console.log('\n--- 4. SEARCH ENGINE & FORMULATION INDEXING TEST ---');

  // Mock DOM environment in Node
  global.window = global;
  global.document = {
    querySelectorAll: () => [],
    getElementById: () => null,
    addEventListener: () => {}
  };

  // Load products & search code
  const prodCode = fs.readFileSync(path.join(__dirname, '..', 'js', 'products.js'), 'utf8');
  eval(prodCode);

  const searchCode = fs.readFileSync(path.join(__dirname, '..', 'js', 'search.js'), 'utf8');
  eval(searchCode);

  const search = window.BLUEZONE_SEARCH;
  let passed = 0;
  let total = 5;

  // 1. Empty query returns all products
  const all = search.query('');
  if (all.length === 6) {
    passed++;
    console.log(`✓ 1. Empty query returns full catalog (${all.length} formulations)`);
  } else console.error('✖ Failed empty query');

  // 2. Query by product title ("mind")
  const mind = search.query('mind');
  if (mind.length === 1 && mind[0].id === 'blue-mind') {
    passed++;
    console.log('✓ 2. Query "mind" matches BLUE MIND');
  } else console.error('✖ Failed title query');

  // 3. Query by category ("Cognitive")
  const cognitive = search.query('Cognitive');
  if (cognitive.length >= 1 && cognitive[0].id === 'blue-mind') {
    passed++;
    console.log('✓ 3. Query "Cognitive" matches formulation category');
  } else console.error('✖ Failed category query');

  // 4. Query by active ingredient ("Ginkgo")
  const ginkgo = search.query('ginkgo');
  if (ginkgo.length >= 1 && ginkgo.some(p => p.id === 'blue-mind')) {
    passed++;
    console.log('✓ 4. Query "ginkgo" matches formulation by deep botanical ingredient');
  } else console.error('✖ Failed ingredient query');

  // 5. Zero-match query returns empty array
  const empty = search.query('nonexistent_compound_xyz');
  if (empty.length === 0) {
    passed++;
    console.log('✓ 5. Nonexistent query returns clean empty array');
  } else console.error('✖ Failed zero-match query');

  console.log(`\nSearch Tests Passed: ${passed}/${total}`);
  return passed === total;
}

if (require.main === module) {
  const ok = testSearch();
  process.exit(ok ? 0 : 1);
}

module.exports = testSearch;
