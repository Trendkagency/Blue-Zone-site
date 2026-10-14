<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->integer('slide_order')->default(0);
            $table->boolean('is_active')->default(true);

            // Badges & Taglines
            $table->string('badge_en')->nullable();
            $table->string('badge_ar')->nullable();
            $table->string('badge_color')->default('emerald'); // emerald, cyan, amber, rose, purple

            // Headlines & Subtitles
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('title_highlight_en')->nullable();
            $table->string('title_highlight_ar')->nullable();
            $table->string('title_highlight_color')->default('#67B34A');
            $table->text('subtitle_en')->nullable();
            $table->text('subtitle_ar')->nullable();

            // Dual CTA Buttons
            $table->string('primary_cta_text_en')->nullable();
            $table->string('primary_cta_text_ar')->nullable();
            $table->string('primary_cta_link')->nullable();
            $table->string('primary_cta_style')->default('emerald'); // emerald, cyan, white

            $table->string('secondary_cta_text_en')->nullable();
            $table->string('secondary_cta_text_ar')->nullable();
            $table->string('secondary_cta_link')->nullable();
            $table->string('secondary_cta_style')->default('glass'); // glass, outline

            // Media
            $table->string('bg_image')->default('assets/images/hero/hero-01.webp');

            // Floating Glassmorphic Card (The element in user screenshots)
            $table->boolean('show_floating_card')->default(true);
            $table->string('card_badge_en')->nullable();
            $table->string('card_badge_ar')->nullable();
            $table->string('card_title_en')->nullable();
            $table->string('card_title_ar')->nullable();
            $table->string('card_subtitle_en')->nullable();
            $table->string('card_subtitle_ar')->nullable();
            $table->string('card_image')->nullable();
            $table->string('card_link')->nullable();

            $table->timestamps();
        });

        // Seed the 5 standard curated brand slides
        $this->seedInitialSlides();
    }

    /**
     * Seed initial curated slides from the original design.
     */
    private function seedInitialSlides(): void
    {
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
                'bg_image' => 'assets/images/hero/hero-01.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'FLAGSHIP NOOTROPIC',
                'card_badge_ar' => 'المنشط الذهني الرائد',
                'card_title_en' => 'BLUE MIND',
                'card_title_ar' => 'بلو مايند',
                'card_subtitle_en' => '★ 4.9 Verified Reviews',
                'card_subtitle_ar' => '★ 4.9 تقييم سريري معتمد',
                'card_image' => 'assets/products/blue-mind.webp',
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
                'bg_image' => 'assets/images/hero/hero-02.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'MITOCHONDRIAL ATP',
                'card_badge_ar' => 'طاقة الخلية والمناعة',
                'card_title_en' => 'BLUE SHIELD',
                'card_title_ar' => 'بلو شيلد',
                'card_subtitle_en' => '★ 4.9 Verified Reviews',
                'card_subtitle_ar' => '★ 4.9 تقييم موثق',
                'card_image' => 'assets/products/blue-cell.webp',
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
                'bg_image' => 'assets/images/hero/hero-03.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'JOINT MOBILITY',
                'card_badge_ar' => 'مرونة وصحة المفاصل',
                'card_title_en' => 'BLUE JOINT',
                'card_title_ar' => 'بلو جوينت',
                'card_subtitle_en' => '★ 4.8 Verified Reviews',
                'card_subtitle_ar' => '★ 4.8 تقييم سريري',
                'card_image' => 'assets/products/blue-flex.webp',
                'card_link' => '/product/blue-joint',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => null, // Hotspot Slide (The exact one from the user's screenshot!)
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
                'bg_image' => 'assets/images/hero/hero-04.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'CENTENARIAN HOTSPOT',
                'card_badge_ar' => 'بؤرة المعمرين الفائقة',
                'card_title_en' => 'OKINAWA, JAPAN',
                'card_title_ar' => 'أوكيناوا، اليابان',
                'card_subtitle_en' => 'Moai Social & Plant Diet',
                'card_subtitle_ar' => 'روابط مواي الاجتماعية والغذاء النباتي',
                'card_image' => 'assets/images/okinawa.webp',
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
                'bg_image' => 'assets/images/hero/hero-05.webp',
                'show_floating_card' => true,
                'card_badge_en' => 'DEEP SLEEP & NERVE MATRIX',
                'card_badge_ar' => 'مصفوفة الأعصاب والنوم المريح',
                'card_title_en' => 'BLUE PEA B COMPLEX',
                'card_title_ar' => 'بلو بي إي إيه ب كومبلكس',
                'card_subtitle_en' => '★ 4.9 Verified Reviews',
                'card_subtitle_ar' => '★ 4.9 تقييم موثق',
                'card_image' => 'assets/products/blue-rest.webp',
                'card_link' => '/product/blue-pea-b-complex',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('hero_slides')->insert($slides);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
