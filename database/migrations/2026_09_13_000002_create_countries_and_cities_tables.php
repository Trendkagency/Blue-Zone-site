<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Countries Table
        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('iso2', 2)->unique();
                $table->string('phone_code', 10); // e.g. +966, +20, +971
                $table->string('currency_code', 10)->nullable(); // e.g. SAR, EGP, AED
                $table->string('currency_symbol_en', 10)->nullable();
                $table->string('currency_symbol_ar', 10)->nullable();
                $table->string('flag_emoji', 10)->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Cities Table
        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('state_or_province')->nullable();
                $table->decimal('shipping_cost', 10, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Enhance Locations Table with Country and City Foreign Keys
        if (Schema::hasTable('locations')) {
            Schema::table('locations', function (Blueprint $table) {
                if (!Schema::hasColumn('locations', 'country_id')) {
                    $table->foreignId('country_id')->nullable()->after('type')->constrained('countries')->nullOnDelete();
                }
                if (!Schema::hasColumn('locations', 'city_id')) {
                    $table->foreignId('city_id')->nullable()->after('country_id')->constrained('cities')->nullOnDelete();
                }
            });
        }

        // 4. Seed Default Countries and Major Cities
        $this->seedInitialGeographicData();
    }

    private function seedInitialGeographicData(): void
    {
        $now = now();

        $countriesData = [
            [
                'name_en' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
                'iso2' => 'SA',
                'phone_code' => '+966',
                'currency_code' => 'SAR',
                'currency_symbol_en' => 'SAR',
                'currency_symbol_ar' => 'ر.س',
                'flag_emoji' => '🇸🇦',
                'is_active' => true,
                'sort_order' => 1,
                'cities' => [
                    ['name_en' => 'Riyadh', 'name_ar' => 'الرياض', 'shipping_cost' => 25.00],
                    ['name_en' => 'Jeddah', 'name_ar' => 'جدة', 'shipping_cost' => 30.00],
                    ['name_en' => 'Dammam', 'name_ar' => 'الدمام', 'shipping_cost' => 30.00],
                    ['name_en' => 'Khobar', 'name_ar' => 'الخبر', 'shipping_cost' => 30.00],
                    ['name_en' => 'Mecca', 'name_ar' => 'مكة المكرمة', 'shipping_cost' => 35.00],
                    ['name_en' => 'Medina', 'name_ar' => 'المدينة المنورة', 'shipping_cost' => 35.00],
                    ['name_en' => 'Tabuk', 'name_ar' => 'تبوك', 'shipping_cost' => 40.00],
                    ['name_en' => 'Abha', 'name_ar' => 'أبها', 'shipping_cost' => 40.00],
                ],
            ],
            [
                'name_en' => 'United Arab Emirates',
                'name_ar' => 'الإمارات العربية المتحدة',
                'iso2' => 'AE',
                'phone_code' => '+971',
                'currency_code' => 'AED',
                'currency_symbol_en' => 'AED',
                'currency_symbol_ar' => 'د.إ',
                'flag_emoji' => '🇦🇪',
                'is_active' => true,
                'sort_order' => 2,
                'cities' => [
                    ['name_en' => 'Dubai', 'name_ar' => 'دبي', 'shipping_cost' => 35.00],
                    ['name_en' => 'Abu Dhabi', 'name_ar' => 'أبو ظبي', 'shipping_cost' => 35.00],
                    ['name_en' => 'Sharjah', 'name_ar' => 'الشارقة', 'shipping_cost' => 40.00],
                    ['name_en' => 'Ajman', 'name_ar' => 'عجمان', 'shipping_cost' => 40.00],
                ],
            ],
            [
                'name_en' => 'Egypt',
                'name_ar' => 'جمهورية مصر العربية',
                'iso2' => 'EG',
                'phone_code' => '+20',
                'currency_code' => 'EGP',
                'currency_symbol_en' => 'EGP',
                'currency_symbol_ar' => 'ج.م',
                'flag_emoji' => '🇪🇬',
                'is_active' => true,
                'sort_order' => 3,
                'cities' => [
                    ['name_en' => 'Cairo', 'name_ar' => 'القاهرة', 'shipping_cost' => 50.00],
                    ['name_en' => 'Alexandria', 'name_ar' => 'الإسكندرية', 'shipping_cost' => 60.00],
                    ['name_en' => 'Giza', 'name_ar' => 'الجيزة', 'shipping_cost' => 50.00],
                    ['name_en' => 'Mansoura', 'name_ar' => 'المنصورة', 'shipping_cost' => 65.00],
                    ['name_en' => 'Tanta', 'name_ar' => 'طنطا', 'shipping_cost' => 65.00],
                ],
            ],
            [
                'name_en' => 'Kuwait',
                'name_ar' => 'دولة الكويت',
                'iso2' => 'KW',
                'phone_code' => '+965',
                'currency_code' => 'KWD',
                'currency_symbol_en' => 'KWD',
                'currency_symbol_ar' => 'د.ك',
                'flag_emoji' => '🇰🇼',
                'is_active' => true,
                'sort_order' => 4,
                'cities' => [
                    ['name_en' => 'Kuwait City', 'name_ar' => 'مدينة الكويت', 'shipping_cost' => 40.00],
                    ['name_en' => 'Hawalli', 'name_ar' => 'حولي', 'shipping_cost' => 40.00],
                    ['name_en' => 'Salmiya', 'name_ar' => 'السالمية', 'shipping_cost' => 40.00],
                ],
            ],
            [
                'name_en' => 'Qatar',
                'name_ar' => 'دولة قطر',
                'iso2' => 'QA',
                'phone_code' => '+974',
                'currency_code' => 'QAR',
                'currency_symbol_en' => 'QAR',
                'currency_symbol_ar' => 'ر.ق',
                'flag_emoji' => '🇶🇦',
                'is_active' => true,
                'sort_order' => 5,
                'cities' => [
                    ['name_en' => 'Doha', 'name_ar' => 'الدوحة', 'shipping_cost' => 40.00],
                    ['name_en' => 'Al Rayyan', 'name_ar' => 'الريان', 'shipping_cost' => 40.00],
                ],
            ],
            [
                'name_en' => 'Bahrain',
                'name_ar' => 'مملكة البحرين',
                'iso2' => 'BH',
                'phone_code' => '+973',
                'currency_code' => 'BHD',
                'currency_symbol_en' => 'BHD',
                'currency_symbol_ar' => 'د.ب',
                'flag_emoji' => '🇧🇭',
                'is_active' => true,
                'sort_order' => 6,
                'cities' => [
                    ['name_en' => 'Manama', 'name_ar' => 'المنامة', 'shipping_cost' => 35.00],
                    ['name_en' => 'Riffa', 'name_ar' => 'الرفاع', 'shipping_cost' => 35.00],
                ],
            ],
            [
                'name_en' => 'Oman',
                'name_ar' => 'سلطنة عمان',
                'iso2' => 'OM',
                'phone_code' => '+968',
                'currency_code' => 'OMR',
                'currency_symbol_en' => 'OMR',
                'currency_symbol_ar' => 'ر.ع',
                'flag_emoji' => '🇴🇲',
                'is_active' => true,
                'sort_order' => 7,
                'cities' => [
                    ['name_en' => 'Muscat', 'name_ar' => 'مسقط', 'shipping_cost' => 40.00],
                    ['name_en' => 'Salalah', 'name_ar' => 'صلالة', 'shipping_cost' => 50.00],
                ],
            ],
            [
                'name_en' => 'United States',
                'name_ar' => 'الولايات المتحدة',
                'iso2' => 'US',
                'phone_code' => '+1',
                'currency_code' => 'USD',
                'currency_symbol_en' => 'USD',
                'currency_symbol_ar' => '$',
                'flag_emoji' => '🇺🇸',
                'is_active' => true,
                'sort_order' => 8,
                'cities' => [
                    ['name_en' => 'New York', 'name_ar' => 'نيويورك', 'shipping_cost' => 100.00],
                    ['name_en' => 'Los Angeles', 'name_ar' => 'لوس أنجلوس', 'shipping_cost' => 100.00],
                    ['name_en' => 'Miami', 'name_ar' => 'ميامي', 'shipping_cost' => 100.00],
                ],
            ],
            [
                'name_en' => 'United Kingdom',
                'name_ar' => 'المملكة المتحدة',
                'iso2' => 'GB',
                'phone_code' => '+44',
                'currency_code' => 'GBP',
                'currency_symbol_en' => 'GBP',
                'currency_symbol_ar' => '£',
                'flag_emoji' => '🇬🇧',
                'is_active' => true,
                'sort_order' => 9,
                'cities' => [
                    ['name_en' => 'London', 'name_ar' => 'لندن', 'shipping_cost' => 90.00],
                    ['name_en' => 'Manchester', 'name_ar' => 'مانشستر', 'shipping_cost' => 90.00],
                ],
            ],
        ];

        foreach ($countriesData as $countryData) {
            $cities = $countryData['cities'];
            unset($countryData['cities']);

            $existing = DB::table('countries')->where('iso2', $countryData['iso2'])->first();
            if (!$existing) {
                $countryId = DB::table('countries')->insertGetId(array_merge($countryData, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            } else {
                $countryId = $existing->id;
            }

            foreach ($cities as $idx => $city) {
                $cityExists = DB::table('cities')->where('country_id', $countryId)->where('name_en', $city['name_en'])->exists();
                if (!$cityExists) {
                    DB::table('cities')->insert([
                        'country_id' => $countryId,
                        'name_en' => $city['name_en'],
                        'name_ar' => $city['name_ar'],
                        'shipping_cost' => $city['shipping_cost'] ?? 0.00,
                        'is_active' => true,
                        'sort_order' => $idx + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // Link default core locations to Saudi Arabia / Riyadh if available
        $sa = DB::table('countries')->where('iso2', 'SA')->first();
        if ($sa) {
            $riyadh = DB::table('cities')->where('country_id', $sa->id)->where('name_en', 'Riyadh')->first();
            DB::table('locations')->whereNull('country_id')->update([
                'country_id' => $sa->id,
                'city_id' => $riyadh?->id,
                'city' => 'Riyadh',
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('locations')) {
            Schema::table('locations', function (Blueprint $table) {
                if (Schema::hasColumn('locations', 'city_id')) {
                    $table->dropForeign(['city_id']);
                    $table->dropColumn('city_id');
                }
                if (Schema::hasColumn('locations', 'country_id')) {
                    $table->dropForeign(['country_id']);
                    $table->dropColumn('country_id');
                }
            });
        }

        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');
    }
};
