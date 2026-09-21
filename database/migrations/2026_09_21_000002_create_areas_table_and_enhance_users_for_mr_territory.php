<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create Areas Table
        if (!Schema::hasTable('areas')) {
            Schema::create('areas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
                $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('code')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['country_id', 'city_id', 'is_active']);
            });
        }

        // 2. Enhance Users Table with Country, City, Area for MR Territory Assignment
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'country_id')) {
                    $table->foreignId('country_id')->nullable()->after('role_id')->constrained('countries')->nullOnDelete();
                }
                if (!Schema::hasColumn('users', 'city_id')) {
                    $table->foreignId('city_id')->nullable()->after('country_id')->constrained('cities')->nullOnDelete();
                }
                if (!Schema::hasColumn('users', 'area_id')) {
                    $table->foreignId('area_id')->nullable()->after('city_id')->constrained('areas')->nullOnDelete();
                }
            });
        }

        // 3. Enhance MR Contacts Table with Area
        if (Schema::hasTable('mr_contacts')) {
            Schema::table('mr_contacts', function (Blueprint $table) {
                if (!Schema::hasColumn('mr_contacts', 'area_id')) {
                    $table->foreignId('area_id')->nullable()->after('city_id')->constrained('areas')->nullOnDelete();
                }
            });
        }

        // 4. Seed Standard Sample Areas for Key Cities
        $this->seedInitialAreas();
    }

    public function down(): void
    {
        if (Schema::hasTable('mr_contacts') && Schema::hasColumn('mr_contacts', 'area_id')) {
            Schema::table('mr_contacts', function (Blueprint $table) {
                $table->dropForeign(['area_id']);
                $table->dropColumn('area_id');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'area_id')) {
                    $table->dropForeign(['area_id']);
                    $table->dropColumn('area_id');
                }
                if (Schema::hasColumn('users', 'city_id')) {
                    $table->dropForeign(['city_id']);
                    $table->dropColumn('city_id');
                }
                if (Schema::hasColumn('users', 'country_id')) {
                    $table->dropForeign(['country_id']);
                    $table->dropColumn('country_id');
                }
            });
        }

        Schema::dropIfExists('areas');
    }

    private function seedInitialAreas(): void
    {
        $now = now();

        $areasByCity = [
            // Egypt -> Cairo
            'Cairo' => [
                ['name_en' => 'Maadi', 'name_ar' => 'المعادي', 'code' => 'CAI-MAA'],
                ['name_en' => 'Nasr City', 'name_ar' => 'مدينة نصر', 'code' => 'CAI-NAS'],
                ['name_en' => 'Heliopolis (Masr El-Gedida)', 'name_ar' => 'مصر الجديدة', 'code' => 'CAI-HEL'],
                ['name_en' => 'New Cairo (Fifth Settlement)', 'name_ar' => 'التجمع الخامس والقاهرة الجديدة', 'code' => 'CAI-NCA'],
                ['name_en' => 'Zamalek', 'name_ar' => 'الزمالك', 'code' => 'CAI-ZAM'],
                ['name_en' => 'Downtown (Wast El-Balad)', 'name_ar' => 'وسط البلد', 'code' => 'CAI-DWT'],
                ['name_en' => 'Shubra', 'name_ar' => 'شبرا', 'code' => 'CAI-SHU'],
            ],
            // Egypt -> Giza
            'Giza' => [
                ['name_en' => 'Dokki', 'name_ar' => 'الدقي', 'code' => 'GIZ-DOK'],
                ['name_en' => 'Mohandessin', 'name_ar' => 'المهندسين', 'code' => 'GIZ-MOH'],
                ['name_en' => 'Sheikh Zayed City', 'name_ar' => 'مدينة الشيخ زايد', 'code' => 'GIZ-ZAY'],
                ['name_en' => '6th of October City', 'name_ar' => 'مدينة 6 أكتوبر', 'code' => 'GIZ-OCT'],
                ['name_en' => 'Al-Haram', 'name_ar' => 'الهرم', 'code' => 'GIZ-HAR'],
            ],
            // Egypt -> Alexandria
            'Alexandria' => [
                ['name_en' => 'Smouha', 'name_ar' => 'سموحة', 'code' => 'ALX-SMO'],
                ['name_en' => 'Miami', 'name_ar' => 'ميامي', 'code' => 'ALX-MIA'],
                ['name_en' => 'Sidi Gaber', 'name_ar' => 'سيدي جابر', 'code' => 'ALX-SID'],
                ['name_en' => 'Roushdy', 'name_ar' => 'رشدي', 'code' => 'ALX-ROU'],
            ],
            // Saudi Arabia -> Riyadh
            'Riyadh' => [
                ['name_en' => 'Al-Olaya', 'name_ar' => 'العليا', 'code' => 'RUH-OLA'],
                ['name_en' => 'Al-Malaz', 'name_ar' => 'الملز', 'code' => 'RUH-MAL'],
                ['name_en' => 'Al-Nakheel', 'name_ar' => 'النخيل', 'code' => 'RUH-NAK'],
                ['name_en' => 'Al-Sahafa', 'name_ar' => 'الصحافة', 'code' => 'RUH-SAH'],
                ['name_en' => 'Al-Yasmin', 'name_ar' => 'الياسمين', 'code' => 'RUH-YAS'],
                ['name_en' => 'King Abdullah Financial District', 'name_ar' => 'مركز الملك عبدالله المالي', 'code' => 'RUH-KAFD'],
            ],
            // Saudi Arabia -> Jeddah
            'Jeddah' => [
                ['name_en' => 'Al-Hamra', 'name_ar' => 'الحمراء', 'code' => 'JED-HAM'],
                ['name_en' => 'Al-Rawdah', 'name_ar' => 'الروضة', 'code' => 'JED-RAW'],
                ['name_en' => 'Al-Safa', 'name_ar' => 'الصفا', 'code' => 'JED-SAF'],
                ['name_en' => 'Al-Zahra', 'name_ar' => 'الزهراء', 'code' => 'JED-ZAH'],
            ],
            // UAE -> Dubai
            'Dubai' => [
                ['name_en' => 'Downtown Dubai', 'name_ar' => 'وسط مدينة دبي', 'code' => 'DXB-DWT'],
                ['name_en' => 'Dubai Marina & JBR', 'name_ar' => 'دبي مارينا و جي بي آر', 'code' => 'DXB-MAR'],
                ['name_en' => 'Business Bay', 'name_ar' => 'الخليج التجاري', 'code' => 'DXB-BAY'],
                ['name_en' => 'Deira Healthcare District', 'name_ar' => 'ديرة والمنطقة الطبية', 'code' => 'DXB-DEI'],
                ['name_en' => 'Dubai Healthcare City', 'name_ar' => 'مدينة دبي الطبية', 'code' => 'DXB-DHCC'],
            ],
        ];

        foreach ($areasByCity as $cityName => $areas) {
            $city = DB::table('cities')->where('name_en', $cityName)->first();
            if ($city) {
                foreach ($areas as $index => $areaData) {
                    $exists = DB::table('areas')
                        ->where('city_id', $city->id)
                        ->where('name_en', $areaData['name_en'])
                        ->exists();

                    if (!$exists) {
                        DB::table('areas')->insert([
                            'country_id' => $city->country_id,
                            'city_id' => $city->id,
                            'name_en' => $areaData['name_en'],
                            'name_ar' => $areaData['name_ar'],
                            'code' => $areaData['code'],
                            'is_active' => true,
                            'sort_order' => $index + 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }
};
