<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create Breaks Table (Territory Brick/Break Level below Area)
        if (!Schema::hasTable('breaks')) {
            Schema::create('breaks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
                $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
                $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('code')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['country_id', 'city_id', 'area_id', 'is_active']);
            });
        }

        // 2. Enhance Users Table with break_id for granular territory assignment
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'break_id')) {
                    $table->foreignId('break_id')->nullable()->after('area_id')->constrained('breaks')->nullOnDelete();
                }
            });
        }

        // 3. Enhance MR Contacts Table with break_id
        if (Schema::hasTable('mr_contacts')) {
            Schema::table('mr_contacts', function (Blueprint $table) {
                if (!Schema::hasColumn('mr_contacts', 'break_id')) {
                    $table->foreignId('break_id')->nullable()->after('area_id')->constrained('breaks')->nullOnDelete();
                }
            });
        }

        // 4. Seed Standard Sample Breaks for Existing Areas
        $this->seedInitialBreaks();
    }

    public function down(): void
    {
        if (Schema::hasTable('mr_contacts') && Schema::hasColumn('mr_contacts', 'break_id')) {
            Schema::table('mr_contacts', function (Blueprint $table) {
                $table->dropForeign(['break_id']);
                $table->dropColumn('break_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'break_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['break_id']);
                $table->dropColumn('break_id');
            });
        }

        Schema::dropIfExists('breaks');
    }

    protected function seedInitialBreaks(): void
    {
        $now = now();

        $areas = DB::table('areas')->get()->keyBy('name_en');

        $sampleBreaks = [
            // Central Cairo Areas -> Breaks
            'Downtown & Tahrir' => [
                ['name_en' => 'Talaat Harb & Bab El-Louk', 'name_ar' => 'طلعت حرب وباب اللوق', 'code' => 'BRK-CAI-DT-01', 'sort_order' => 1],
                ['name_en' => 'Qasr El-Aini & Garden City', 'name_ar' => 'قصر العيني وجاردن سيتي', 'code' => 'BRK-CAI-DT-02', 'sort_order' => 2],
                ['name_en' => 'Abdeen & Al-Falaki', 'name_ar' => 'عابدين والفلكي', 'code' => 'BRK-CAI-DT-03', 'sort_order' => 3],
            ],
            'Zamalek' => [
                ['name_en' => 'North Zamalek (26th July)', 'name_ar' => 'شمال الزمالك (26 يوليو)', 'code' => 'BRK-CAI-ZAM-01', 'sort_order' => 1],
                ['name_en' => 'South Zamalek (Gezira Club)', 'name_ar' => 'جنوب الزمالك (نادي الجزيرة)', 'code' => 'BRK-CAI-ZAM-02', 'sort_order' => 2],
            ],
            'Heliopolis & Korba' => [
                ['name_en' => 'El-Korba Heritage Sector', 'name_ar' => 'مربع الكوربة التراثي', 'code' => 'BRK-CAI-HEL-01', 'sort_order' => 1],
                ['name_en' => 'Triumph & Merghany Zone', 'name_ar' => 'مربع تريومف والميرغني', 'code' => 'BRK-CAI-HEL-02', 'sort_order' => 2],
                ['name_en' => 'Hegaz & Saint Fatima', 'name_ar' => 'مربع الحجاز وسانت فاتيما', 'code' => 'BRK-CAI-HEL-03', 'sort_order' => 3],
            ],
            'Nasr City Sector 1' => [
                ['name_en' => 'Abbas El-Akkad Medical Hub', 'name_ar' => 'مربع عباس العقاد الطبي', 'code' => 'BRK-CAI-NC1-01', 'sort_order' => 1],
                ['name_en' => 'Makram Ebeid Sector', 'name_ar' => 'مربع مكرم عبيد', 'code' => 'BRK-CAI-NC1-02', 'sort_order' => 2],
                ['name_en' => 'Tayaran & Rabaa Clinic Zone', 'name_ar' => 'مربع الطيران ورابعة', 'code' => 'BRK-CAI-NC1-03', 'sort_order' => 3],
            ],
            'Dokki & Mohandessin' => [
                ['name_en' => 'Messaha & Tahrir Street (Dokki)', 'name_ar' => 'مربع المساحة وشارع التحرير (الدقي)', 'code' => 'BRK-GIZ-DOK-01', 'sort_order' => 1],
                ['name_en' => 'Lebanon Square & Shehab (Mohandessin)', 'name_ar' => 'مربع ميدان لبنان وشهاب (المهندسين)', 'code' => 'BRK-GIZ-MOH-01', 'sort_order' => 2],
                ['name_en' => 'Batal Ahmed Abdelaziz & Gamet Dowal', 'name_ar' => 'مربع بطل أحمد وجامعة الدول', 'code' => 'BRK-GIZ-MOH-02', 'sort_order' => 3],
            ],
            'Maadi & Degla' => [
                ['name_en' => 'Old Maadi & Road 9', 'name_ar' => 'المعادي القديمة وشارع 9', 'code' => 'BRK-CAI-MAA-01', 'sort_order' => 1],
                ['name_en' => 'Degla & Nerco Medical Hub', 'name_ar' => 'دجلة ونيركو للعيادات', 'code' => 'BRK-CAI-MAA-02', 'sort_order' => 2],
                ['name_en' => 'Zahraa El-Maadi Hub', 'name_ar' => 'زهراء المعادي المركزية', 'code' => 'BRK-CAI-MAA-03', 'sort_order' => 3],
            ],
            'Olaya & King Fahd' => [
                ['name_en' => 'Olaya Towers Medical Block', 'name_ar' => 'مربع أبراج العليا الطبي', 'code' => 'BRK-RUH-OLY-01', 'sort_order' => 1],
                ['name_en' => 'Tahlia & King Fahd Medical Zone', 'name_ar' => 'مربع التحلية وطريق الملك فهد', 'code' => 'BRK-RUH-OLY-02', 'sort_order' => 2],
            ],
        ];

        foreach ($sampleBreaks as $areaNameEn => $breaksList) {
            $area = $areas->get($areaNameEn);
            if (!$area) {
                // Try partial match
                $area = DB::table('areas')->where('name_en', 'like', "%{$areaNameEn}%")->first();
            }

            if ($area) {
                foreach ($breaksList as $b) {
                    $exists = DB::table('breaks')->where('code', $b['code'])->exists();
                    if (!$exists) {
                        DB::table('breaks')->insert([
                            'country_id' => $area->country_id,
                            'city_id' => $area->city_id,
                            'area_id' => $area->id,
                            'name_en' => $b['name_en'],
                            'name_ar' => $b['name_ar'],
                            'code' => $b['code'],
                            'is_active' => true,
                            'sort_order' => $b['sort_order'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }
};
