# BLUE ZONE™ — Catalog of Hidden Sections & Modules
> **Document Purpose:** Complete tracking and restoration guide for all temporarily hidden sections and modules across the BLUE ZONE™ application.  
> **Status:** All original code, templates, routes, and controllers are **100% preserved** and safely intact. Nothing has been permanently deleted or damaged.

---

## 1. Executive Summary

As requested, the **OUR SCIENCE** module and related sections have been hidden from public storefront view without deleting any underlying code, styles, or data schemas. This document logs every hidden section, the exact files modified, and step-by-step instructions for bringing any of them back whenever you are ready.

---

## 2. Master Table of Landing Page Sections

The BLUE ZONE™ landing page is governed by an interactive dynamic section builder. Here is the current live status of all 12 registered sections:

| # | Section Key | English Name | Arabic Name | Live Status | Visibility Control |
|---|-------------|--------------|-------------|-------------|---------------------|
| 1 | `hero_slider` | Hero Lifestyle & Product Showcase Slider | سلايدر الواجهة الرئيسي | **ACTIVE** | `landing_hero_slider_enabled` |
| 2 | `who_we_are` | Who We Are (Brand Legacy & Origin) | من نحن وتراث العلامة | **ACTIVE** | `landing_who_we_are_enabled` |
| 3 | `philosophy` | The 6 Pillars of Longevity Philosophy | ركائز طول العمر الست | **ACTIVE** | `landing_philosophy_enabled` |
| 4 | `new_arrivals` | New Arrivals & Longevity Packs | أحدث التركيبات والتشغيلات | **ACTIVE** | `landing_new_arrivals_enabled` |
| 5 | `featured_products`| Featured Formulations Spotlight | المستحضرات المميزة | **ACTIVE** | `landing_featured_products_enabled` |
| 6 | `products_vertical`| Structured Vertical Breakdown | استعراض التركيبات الرأسي | **ACTIVE** | `landing_products_vertical_enabled` |
| 7 | `blue_mind_flagship`| Blue Mind Flagship Nootropic Hero | مستحضر بلو مايند الرائد | **ACTIVE** | `landing_blue_mind_flagship_enabled` |
| 8 | `five_blue_zones` | 5 Global Blue Zones Ecosystems | الأقاليم الخمسة المعمرة | **ACTIVE** | `landing_five_blue_zones_enabled` |
| 9 | `bluemint_preps` | Bluemint Specialized Preparations | مستحضرات بلو مينت وبروتوكولاتها | **ACTIVE** | `landing_bluemint_preps_enabled` |
| 10| `our_science` | **Our Science & Clinical Dossier** | **الأبحاث والنزاهة السريرية** | 🔒 **HIDDEN** | `landing_our_science_enabled` |
| 11| `journal_news` | Longevity Journal & Insights | مجلة وأبحاث طول العمر | 📍 **RELOCATED TO ABOUT** | `landing_journal_news_enabled` |
| 12| `final_cta` | Diagnostic Assessment & Final CTA | الدعوة الختامية للاستشارة | **ACTIVE** | `landing_final_cta_enabled` |

---

## 3. Detailed Breakdown of Hidden Modules

### Module A: [OUR SCIENCE] (Clinical Pharmacology & Research Dossier)

#### Description:
A comprehensive scientific section detailing biochemical pathways, clinical pharmacology, standardized purity assays (HPLC), and individual product clinical dossier swapper/grid cards.

#### Elements Hidden:
1. **Homepage Section:**
   - **File:** `resources/views/customer/home/partials/our_science.blade.php`
   - **Method:** Dynamic master switch `landing_our_science_enabled` set to `false` in both:
     - `app/View/ViewModels/SettingViewModel.php` (code default)
     - `settings` database table (`Setting::set('landing_our_science_enabled', false)`)
   - **Impact:** Section is not rendered on the landing page.

2. **Main Navigation (Desktop Header):**
   - **File:** `resources/views/components/layouts/customer.blade.php` (around lines 160–166)
   - **Method:** Commented out using standard Blade tags `{{-- ... --}}`.
   - **Snippet:**
     ```blade
     {{-- [HIDDEN MODULE: OUR SCIENCE] - Can be re-enabled anytime by un-commenting --}}
     {{--
     <a href="{{ route('customer.pages.science') }}"
         class="nav-link text-xs uppercase tracking-[0.15em] font-bold py-1 transition-colors ...">
         {{ __('app.our_science') }}
     </a>
     --}}
     ```

3. **Mobile Drawer Navigation:**
   - **File:** `resources/views/components/layouts/customer.blade.php` (around lines 354–360)
   - **Method:** Commented out using `{{-- ... --}}`.
   - **Snippet:**
     ```blade
     {{-- [HIDDEN MODULE: OUR SCIENCE] - Can be re-enabled anytime by un-commenting --}}
     {{--
     <a href="{{ route('customer.pages.science') }}"
         class="mobile-nav-link text-xs font-extrabold uppercase tracking-widest py-2.5 ...">
         <span>OUR SCIENCE</span> <i class="fa-solid fa-arrow-right rtl:rotate-180 ..."></i>
     </a>
     --}}
     ```

4. **Footer Explore Links:**
   - **File:** `resources/views/components/layouts/customer.blade.php` (around lines 607–613)
   - **Method:** Commented out using `{{-- ... --}}`.
   - **Snippet:**
     ```blade
     {{-- [HIDDEN MODULE: OUR SCIENCE] - Can be re-enabled anytime by un-commenting --}}
     {{--
     <li><a href="{{ route('customer.pages.science') }}"
             class="text-[#031827] hover:text-[#0A4F78] dark:text-[#F6F5EF] ...">
             {{ __('app.nav.science') ?? 'Our Science' }}
         </a></li>
     --}}
     ```

5. **Legacy Layout Template:**
   - **File:** `resources/views/layouts/customer.blade.php`
   - **Method:** Both header nav link (lines 27–29) and footer link (line 126) commented out with `{{-- ... --}}`.

6. **Hero Slider Slide #1 Action Button:**
   - **File:** `resources/views/customer/home/partials/hero_slider.blade.php` (around lines 107–111)
   - **Method:** "EXPLORE OUR SCIENCE" button commented out with `{{-- ... --}}`, keeping the direct "SHOP FORMULATIONS" CTA active.

7. **Final CTA Section Secondary Button:**
   - **File:** `resources/views/customer/home/partials/final_cta.blade.php` (around lines 17–20)
   - **Method:** "OUR SCIENCE" button commented out with `{{-- ... --}}`, keeping "SHOP ALL FORMULATIONS" active.

8. **About Page CTA:**
   - **File:** `resources/views/customer/pages/about.blade.php` (around lines 121–125)
   - **Method:** "OUR SCIENCE" button commented out with `{{-- ... --}}`.

9. **404 Error Page Quick Links:**
   - **File:** `resources/views/errors/404.blade.php` (around lines 53–56)
   - **Method:** "Our Science" quick button commented out with `{{-- ... --}}`.

---

### Module B: [MEET THE TEAM] (Team Bios & Leadership)

#### Description:
A dedicated page (`/team`) and navigation links introducing the medical advisory board and executive team.

#### Elements Hidden:
- **Desktop Header Nav:** `resources/views/components/layouts/customer.blade.php` (commented out)
- **Footer Quick Links:** `resources/views/components/layouts/customer.blade.php` (commented out)
- **Alternative Layout:** `resources/views/layouts/customer.blade.php` (commented out)

---

### Module C: [SHOP CATALOG DIRECT] (`/shop` direct links)

#### Description:
Direct links to `/shop` have been hidden in favor of `/products` (`customer.products`), consolidating product discovery onto the unified formulations catalog.

#### Elements Hidden:
- **Desktop Navbar & Header Shop CTA:** `resources/views/components/layouts/customer.blade.php` (commented out)
- **Mobile Drawer:** `resources/views/components/layouts/customer.blade.php` (commented out)
- **Footer Quick Links:** `resources/views/components/layouts/customer.blade.php` (commented out)

---

### Module D: [BLUE ZONE JOURNAL] (Relocated to About Us Page)

#### Description:
The **THE BLUE ZONE JOURNAL** (Research & Insights) section previously featured on the homepage has been relocated to the dedicated **About Us** page (`/about`), positioned directly beneath the **About Blue Zone** philosophy section.

#### Actions Taken:
1. **Homepage Master Switch Disabled:**
   - `landing_journal_news_enabled` default changed to `false` in `app/View/ViewModels/SettingViewModel.php`.
   - Saved in database via `Setting::set('landing_journal_news_enabled', false, 'landing', 'boolean')`.
2. **Integrated into About Page:**
   - Template: `resources/views/customer/pages/about.blade.php`.
   - Placed directly under `<section id="philosophy">` ("About Blue Zone").
   - Enhanced with complete bilingual (Arabic & English) support, high-resolution cards, and links to the full journal at `customer.pages.blog`.
3. **Preserved Homepage Partial:**
   - `resources/views/customer/home/partials/journal_news.blade.php` remains 100% intact and can be toggled back on for the homepage anytime via Admin Settings (`/admin/settings`).

---

## 4. How to Return / Un-hide [OUR SCIENCE] Anytime

When you are ready to bring the **OUR SCIENCE** module back, follow these simple steps:

### Option 1: One-Click via Admin Panel UI (For Homepage Section)
1. Go to the Admin Dashboard: `http://localhost/admin/settings` (or your domain).
2. Open the **Landing Page Builder** tab.
3. Locate row **Our Science & Clinical Dossier** (`our_science`).
4. Toggle the switch to **ON / Enabled**.
5. Click **Save Settings**.

---

### Option 2: Via Artisan Command (Instant Terminal Re-enable)
Run the following command in PowerShell / Bash from the project root:

```bash
php artisan tinker --execute="App\Models\Setting::set('landing_our_science_enabled', true, 'landing', 'boolean'); Illuminate\Support\Facades\Cache::flush(); echo 'OUR SCIENCE homepage section successfully re-enabled!';"
```

---

### Option 3: Un-commenting Navigation Links in Blade
Simply remove the `{{--` and `--}}` comment wrappers in the following files:

1. **Header Navigation (Desktop & Mobile):**
   - File: `resources/views/components/layouts/customer.blade.php`
   - Remove `{{--` and `--}}` around lines 160–166 and 354–360.

2. **Footer Navigation:**
   - File: `resources/views/components/layouts/customer.blade.php`
   - Remove `{{--` and `--}}` around lines 607–613.

3. **CTA Buttons (Hero Slider, Final CTA, About, 404):**
   - Remove `{{--` and `--}}` in:
     - `resources/views/customer/home/partials/hero_slider.blade.php`
     - `resources/views/customer/home/partials/final_cta.blade.php`
     - `resources/views/customer/pages/about.blade.php`
     - `resources/views/errors/404.blade.php`

---

## 5. Summary of Preserved Assets (Untouched Files)

The following core files remain completely intact and ready to serve at any time:
- Controller: `app/Http/Controllers/Customer/ScienceController.php`
- Controller: `app/Http/Controllers/Customer/PageController.php` (`science` method)
- Route: `routes/customer.php` (`/science` and `/our-science/{slug}`)
- Views:
  - `resources/views/customer/pages/science.blade.php`
  - `resources/views/customer/pages/science-details.blade.php`
  - `resources/views/customer/home/partials/our_science.blade.php`
  - `resources/views/customer/pages/partials/medical-product-card.blade.php`
- Admin Product Science Fields:
  - `resources/views/admin/products/create.blade.php` (Clinical dossier input fields)
  - `resources/views/admin/products/edit.blade.php` (Clinical dossier input fields)

---

## 6. Landing Page Products Showcase & Configuration Control

### Overview
The **Featured Products Section** on the Landing Page (`featured_products.blade.php`) is now fully configurable both **globally** and on an **individual product level** directly from the Admin Products module.

### 1. Global Section Configuration (Admin Products Index)
- **Location:** Admin Products Index (`/admin/products`) — Header Banner Card.
- **Controls Available:**
  - **Master Section Toggle:** Open/Hide the section entirely (`landing_featured_products_enabled`).
  - **Showcase Limit:** Display all products (`0` - no limit) or limit to 3, 6, 9, or 12 items.
  - **Bilingual Section Eyebrow/Badge:** `landing_products_badge_en` & `landing_products_badge_ar`.
  - **Bilingual Section Title:** `landing_products_title_en` & `landing_products_title_ar`.
  - **Bilingual Section Subtitle:** `landing_products_subtitle_en` & `landing_products_subtitle_ar`.
  - **Bilingual Catalog Button CTA:** `landing_products_cta_text_en` & `landing_products_cta_text_ar`.

### 2. Per-Product Configuration
- **Table Row Quick-Toggle:** In `/admin/products`, click the **"Showcased / Hidden"** pill button in the `Landing Page` column to instantly toggle visibility with zero page reload.
- **Product Create / Edit Form:**
  - Dedicated **"Landing Page Display & Showcase Configuration"** card.
  - Switch: **Show on Landing Page** (`show_on_landing`).
  - Number: **Landing Priority Order** (`landing_order`).
  - Text: **Custom Landing Badge** (EN / AR) e.g., *"Longevity Flagship"*, *"Best Seller"*, *"مستحضر معتمد"*.
  - Direct Link: Quick jump to Hero Carousel & Slide Manager.

---

## 7. Hero Slider & Product Carousel Command Center

### Overview
The top **Cinematic Hero Slider** (`hero_slider.blade.php`), including all background images, titles, highlighted keywords, dual CTA buttons, and the **Floating Glassmorphic Cards** (such as *"CENTENARIAN HOTSPOT / OKINAWA, JAPAN"* and *"FLAGSHIP NOOTROPIC / BLUE MIND"*), is now 100% dynamic and manageable from Admin.

### 1. Management Hub
- **Route:** `/admin/hero-slider` (`admin.hero-slides.index`)
- **Navigation:** Accessible directly from:
  1. Admin Sidebar under **Catalog & Formulations** ➔ **Hero Slider & Carousel**.
  2. Products Catalog Header ➔ **Hero Carousel Manager** button.
  3. Product Create / Edit page ➔ Inside the **Landing Settings** card.

### 2. Key Slide Customization Controls
- **Product Association:** Link any slide to an existing product in the catalog (`product_id`) with 1-click auto-fill, or configure as a custom regional hotspot/feature.
- **Floating Glassmorphic Card:**
  - Master Show/Hide toggle per slide (`show_floating_card`).
  - Card Eyebrow/Badge (EN & AR) e.g. `CENTENARIAN HOTSPOT`.
  - Card Title (EN & AR) e.g. `OKINAWA, JAPAN` or `BLUE MIND`.
  - Card Subtitle / Meta / Reviews (EN & AR) e.g. `Moai Social & Plant Diet` or `★ 4.9 Verified Reviews`.
  - Card Thumbnail Image (Upload or path/URL).
  - Card Target Click Action/URL.
- **Bilingual Copy & Design:**
  - Top Eyebrow Badge (EN & AR) + Accent Color Palette (Emerald, Cyan, Amber, Rose, Purple).
  - Main Headline (EN & AR).
  - Word/Phrase Highlight (EN & AR) with custom hex color picker.
  - Subtitle / Description (EN & AR).
  - Primary CTA Button: Text (EN/AR), Link URL, and Button Style (Emerald, Cyan, White).
  - Secondary CTA Button: Text (EN/AR), Link URL, and Button Style.
- **Background Media:**
  - Presets (Slides 1 through 5).
  - Custom HD image upload (`/uploads/hero/`).
  - Direct Image URL.
- **Global Carousel Settings:**
  - Master Section Toggle (`landing_hero_slider_enabled`).
  - Autoplay Toggle & Delay Speed (3.5s, 5.5s, 7.5s, 10s).
  - 1-Click **"Reset to Brand Curated Defaults"** restoring the 5 original high-fidelity slides.


