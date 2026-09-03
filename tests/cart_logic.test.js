const fs = require('fs');
const path = require('path');

function testCart() {
  console.log('\n--- 3. CART LOGIC & DATA INTEGRITY TEST ---');

  // Mock DOM environment in Node
  const storage = {};
  global.localStorage = {
    getItem: (k) => storage[k] || null,
    setItem: (k, v) => { storage[k] = v; },
    removeItem: (k) => { delete storage[k]; }
  };
  global.window = global;
  global.document = {
    querySelectorAll: () => [],
    getElementById: () => null,
    addEventListener: () => {}
  };

  // Load products & cart code
  const prodCode = fs.readFileSync(path.join(__dirname, '..', 'js', 'products.js'), 'utf8');
  eval(prodCode);

  const cartCode = fs.readFileSync(path.join(__dirname, '..', 'js', 'cart.js'), 'utf8');
  eval(cartCode);

  const cart = window.BLUEZONE_CART;
  let passed = 0;
  let total = 6;

  // 1. Initial empty cart
  cart.clear();
  if (cart.get().length === 0 && cart.getSubtotal() === 0) {
    passed++;
    console.log('✓ 1. Initial cart is empty and subtotal is 0');
  } else console.error('✖ Failed initial empty cart');

  // 2. Add single product (BLUE MIND: $68.00)
  cart.add('blue-mind', 1);
  const itemsAfter1 = cart.get();
  if (itemsAfter1.length === 1 && itemsAfter1[0].id === 'blue-mind' && itemsAfter1[0].quantity === 1) {
    passed++;
    console.log('✓ 2. Successfully added 1 BLUE MIND formulation');
  } else console.error('✖ Failed add single product');

  // 3. Increment quantity
  cart.updateQty('blue-mind', 2);
  const itemsAfterInc = cart.get();
  if (itemsAfterInc[0].quantity === 3) {
    passed++;
    console.log('✓ 3. Successfully incremented BLUE MIND quantity to 3');
  } else console.error('✖ Failed quantity increment');

  // 4. Subtotal calculation (3 * 68 = 204.00)
  const subtotal = cart.getSubtotal();
  if (Math.abs(subtotal - 204.00) < 0.01) {
    passed++;
    console.log('✓ 4. Subtotal correctly calculated: $204.00');
  } else console.error(`✖ Failed subtotal calculation: expected 204.00, got ${subtotal}`);

  // 5. Add second product (BLUE ENERGY: $58.00)
  cart.add('blue-energy', 1);
  const itemsAfter2 = cart.get();
  if (itemsAfter2.length === 2 && Math.abs(cart.getSubtotal() - 262.00) < 0.01) {
    passed++;
    console.log('✓ 5. Second product added and subtotal updated: $262.00');
  } else console.error('✖ Failed multiple products in cart');

  // 6. Remove product
  cart.remove('blue-mind');
  const itemsAfterRemove = cart.get();
  if (itemsAfterRemove.length === 1 && itemsAfterRemove[0].id === 'blue-energy') {
    passed++;
    console.log('✓ 6. Product removal works seamlessly');
  } else console.error('✖ Failed remove product');

  console.log(`\nCart Tests Passed: ${passed}/${total}`);
  return passed === total;
}

if (require.main === module) {
  const ok = testCart();
  process.exit(ok ? 0 : 1);
}

module.exports = testCart;
