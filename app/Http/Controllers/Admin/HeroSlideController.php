<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HeroSlideController extends Controller
{
    /**
     * Display a listing of hero carousel slides with visual editor and global controls.
     */
    public function index()
    {
        $slides = HeroSlide::with('product')->ordered()->get();
        $products = Product::where('status', 'active')
            ->orWhere('is_active', true)
            ->select('id', 'name_en', 'name_ar', 'slug', 'image', 'rating', 'price')
            ->orderBy('name_en')
            ->get();

        $heroSettings = [
            'landing_hero_slider_enabled' => (bool) Setting::get('landing_hero_slider_enabled', true),
            'landing_hero_slider_autoplay' => (bool) Setting::get('landing_hero_slider_autoplay', true),
            'landing_hero_slider_delay' => (int) Setting::get('landing_hero_slider_delay', 5500),
        ];

        $totalSlides = $slides->count();
        $activeSlides = $slides->where('is_active', true)->count();
        $connectedProductsCount = $slides->whereNotNull('product_id')->unique('product_id')->count();

        return view('admin.hero_slides.index', compact(
            'slides',
            'products',
            'heroSettings',
            'totalSlides',
            'activeSlides',
            'connectedProductsCount'
        ));
    }

    /**
     * Store a newly created slide in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'title_highlight_en' => 'nullable|string|max:100',
            'title_highlight_ar' => 'nullable|string|max:100',
            'title_highlight_color' => 'nullable|string|max:20',
            'badge_en' => 'nullable|string|max:100',
            'badge_ar' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:20',
            'subtitle_en' => 'nullable|string|max:1000',
            'subtitle_ar' => 'nullable|string|max:1000',
            'product_id' => 'nullable|exists:products,id',
            'primary_cta_text_en' => 'nullable|string|max:100',
            'primary_cta_text_ar' => 'nullable|string|max:100',
            'primary_cta_link' => 'nullable|string|max:255',
            'primary_cta_style' => 'nullable|string|max:30',
            'secondary_cta_text_en' => 'nullable|string|max:100',
            'secondary_cta_text_ar' => 'nullable|string|max:100',
            'secondary_cta_link' => 'nullable|string|max:255',
            'secondary_cta_style' => 'nullable|string|max:30',
            'bg_image_preset' => 'nullable|string',
            'bg_image_file' => 'nullable|image|max:8192',
            'bg_image_url' => 'nullable|string|max:500',
            'show_floating_card' => 'nullable|boolean',
            'card_badge_en' => 'nullable|string|max:100',
            'card_badge_ar' => 'nullable|string|max:100',
            'card_title_en' => 'nullable|string|max:255',
            'card_title_ar' => 'nullable|string|max:255',
            'card_subtitle_en' => 'nullable|string|max:255',
            'card_subtitle_ar' => 'nullable|string|max:255',
            'card_image_file' => 'nullable|image|max:4096',
            'card_image_url' => 'nullable|string|max:500',
            'card_link' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $slide = new HeroSlide();
        $this->fillSlideAttributes($slide, $request, $validated);

        // Position at end of list
        $maxOrder = HeroSlide::max('slide_order') ?? 0;
        $slide->slide_order = $maxOrder + 1;
        $slide->save();

        $this->flushHeroCaches();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Carousel slide created successfully!',
                'slide' => $slide->load('product'),
            ]);
        }

        return redirect()->route('admin.hero-slides.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء شريحة العرض بنجاح!' : 'Hero carousel slide created successfully!');
    }

    /**
     * Return JSON data for single slide (useful for quick edit modal).
     */
    public function show($id)
    {
        $slide = HeroSlide::with('product')->findOrFail($id);
        $slideData = $slide->toArray();
        $slideData['display_card_image_url'] = $slide->display_card_image_url;
        $slideData['display_bg_image_url'] = $slide->display_bg_image_url;

        return response()->json([
            'success' => true,
            'slide' => $slideData,
        ]);
    }

    /**
     * Update the specified slide in storage.
     */
    public function update(Request $request, $id)
    {
        $slide = HeroSlide::findOrFail($id);

        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'title_highlight_en' => 'nullable|string|max:100',
            'title_highlight_ar' => 'nullable|string|max:100',
            'title_highlight_color' => 'nullable|string|max:20',
            'badge_en' => 'nullable|string|max:100',
            'badge_ar' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:20',
            'subtitle_en' => 'nullable|string|max:1000',
            'subtitle_ar' => 'nullable|string|max:1000',
            'product_id' => 'nullable|exists:products,id',
            'primary_cta_text_en' => 'nullable|string|max:100',
            'primary_cta_text_ar' => 'nullable|string|max:100',
            'primary_cta_link' => 'nullable|string|max:255',
            'primary_cta_style' => 'nullable|string|max:30',
            'secondary_cta_text_en' => 'nullable|string|max:100',
            'secondary_cta_text_ar' => 'nullable|string|max:100',
            'secondary_cta_link' => 'nullable|string|max:255',
            'secondary_cta_style' => 'nullable|string|max:30',
            'bg_image_preset' => 'nullable|string',
            'bg_image_file' => 'nullable|image|max:8192',
            'bg_image_url' => 'nullable|string|max:500',
            'show_floating_card' => 'nullable|boolean',
            'card_badge_en' => 'nullable|string|max:100',
            'card_badge_ar' => 'nullable|string|max:100',
            'card_title_en' => 'nullable|string|max:255',
            'card_title_ar' => 'nullable|string|max:255',
            'card_subtitle_en' => 'nullable|string|max:255',
            'card_subtitle_ar' => 'nullable|string|max:255',
            'card_image_file' => 'nullable|image|max:4096',
            'card_image_url' => 'nullable|string|max:500',
            'card_link' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'slide_order' => 'nullable|integer',
        ]);

        $this->fillSlideAttributes($slide, $request, $validated);

        if (isset($validated['slide_order'])) {
            $slide->slide_order = (int) $validated['slide_order'];
        }

        $slide->save();
        $this->flushHeroCaches();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Carousel slide updated successfully!',
                'slide' => $slide->load('product'),
            ]);
        }

        return redirect()->route('admin.hero-slides.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث شريحة العرض بنجاح!' : 'Hero carousel slide updated successfully!');
    }

    /**
     * Remove the specified slide from storage.
     */
    public function destroy($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $title = $slide->display_title;
        $slide->delete();

        $this->flushHeroCaches();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Slide [{$title}] deleted successfully",
            ]);
        }

        return redirect()->route('admin.hero-slides.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف الشريحة بنجاح' : "Slide [{$title}] deleted successfully");
    }

    /**
     * Toggle active status of a slide via AJAX.
     */
    public function toggleStatus(Request $request, $id)
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->is_active = !$slide->is_active;
        $slide->save();

        $this->flushHeroCaches();

        $msg = $slide->is_active
            ? (app()->getLocale() === 'ar' ? 'تم تفعيل الشريحة في السلايدر' : "Slide [{$slide->display_title}] is now active!")
            : (app()->getLocale() === 'ar' ? 'تم إخفاء الشريحة من السلايدر' : "Slide [{$slide->display_title}] is now hidden!");

        return response()->json([
            'success' => true,
            'is_active' => $slide->is_active,
            'message' => $msg,
        ]);
    }

    /**
     * Reorder slides via AJAX drag/buttons.
     */
    public function reorder(Request $request)
    {
        $order = $request->input('order', []);
        if (is_array($order)) {
            foreach ($order as $index => $id) {
                HeroSlide::where('id', $id)->update(['slide_order' => $index + 1]);
            }
        }

        $this->flushHeroCaches();

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar' ? 'تم تحديث ترتيب الشرائح بنجاح' : 'Slides reordered successfully!',
        ]);
    }

    /**
     * Update global carousel settings (show/hide section, autoplay, delay).
     */
    public function updateGlobalSettings(Request $request)
    {
        $enabled = $request->has('landing_hero_slider_enabled');
        $autoplay = $request->has('landing_hero_slider_autoplay');
        $delay = (int) $request->input('landing_hero_slider_delay', 5500);
        if ($delay < 2000) $delay = 2000;
        if ($delay > 20000) $delay = 20000;

        Setting::set('landing_hero_slider_enabled', $enabled, 'landing', 'boolean');
        Setting::set('landing_hero_slider_autoplay', $autoplay, 'landing', 'boolean');
        Setting::set('landing_hero_slider_delay', $delay, 'landing', 'integer');

        $this->flushHeroCaches();

        return redirect()->route('admin.hero-slides.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم حفظ إعدادات السلايدر العامة بنجاح!' : 'Global hero carousel settings saved successfully!');
    }

    /**
     * Reset to the 5 standard curated brand slides with one click.
     */
    public function resetDefaults()
    {
        DB::table('hero_slides')->truncate();

        $blueMindId = DB::table('products')->where('slug', 'blue-mind')->value('id');
        $blueJointId = DB::table('products')->where('slug', 'blue-joint')->value('id');
        $bluePeaId = DB::table('products')->where('slug', 'blue-pea-b-complex')->value('id');
        $blueShieldId = DB::table('products')->where('slug', 'blue-shield')->value('id');

        $slides = [
            [
                'product_id' => $blueMindId,
                'slide_order' => 1,
                'is_active' => true,
                'badge_en' => 'CENTENARIAN WISDOM',
                'badge_ar' => 'حكمة المعمرين عبر العصور',
                'badge_color' => 'emerald',
                'title_en' => 'LIVE LONG. LIVE WELL.',
                'title_ar' => 'عِش طويلاً. عِش بصحة وعافية.',
                'title_highlight_en' => 'LIVE WELL.',
                'title_highlight_ar' => 'بصحة وعافية.',
                'title_highlight_color' => '#67B34A',
                'subtitle_en' => 'Precision cellular nutraceuticals engineered from the world\'s most resilient longevity zones.',
                'subtitle_ar' => 'مغذيات خلوية علاجية دقيقة مستخلصة من أقاليم المناطق الزرقاء الأكثر عمراً ومرونة بيولوجية.',
                'primary_cta_text_en' => 'DISCOVER OUR STORY',
                'primary_cta_text_ar' => 'اكتشف قصتنا وتراثنا',
                'primary_cta_link' => '#who-we-are',
                'primary_cta_style' => 'emerald',
                'secondary_cta_text_en' => 'EXPLORE PRODUCTS',
                'secondary_cta_text_ar' => 'استكشف كافة التركيبات',
                'secondary_cta_link' => '/products',
                'secondary_cta_style' => 'glass',
                'bg_image' => '/assets/images/hero/hero-01.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'FLAGSHIP NOOTROPIC',
                'card_badge_ar' => 'المنشط الذهني الرائد',
                'card_title_en' => 'BLUE MIND',
                'card_title_ar' => 'بلو مايند',
                'card_subtitle_en' => '★ 4.9 Verified Reviews',
                'card_subtitle_ar' => '★ 4.9 تقييم سريري معتمد',
                'card_image' => '/assets/products/blue-mind.webp',
                'card_link' => '/product/blue-mind',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $blueShieldId,
                'slide_order' => 2,
                'is_active' => true,
                'badge_en' => 'BIO-ENGINEERED NUTRITION',
                'badge_ar' => 'تغذية حيوية متطورة',
                'badge_color' => 'cyan',
                'title_en' => 'CLINICAL BOTANICAL INTEGRITY.',
                'title_ar' => 'نزاهة نباتية سريرية قياسية.',
                'title_highlight_en' => 'INTEGRITY.',
                'title_highlight_ar' => 'قياسية.',
                'title_highlight_color' => '#2A8FC2',
                'subtitle_en' => 'Bio-identical dietary compounds standardized for optimal cellular absorption and daily mitochondrial vitality.',
                'subtitle_ar' => 'مركبات غذائية متطابقة حيوياً موحدة لأعلى امتصاص خلوي وطاقة ميتوكوندريا يومية.',
                'primary_cta_text_en' => 'SHOP FORMULATIONS',
                'primary_cta_text_ar' => 'تسوق التركيبات الحيوية',
                'primary_cta_link' => '/products',
                'primary_cta_style' => 'cyan',
                'secondary_cta_text_en' => 'VIEW CATALOG',
                'secondary_cta_text_ar' => 'تصفح الكتالوج',
                'secondary_cta_link' => '/products',
                'secondary_cta_style' => 'glass',
                'bg_image' => '/assets/images/hero/hero-02.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'MITOCHONDRIAL ATP',
                'card_badge_ar' => 'طاقة الخلية والمناعة',
                'card_title_en' => 'BLUE SHIELD',
                'card_title_ar' => 'بلو شيلد',
                'card_subtitle_en' => '★ 4.9 Verified Reviews',
                'card_subtitle_ar' => '★ 4.9 تقييم موثق',
                'card_image' => '/assets/products/blue-cell.webp',
                'card_link' => '/product/blue-shield',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $blueJointId,
                'slide_order' => 3,
                'is_active' => true,
                'badge_en' => 'WELLNESS PHILOSOPHY',
                'badge_ar' => 'فلسفة العافية والحركة',
                'badge_color' => 'emerald',
                'title_en' => 'DAILY VITALITY & FLUID MOVEMENT.',
                'title_ar' => 'حيوية يومية وحركة انسيابية.',
                'title_highlight_en' => 'FLUID MOVEMENT.',
                'title_highlight_ar' => 'وحركة انسيابية.',
                'title_highlight_color' => '#67B34A',
                'subtitle_en' => 'Integrating natural movement, plant-rich nutrition, and cognitive purpose into daily routines.',
                'subtitle_ar' => 'دمج الحركة الطبيعية، والتغذية الغنية بالنباتات، والهدف الذهني في الروتين اليومي.',
                'primary_cta_text_en' => 'THE 6 PILLARS',
                'primary_cta_text_ar' => 'ركائز طول العمر الست',
                'primary_cta_link' => '#philosophy',
                'primary_cta_style' => 'emerald',
                'secondary_cta_text_en' => 'VIEW CATALOG',
                'secondary_cta_text_ar' => 'عرض الكتالوج',
                'secondary_cta_link' => '/products',
                'secondary_cta_style' => 'glass',
                'bg_image' => '/assets/images/hero/hero-03.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'JOINT MOBILITY',
                'card_badge_ar' => 'مرونة وصحة المفاصل',
                'card_title_en' => 'BLUE JOINT',
                'card_title_ar' => 'بلو جوينت',
                'card_subtitle_en' => '★ 4.8 Verified Reviews',
                'card_subtitle_ar' => '★ 4.8 تقييم سريري',
                'card_image' => '/assets/products/blue-flex.webp',
                'card_link' => '/product/blue-joint',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => null,
                'slide_order' => 4,
                'is_active' => true,
                'badge_en' => 'GLOBAL BLUE ZONES',
                'badge_ar' => 'المناطق الزرقاء العالمية',
                'badge_color' => 'cyan',
                'title_en' => 'INSPIRED BY THE 5 BLUE ZONES.',
                'title_ar' => 'مستوحاة من أقاليم المناطق الزرقاء الخمس.',
                'title_highlight_en' => '5 BLUE ZONES.',
                'title_highlight_ar' => 'المناطق الزرقاء الخمس.',
                'title_highlight_color' => '#2A8FC2',
                'subtitle_en' => 'Unlocking the longevity principles observed across Okinawa, Sardinia, Nicoya, Ikaria, and Loma Linda.',
                'subtitle_ar' => 'استكشاف أسرار طول العمر المعمرة في أوكيناوا، سردينيا، نيكويا، إيكاريا، ولوما ليندا.',
                'primary_cta_text_en' => 'EXPLORE REGIONS',
                'primary_cta_text_ar' => 'استكشف الأقاليم',
                'primary_cta_link' => '#five-blue-zones',
                'primary_cta_style' => 'cyan',
                'secondary_cta_text_en' => 'SHOP CATALOG',
                'secondary_cta_text_ar' => 'تسوق الكتالوج',
                'secondary_cta_link' => '/products',
                'secondary_cta_style' => 'glass',
                'bg_image' => '/assets/images/hero/hero-04.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'CENTENARIAN HOTSPOT',
                'card_badge_ar' => 'بؤرة المعمرين الفائقة',
                'card_title_en' => 'OKINAWA, JAPAN',
                'card_title_ar' => 'أوكيناوا، اليابان',
                'card_subtitle_en' => 'Moai Social & Plant Diet',
                'card_subtitle_ar' => 'روابط مواي الاجتماعية والغذاء النباتي',
                'card_image' => '/assets/images/okinawa.webp',
                'card_link' => '#five-blue-zones',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $bluePeaId,
                'slide_order' => 5,
                'is_active' => true,
                'badge_en' => 'CIRCADIAN ALIGNMENT',
                'badge_ar' => 'التناغم اليومي والتعافي الخلوي',
                'badge_color' => 'emerald',
                'title_en' => 'RESTORATIVE SLEEP & RECOVERY.',
                'title_ar' => 'نوم تجديدي عميق وتعافٍ خلوي.',
                'title_highlight_en' => 'RECOVERY.',
                'title_highlight_ar' => 'وتعافٍ خلوي.',
                'title_highlight_color' => '#67B34A',
                'subtitle_en' => 'Natural nighttime neurological relaxation designed to promote deep slow-wave REM sleep cycles.',
                'subtitle_ar' => 'استرخاء عصبي ليلي طبيعي مصمم لتحفيز مراحل النوم العميق وموجات النوم البطيء.',
                'primary_cta_text_en' => 'SHOP PEA COMPLEX',
                'primary_cta_text_ar' => 'تسوق مركب بي إي إيه',
                'primary_cta_link' => '/product/blue-pea-b-complex',
                'primary_cta_style' => 'emerald',
                'secondary_cta_text_en' => 'BROWSE ALL',
                'secondary_cta_text_ar' => 'تصفح الكل',
                'secondary_cta_link' => '/products',
                'secondary_cta_style' => 'glass',
                'bg_image' => '/assets/images/hero/hero-05.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'DEEP SLEEP & NERVE MATRIX',
                'card_badge_ar' => 'مصفوفة الأعصاب والنوم المريح',
                'card_title_en' => 'BLUE PEA B COMPLEX',
                'card_title_ar' => 'بلو بي إي إيه ب كومبلكس',
                'card_subtitle_en' => '★ 4.9 Verified Reviews',
                'card_subtitle_ar' => '★ 4.9 تقييم موثق',
                'card_image' => '/assets/products/blue-rest.webp',
                'card_link' => '/product/blue-pea-b-complex',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('hero_slides')->insert($slides);

        $this->flushHeroCaches();

        return redirect()->route('admin.hero-slides.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تمت استعادة الشرائح الافتراضية المنسقة بنجاح!' : 'Curated brand hero slides successfully restored!');
    }

    /**
     * Helper to fill slide attributes and handle image uploads.
     */
    private function fillSlideAttributes(HeroSlide $slide, Request $request, array $validated): void
    {
        $slide->title_en = $validated['title_en'];
        $slide->title_ar = $validated['title_ar'];
        $slide->title_highlight_en = $validated['title_highlight_en'] ?? null;
        $slide->title_highlight_ar = $validated['title_highlight_ar'] ?? null;
        $slide->title_highlight_color = $validated['title_highlight_color'] ?? '#67B34A';
        $slide->badge_en = $validated['badge_en'] ?? null;
        $slide->badge_ar = $validated['badge_ar'] ?? null;
        $slide->badge_color = $validated['badge_color'] ?? 'emerald';
        $slide->subtitle_en = $validated['subtitle_en'] ?? null;
        $slide->subtitle_ar = $validated['subtitle_ar'] ?? null;
        $slide->product_id = !empty($validated['product_id']) ? (int) $validated['product_id'] : null;

        $slide->primary_cta_text_en = $validated['primary_cta_text_en'] ?? null;
        $slide->primary_cta_text_ar = $validated['primary_cta_text_ar'] ?? null;
        $slide->primary_cta_link = $validated['primary_cta_link'] ?? null;
        $slide->primary_cta_style = $validated['primary_cta_style'] ?? 'emerald';

        $slide->secondary_cta_text_en = $validated['secondary_cta_text_en'] ?? null;
        $slide->secondary_cta_text_ar = $validated['secondary_cta_text_ar'] ?? null;
        $slide->secondary_cta_link = $validated['secondary_cta_link'] ?? null;
        $slide->secondary_cta_style = $validated['secondary_cta_style'] ?? 'glass';

        $slide->show_floating_card = $request->boolean('show_floating_card', true);
        $slide->card_badge_en = $validated['card_badge_en'] ?? null;
        $slide->card_badge_ar = $validated['card_badge_ar'] ?? null;
        $slide->card_title_en = $validated['card_title_en'] ?? null;
        $slide->card_title_ar = $validated['card_title_ar'] ?? null;
        $slide->card_subtitle_en = $validated['card_subtitle_en'] ?? null;
        $slide->card_subtitle_ar = $validated['card_subtitle_ar'] ?? null;
        $slide->card_link = $validated['card_link'] ?? null;
        $slide->is_active = $request->boolean('is_active', true);

        // Handle Background Image
        if ($request->hasFile('bg_image_file')) {
            $file = $request->file('bg_image_file');
            $filename = 'hero_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/hero');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $slide->bg_image = '/uploads/hero/' . $filename;
        } elseif (!empty($validated['bg_image_preset'])) {
            $slide->bg_image = Product::normalizeUrl($validated['bg_image_preset']);
        } elseif (!empty($validated['bg_image_url'])) {
            $slide->bg_image = Product::normalizeUrl($validated['bg_image_url']);
        }

        // Handle Floating Card Image
        if ($request->hasFile('card_image_file')) {
            $file = $request->file('card_image_file');
            $filename = 'card_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/hero');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $slide->card_image = '/uploads/hero/' . $filename;
        } elseif (!empty($validated['card_image_url'])) {
            $slide->card_image = Product::normalizeUrl($validated['card_image_url']);
        }
    }

    /**
     * Flush relevant caches.
     */
    private function flushHeroCaches(): void
    {
        Setting::flushRuntimeCache();
        Cache::forget('hero_slides_active');
        Cache::forget('all_settings_array');
    }
}
