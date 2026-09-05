# BLUE ZONE — Cinematic World Map Loader System Documentation

## 1. Overview & Architectural Goals

The **Cinematic World Map Loader** is an immersive, high-performance visual introduction designed for the **BLUE ZONE** longevity and wellness platform. It replaces generic loading spinners with a cinematic geographic narrative, mapping out the five global Blue Zones (**Okinawa**, **Sardinia**, **Nicoya**, **Ikaria**, and **Loma Linda**) while assets load and the application initializes.

### Key Highlights
- **High-Precision Vector Cartography**: Accurate multi-polygon world map rendered directly via scalable vector graphics (SVG) with oceanic grid backdrops.
- **Modular Codebase**: Strict separation between presentation (`css/map-loader.css`), animation logic (`js/map.js`), and semantic document markup (`index.html`).
- **Deterministic 6.8s Timeline**: Predictable stage-based sequence with early-exit ("Skip Intro") and on-demand replay (`window.BLUEZONE_MAP.replay()`).
- **Responsive Viewport Clamping**: Perfectly scaled across ultra-wide desktop monitors, standard laptops, tablets, and mobile phones.
- **Accessibility & Motion Safety**: Native support for `prefers-reduced-motion` and assistive ARIA roles.

---

## 2. File Organization

```
Blue_Zone_site/
├── css/
│   ├── map-loader.css         # Dedicated stylesheet for loader layout, tokens & keyframes
│   └── custom.css             # Main site animations & custom styles
├── js/
│   ├── map.js                 # Loader controller, timeline sequencing, API & state
│   └── app.js                 # Global application scripts
├── components/
│   └── footer.html            # Includes "Replay World Map Intro" action trigger
├── index.html                 # Main landing page mounting #blue-zone-loader
└── MAP_LOADER_SYSTEM.md       # Full architectural & logic documentation (this file)
```

---

## 3. Design System & Style Tokens (`css/map-loader.css`)

All colors, dimensions, and atmospheric glows are defined through CSS custom properties on `:root` to ensure easy theming and visual cohesion:

| Token | Value | Purpose |
| :--- | :--- | :--- |
| `--bg` | `#071018` | Deep abyss background radial perimeter |
| `--bg-2` | `#0a1a26` | Concentrated radial background core |
| `--line` | `#2c5678` | Structural frames, borders, and pin label strokes |
| `--accent` | `#4fb0e6` | Primary electric cyan neon accent & glowing trails |
| `--accent-dim` | `#7fc4e8` | Subdued secondary cyan for labels and metadata |
| `--text` | `#eaf4fb` | Primary readable high-contrast text |
| `--text-dim` | `#6e93ab` | Muted subtitle text and separator dots |
| `--land` | `#1c4363` | Vector continent base gradient fill (bottom) |
| `--land-hi` | `#2f6690` | Vector continent highlight gradient fill (top) |

### Key Animations & Visual Effects

1. **Rotating Micro-Spinner (`bzSpin`)**:
   - `13px × 13px` dual-tone glowing ring with continuous 360° rotation (`0.75s linear infinite`).
   - Positioned alongside the dynamic step heading inside `.status-wrap`.
2. **Pulsing Pin Rings (`bzRingPulse`)**:
   - Expanding radial echo on each active Blue Zone coordinate (`0% r:8, opacity:0.9` to `100% r:22, opacity:0`).
   - Staggered animation delays (0.0s to 1.2s) across the 5 pins.
3. **Pulsing Radar Dot (`bzDotPulse`)**:
   - Brand indicator in the top bar with subtle glowing breath cycle (`1.8s ease-in-out infinite`).
4. **Connecting Constellation Lines (`#map-lines-group`)**:
   - SVG `<path>` elements with dashed stroke (`stroke-dasharray: 6 6`) and neon drop-shadow filter connecting all 5 zones in sequence.

---

## 4. Geographic Coordinate Matrix

All coordinates map accurately to the SVG `viewBox="0 0 1200 560"`:

| Blue Zone | Country | SVG Translation `(X, Y)` | Label Placement |
| :--- | :--- | :--- | :--- |
| **Okinawa** | Japan | `(948.9, 189.2)` | Right aligned (`x: 14, y: -14`) |
| **Sardinia** | Italy | `(623.7, 144.6)` | Top-center aligned (`x: -59, y: -50`) |
| **Nicoya** | Costa Rica | `(360.2, 243.0)` | Bottom-center aligned (`x: -59, y: 16`) |
| **Ikaria** | Greece | `(668.9, 152.8)` | Bottom-right aligned (`x: 14, y: 16`) |
| **Loma Linda** | USA | `(287.4, 164.6)` | Left aligned (`x: -132, y: -14`) |

---

## 5. Timeline Sequence Logic (`js/map.js`)

```
0.0s ───────────────────────────────► 6.0s ───► 6.8s
 [Intro]   [Pins 1-5 Staggered]       [Lines]  [Fade Out]
 15%       30% -> 45% -> 60% -> 75%   95%-100%  Hidden
```

The timeline is managed via asynchronous timers stored in an `activeTimers` registry to enable instant cancellation on skip or replay:

| Timestamp | Step Heading | Progress | Visual Actions |
| :--- | :--- | :--- | :--- |
| **0.0s** | `DISCOVERING THE WORLD` | `15%` | Loader overlay displayed at 100% opacity; continent map fades in. |
| **0.8s – 1.0s** | `LOCATING THE BLUE ZONES` | `30%` | **Okinawa** pin marker reveals at 0.8s; label reveals at 1.0s; "Okinawa" footer text activates. |
| **1.5s – 1.7s** | `LOCATING THE BLUE ZONES` | `45%` | **Sardinia** pin marker reveals at 1.5s; label reveals at 1.7s; "Sardinia" footer text activates. |
| **2.2s – 2.4s** | `LOCATING THE BLUE ZONES` | `60%` | **Nicoya** pin marker reveals at 2.2s; label reveals at 2.4s; "Nicoya" footer text activates. |
| **2.9s – 3.1s** | `LOCATING THE BLUE ZONES` | `75%` | **Ikaria** pin marker reveals at 2.9s; label reveals at 3.1s; "Ikaria" footer text activates. |
| **3.6s – 3.8s** | `LOCATING THE BLUE ZONES` | `85%` | **Loma Linda** pin marker reveals at 3.6s; label reveals at 3.8s; "Loma Linda" footer text activates. |
| **4.3s** | `CONNECTING THE STORIES` | `95%` | `#map-lines-group` reveals with dashed lines; footer zones enter high-speed connection cycle. |
| **5.1s** | `ENTERING BLUE ZONE` | `100%` | Progress reaches 100%; all 5 zone labels remain lit simultaneously. |
| **6.0s** | — | `100%` | `finishIntro()` triggers 800ms ease fade-out (`opacity: 0`). |
| **6.8s** | — | — | Loader element set to `display: none`. Interaction handed off to main page. |
| **10.0s** | — | — | Hard failsafe timeout ensuring loader never locks the viewport under any unexpected error. |

---

## 6. Public JavaScript API & State Control

The map controller exposes a clean global namespace on `window.BLUEZONE_MAP`:

```javascript
// Initialize or run the loader
window.BLUEZONE_MAP.init(forceReplay = true);

// Replay the full cinematic intro at any point (e.g., from footer button)
window.BLUEZONE_MAP.replay();
```

### Memory Safety & Lifecycle Handling
- **Timer Clearing**: Every invocation of `initMapIntro()` immediately executes `clearTimeout` on all pending timers and resets `clearInterval` on the zone cycling loop.
- **State Reset**: Pin opacity, line opacity, progress bar width, text content, and active zone CSS classes are systematically reset before the sequence restarts.
- **LocalStorage**: Sets `localStorage.setItem('bluezone_intro_completed', 'true')` upon completion or skip.

---

## 7. Responsive & Motion Specifications

- **Desktop & Large Displays**: Constrained to `max-width: 1320px` and `max-height: 95vh` to prevent vertical scrolling.
- **Mobile Devices (`≤ 768px`)**:
  - Loader padding drops to `12px`.
  - Topbar and footer margins scaled via CSS `clamp()` functions.
  - Micro-spinner downscales to `11px` with `1.5px` border.
  - Typography automatically rescales from `13px` to `11px`.
- **Accessibility (`prefers-reduced-motion: reduce`)**:
  - Automatically suppresses all infinite keyframes (`bzRingPulse`, `bzDotPulse`, `bzSpin`, `bar-fill`).
  - Guarantees immediate, comfortable legibility for users with vestibular sensitivities.
