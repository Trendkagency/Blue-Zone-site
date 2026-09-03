# BLUE ZONE™ — Full System Architecture & E-Commerce Logic Documentation

Welcome to the comprehensive technical documentation for the **BLUE ZONE Longevity & Cellular Health** web application. This document details the clean architecture, client-side state engines, reactive UI components, and complete e-commerce lifecycle across all pages.

---

## 1. Architectural Overview & Technology Stack

BLUE ZONE is built as a zero-dependency, ultra-fast modular web application utilizing pure native web standards:

- **Markup & Semantics**: Semantic HTML5 with accessibility attributes (`aria-label`, `role`), structured JSON-LD MedicalBusiness schema, and responsive viewport configuration.
- **Styling & Tokens**: Tailwind CSS utility framework complemented by custom CSS variables and utility classes (`css/custom.css`, `css/map-loader.css`).
- **Typography & Aesthetics**: Cairo font family matrix with bespoke glassmorphism (`backdrop-blur`), subtle oceanic gradients, and light/dark theme tokens (`#031827`, `#062B49`, `#0A4F78`, `#2A8FC2`, `#67B34A`, `#E8DCC4`, `#F6F5EF`).
- **State & Logic Modules**: Modular vanilla JavaScript with dedicated namespaced global controllers attached to `window`.

```
├── index.html              # Hero, Map Loader, Longevity Zones, Featured Carousel, Protocol Quiz CTA
├── science.html            # Clinical Research, Cellular Mechanisms & Bio-Studies
├── products.html           # Formulations Showcase, Quick Compare & Protocol Quiz
├── shop.html               # Advanced E-Commerce Catalog (Filters, Range Slider, Sort, Compare, Quiz)
├── product.html            # Dynamic Product Detail (Dosage Matrix, Reviews, Subscriptions)
├── team.html               # Scientific Advisory Board & Medical Leadership
├── blog.html               # Longevity Journal & Clinical Dispatches
├── contact.html            # Inquiries, Headquarters, & Interactive FAQs
├── css/
│   ├── custom.css          # Design tokens, keyframes, sheen effects, dock, scroll bar, drawer animation
│   └── map-loader.css      # World map preloader animation
├── js/
│   ├── products.js         # Master formulation matrix with clinical dosages
│   ├── theme.js            # Light/Dark mode localStorage manager
│   ├── cart.js             # Self-healing cart engine, drawer upsells, promo coupons, threshold shipping
│   ├── wishlist.js         # Reactive wishlist manager & heart icon synchronizer
│   ├── search.js           # Instant fuzzy search across titles, tags, and ingredients
│   ├── app.js              # Global coordinator, Compare modal, Quiz modal, Quick View, Checkout, Back-to-Top
│   ├── hero-slider.js      # Hero animation & story carousel
│   ├── product-slider.js   # Touch-enabled responsive product card slider
│   └── map.js              # Interactive Blue Zone coordinate explorer
└── components/
    ├── header.html         # Standardized 7-link navigation header
    └── cart-drawer.html    # Offcanvas sliding cart with promo code engine
```

---

## 2. Centralized Product Matrix (`js/products.js`)

All pages hydrate from a unified master data layer exposed globally at `window.BLUEZONE_PRODUCTS`.

### Data Model Schema:
```javascript
{
  id: "blue-mind",
  name: "BLUE MIND",
  tagline: "Daily Cognitive & Nootropic Support",
  category: "COGNITIVE",
  price: 68.00,
  rating: 4.9,
  reviewsCount: 142,
  image: "assets/products/blue-mind.jpg",
  description: "A precision-engineered cognitive complex designed to optimize memory, heighten focus...",
  shortDesc: "Comprehensive Nootropic & Cellular Brain Health Matrix.",
  isFlagship: true,
  ingredients: {
    cognitive: [
      { name: "Ginkgo Biloba", dose: "120 mg" },
      { name: "Phosphatidylserine", dose: "100 mg" },
      { name: "Phosphatidylcholine", dose: "150 mg" },
      { name: "Co-Q10 (Ubiquinol)", dose: "100 mg" },
      { name: "L-Glutathione", dose: "50 mg" }
    ],
    minerals: [...],
    vitamins: [...]
  },
  functionalAreas: [
    { name: "Memory Enhancement", desc: "Supports synaptic plasticity..." },
    { name: "Focus & Executive Function", desc: "Sustained mental clarity..." }
  ],
  science: "Formulated using bio-identical phospholipid carriers...",
  usage: "Take 2 capsules daily in the morning with a glass of water..."
}
```

---

## 3. E-Commerce Engine & Reactive State

### Cart Engine (`js/cart.js`)
- **Self-Healing Hydration**: Verifies and repairs missing or malformed cart items stored in `localStorage` by matching against `window.BLUEZONE_PRODUCTS`.
- **Safe Number Formatting**: Implements `safeNumber()` and `formatPrice()` helpers preventing runtime exceptions.
- **Smart Drawer Upsell Engine**: Analyzes current cart items and displays complementary "Recommended Pairings" with 1-click add buttons.
- **Dynamic Tiered Free Shipping Bar**: Real-time progress towards the $75.00 threshold.
- **Discount & Promo Code Engine**:
  - `LONGEVITY10` (10% off)
  - `BLUEZONE20` (20% off)
  - `WELCOME15` (15% off)
- **Subscription vs. One-Time Purchase**: 15% discount for recurring monthly orders.

### Wishlist Engine (`js/wishlist.js`)
- Instant toggle with reactive badge counter.
- Offcanvas sliding wishlist drawer.
- 1-click "Move to Cart" action.

### Longevity Protocol Quiz & Comparison Engine (`js/app.js`)
- **Interactive Formulation Comparison (`BLUEZONE_APP.openCompare()`)**: Side-by-side clinical dossier comparing actives, category, usage, price, and instant add-to-cart.
- **Find Your Longevity Protocol (`BLUEZONE_APP.openQuiz()`)**: 3-question physiological assessment recommending tailored 2-bottle synergy stacks with an exclusive 15% bundle discount.

---

## 4. Performance & Mobile Navigation Features

1. **Top Scroll Progress Indicator**: Real-time gradient progress bar fixed to the top of the viewport.
2. **Floating Back-to-Top Button**: Appears smoothly once scrolled past 350px with smooth return.
3. **Mobile Bottom Quick Action Dock**: Persistent bottom navigation bar on mobile/tablet devices featuring Home, Shop, Quiz, Wishlist, and live-badged Cart.
4. **Structured JSON-LD Schema**: MedicalBusiness and schema metadata embedded on all pages for rich search results.
5. **Universal Keyboard Shortcuts**: Pressing `ESC` closes all open drawers, search modals, comparison views, or quiz modals.
