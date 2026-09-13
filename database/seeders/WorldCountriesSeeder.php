<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorldCountriesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            // Middle East & GCC (High Priority)
            ['iso2' => 'SA', 'name_en' => 'Saudi Arabia', 'name_ar' => 'المملكة العربية السعودية', 'phone_code' => '+966', 'currency_code' => 'SAR', 'currency_symbol_en' => 'SAR', 'currency_symbol_ar' => 'ر.س', 'flag_emoji' => '🇸🇦', 'sort_order' => 1],
            ['iso2' => 'AE', 'name_en' => 'United Arab Emirates', 'name_ar' => 'الإمارات العربية المتحدة', 'phone_code' => '+971', 'currency_code' => 'AED', 'currency_symbol_en' => 'AED', 'currency_symbol_ar' => 'د.إ', 'flag_emoji' => '🇦🇪', 'sort_order' => 2],
            ['iso2' => 'KW', 'name_en' => 'Kuwait', 'name_ar' => 'الكويت', 'phone_code' => '+965', 'currency_code' => 'KWD', 'currency_symbol_en' => 'KWD', 'currency_symbol_ar' => 'د.ك', 'flag_emoji' => '🇰🇼', 'sort_order' => 3],
            ['iso2' => 'QA', 'name_en' => 'Qatar', 'name_ar' => 'قطر', 'phone_code' => '+974', 'currency_code' => 'QAR', 'currency_symbol_en' => 'QAR', 'currency_symbol_ar' => 'ر.ق', 'flag_emoji' => '🇶🇦', 'sort_order' => 4],
            ['iso2' => 'BH', 'name_en' => 'Bahrain', 'name_ar' => 'البحرين', 'phone_code' => '+973', 'currency_code' => 'BHD', 'currency_symbol_en' => 'BHD', 'currency_symbol_ar' => 'د.ب', 'flag_emoji' => '🇧🇭', 'sort_order' => 5],
            ['iso2' => 'OM', 'name_en' => 'Oman', 'name_ar' => 'عُمان', 'phone_code' => '+968', 'currency_code' => 'OMR', 'currency_symbol_en' => 'OMR', 'currency_symbol_ar' => 'ر.ع', 'flag_emoji' => '🇴🇲', 'sort_order' => 6],
            ['iso2' => 'EG', 'name_en' => 'Egypt', 'name_ar' => 'مصر', 'phone_code' => '+20', 'currency_code' => 'EGP', 'currency_symbol_en' => 'EGP', 'currency_symbol_ar' => 'ج.م', 'flag_emoji' => '🇪🇬', 'sort_order' => 7],
            ['iso2' => 'JO', 'name_en' => 'Jordan', 'name_ar' => 'الأردن', 'phone_code' => '+962', 'currency_code' => 'JOD', 'currency_symbol_en' => 'JOD', 'currency_symbol_ar' => 'د.أ', 'flag_emoji' => '🇯🇴', 'sort_order' => 8],
            ['iso2' => 'LB', 'name_en' => 'Lebanon', 'name_ar' => 'لبنان', 'phone_code' => '+961', 'currency_code' => 'LBP', 'currency_symbol_en' => 'LBP', 'currency_symbol_ar' => 'ل.ل', 'flag_emoji' => '🇱🇧', 'sort_order' => 9],
            ['iso2' => 'IQ', 'name_en' => 'Iraq', 'name_ar' => 'العراق', 'phone_code' => '+964', 'currency_code' => 'IQD', 'currency_symbol_en' => 'IQD', 'currency_symbol_ar' => 'د.ع', 'flag_emoji' => '🇮🇶', 'sort_order' => 10],
            ['iso2' => 'YE', 'name_en' => 'Yemen', 'name_ar' => 'اليمن', 'phone_code' => '+967', 'currency_code' => 'YER', 'currency_symbol_en' => 'YER', 'currency_symbol_ar' => 'ر.ي', 'flag_emoji' => '🇾🇪', 'sort_order' => 11],
            ['iso2' => 'MA', 'name_en' => 'Morocco', 'name_ar' => 'المغرب', 'phone_code' => '+212', 'currency_code' => 'MAD', 'currency_symbol_en' => 'MAD', 'currency_symbol_ar' => 'د.م', 'flag_emoji' => '🇲🇦', 'sort_order' => 12],
            ['iso2' => 'DZ', 'name_en' => 'Algeria', 'name_ar' => 'الجزائر', 'phone_code' => '+213', 'currency_code' => 'DZD', 'currency_symbol_en' => 'DZD', 'currency_symbol_ar' => 'د.ج', 'flag_emoji' => '🇩🇿', 'sort_order' => 13],
            ['iso2' => 'TN', 'name_en' => 'Tunisia', 'name_ar' => 'تونس', 'phone_code' => '+216', 'currency_code' => 'TND', 'currency_symbol_en' => 'TND', 'currency_symbol_ar' => 'د.ت', 'flag_emoji' => '🇹🇳', 'sort_order' => 14],
            ['iso2' => 'LY', 'name_en' => 'Libya', 'name_ar' => 'ليبيا', 'phone_code' => '+218', 'currency_code' => 'LYD', 'currency_symbol_en' => 'LYD', 'currency_symbol_ar' => 'د.ل', 'flag_emoji' => '🇱🇾', 'sort_order' => 15],
            ['iso2' => 'SD', 'name_en' => 'Sudan', 'name_ar' => 'السودان', 'phone_code' => '+249', 'currency_code' => 'SDG', 'currency_symbol_en' => 'SDG', 'currency_symbol_ar' => 'ج.س', 'flag_emoji' => '🇸🇩', 'sort_order' => 16],
            ['iso2' => 'SY', 'name_en' => 'Syria', 'name_ar' => 'سوريا', 'phone_code' => '+963', 'currency_code' => 'SYP', 'currency_symbol_en' => 'SYP', 'currency_symbol_ar' => 'ل.س', 'flag_emoji' => '🇸🇾', 'sort_order' => 17],
            ['iso2' => 'PS', 'name_en' => 'Palestine', 'name_ar' => 'فلسطين', 'phone_code' => '+970', 'currency_code' => 'ILS', 'currency_symbol_en' => 'ILS', 'currency_symbol_ar' => 'ش.ج', 'flag_emoji' => '🇵🇸', 'sort_order' => 18],

            // North America
            ['iso2' => 'US', 'name_en' => 'United States', 'name_ar' => 'الولايات المتحدة', 'phone_code' => '+1', 'currency_code' => 'USD', 'currency_symbol_en' => '$', 'currency_symbol_ar' => '$', 'flag_emoji' => '🇺🇸', 'sort_order' => 20],
            ['iso2' => 'CA', 'name_en' => 'Canada', 'name_ar' => 'كندا', 'phone_code' => '+1', 'currency_code' => 'CAD', 'currency_symbol_en' => 'C$', 'currency_symbol_ar' => 'C$', 'flag_emoji' => '🇨🇦', 'sort_order' => 21],
            ['iso2' => 'MX', 'name_en' => 'Mexico', 'name_ar' => 'المكسيك', 'phone_code' => '+52', 'currency_code' => 'MXN', 'currency_symbol_en' => 'Mex$', 'currency_symbol_ar' => '$', 'flag_emoji' => '🇲🇽', 'sort_order' => 22],

            // Europe
            ['iso2' => 'GB', 'name_en' => 'United Kingdom', 'name_ar' => 'المملكة المتحدة', 'phone_code' => '+44', 'currency_code' => 'GBP', 'currency_symbol_en' => '£', 'currency_symbol_ar' => '£', 'flag_emoji' => '🇬🇧', 'sort_order' => 30],
            ['iso2' => 'DE', 'name_en' => 'Germany', 'name_ar' => 'ألمانيا', 'phone_code' => '+49', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇩🇪', 'sort_order' => 31],
            ['iso2' => 'FR', 'name_en' => 'France', 'name_ar' => 'فرنسا', 'phone_code' => '+33', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇫🇷', 'sort_order' => 32],
            ['iso2' => 'IT', 'name_en' => 'Italy', 'name_ar' => 'إيطاليا', 'phone_code' => '+39', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇮🇹', 'sort_order' => 33],
            ['iso2' => 'ES', 'name_en' => 'Spain', 'name_ar' => 'إسبانيا', 'phone_code' => '+34', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇪🇸', 'sort_order' => 34],
            ['iso2' => 'NL', 'name_en' => 'Netherlands', 'name_ar' => 'هولندا', 'phone_code' => '+31', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇳🇱', 'sort_order' => 35],
            ['iso2' => 'CH', 'name_en' => 'Switzerland', 'name_ar' => 'سويسرا', 'phone_code' => '+41', 'currency_code' => 'CHF', 'currency_symbol_en' => 'CHF', 'currency_symbol_ar' => 'CHF', 'flag_emoji' => '🇨🇭', 'sort_order' => 36],
            ['iso2' => 'BE', 'name_en' => 'Belgium', 'name_ar' => 'بلجيكا', 'phone_code' => '+32', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇧🇪', 'sort_order' => 37],
            ['iso2' => 'AT', 'name_en' => 'Austria', 'name_ar' => 'النمسا', 'phone_code' => '+43', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇦🇹', 'sort_order' => 38],
            ['iso2' => 'SE', 'name_en' => 'Sweden', 'name_ar' => 'السويد', 'phone_code' => '+46', 'currency_code' => 'SEK', 'currency_symbol_en' => 'kr', 'currency_symbol_ar' => 'kr', 'flag_emoji' => '🇸🇪', 'sort_order' => 39],
            ['iso2' => 'NO', 'name_en' => 'Norway', 'name_ar' => 'النرويج', 'phone_code' => '+47', 'currency_code' => 'NOK', 'currency_symbol_en' => 'kr', 'currency_symbol_ar' => 'kr', 'flag_emoji' => '🇳🇴', 'sort_order' => 40],
            ['iso2' => 'DK', 'name_en' => 'Denmark', 'name_ar' => 'الدنمارك', 'phone_code' => '+45', 'currency_code' => 'DKK', 'currency_symbol_en' => 'kr', 'currency_symbol_ar' => 'kr', 'flag_emoji' => '🇩🇰', 'sort_order' => 41],
            ['iso2' => 'FI', 'name_en' => 'Finland', 'name_ar' => 'فنلندا', 'phone_code' => '+358', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇫🇮', 'sort_order' => 42],
            ['iso2' => 'IE', 'name_en' => 'Ireland', 'name_ar' => 'أيرلندا', 'phone_code' => '+353', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇮🇪', 'sort_order' => 43],
            ['iso2' => 'PL', 'name_en' => 'Poland', 'name_ar' => 'بولندا', 'phone_code' => '+48', 'currency_code' => 'PLN', 'currency_symbol_en' => 'zł', 'currency_symbol_ar' => 'zł', 'flag_emoji' => '🇵🇱', 'sort_order' => 44],
            ['iso2' => 'PT', 'name_en' => 'Portugal', 'name_ar' => 'البرتغال', 'phone_code' => '+351', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇵🇹', 'sort_order' => 45],
            ['iso2' => 'GR', 'name_en' => 'Greece', 'name_ar' => 'اليونان', 'phone_code' => '+30', 'currency_code' => 'EUR', 'currency_symbol_en' => '€', 'currency_symbol_ar' => '€', 'flag_emoji' => '🇬🇷', 'sort_order' => 46],
            ['iso2' => 'TR', 'name_en' => 'Turkey', 'name_ar' => 'تركيا', 'phone_code' => '+90', 'currency_code' => 'TRY', 'currency_symbol_en' => '₺', 'currency_symbol_ar' => '₺', 'flag_emoji' => '🇹🇷', 'sort_order' => 47],
            ['iso2' => 'RU', 'name_en' => 'Russia', 'name_ar' => 'روسيا', 'phone_code' => '+7', 'currency_code' => 'RUB', 'currency_symbol_en' => '₽', 'currency_symbol_ar' => '₽', 'flag_emoji' => '🇷🇺', 'sort_order' => 48],
            ['iso2' => 'UA', 'name_en' => 'Ukraine', 'name_ar' => 'أوكرانيا', 'phone_code' => '+380', 'currency_code' => 'UAH', 'currency_symbol_en' => '₴', 'currency_symbol_ar' => '₴', 'flag_emoji' => '🇺🇦', 'sort_order' => 49],

            // Asia & Pacific
            ['iso2' => 'CN', 'name_en' => 'China', 'name_ar' => 'الصين', 'phone_code' => '+86', 'currency_code' => 'CNY', 'currency_symbol_en' => '¥', 'currency_symbol_ar' => '¥', 'flag_emoji' => '🇨🇳', 'sort_order' => 60],
            ['iso2' => 'JP', 'name_en' => 'Japan', 'name_ar' => 'اليابان', 'phone_code' => '+81', 'currency_code' => 'JPY', 'currency_symbol_en' => '¥', 'currency_symbol_ar' => '¥', 'flag_emoji' => '🇯🇵', 'sort_order' => 61],
            ['iso2' => 'KR', 'name_en' => 'South Korea', 'name_ar' => 'كوريا الجنوبية', 'phone_code' => '+82', 'currency_code' => 'KRW', 'currency_symbol_en' => '₩', 'currency_symbol_ar' => '₩', 'flag_emoji' => '🇰🇷', 'sort_order' => 62],
            ['iso2' => 'IN', 'name_en' => 'India', 'name_ar' => 'الهند', 'phone_code' => '+91', 'currency_code' => 'INR', 'currency_symbol_en' => '₹', 'currency_symbol_ar' => '₹', 'flag_emoji' => '🇮🇳', 'sort_order' => 63],
            ['iso2' => 'PK', 'name_en' => 'Pakistan', 'name_ar' => 'باكستان', 'phone_code' => '+92', 'currency_code' => 'PKR', 'currency_symbol_en' => 'Rs', 'currency_symbol_ar' => 'Rs', 'flag_emoji' => '🇵🇰', 'sort_order' => 64],
            ['iso2' => 'BD', 'name_en' => 'Bangladesh', 'name_ar' => 'بنغلاديش', 'phone_code' => '+880', 'currency_code' => 'BDT', 'currency_symbol_en' => '৳', 'currency_symbol_ar' => '৳', 'flag_emoji' => '🇧🇩', 'sort_order' => 65],
            ['iso2' => 'ID', 'name_en' => 'Indonesia', 'name_ar' => 'إندونيسيا', 'phone_code' => '+62', 'currency_code' => 'IDR', 'currency_symbol_en' => 'Rp', 'currency_symbol_ar' => 'Rp', 'flag_emoji' => '🇮🇩', 'sort_order' => 66],
            ['iso2' => 'MY', 'name_en' => 'Malaysia', 'name_ar' => 'ماليزيا', 'phone_code' => '+60', 'currency_code' => 'MYR', 'currency_symbol_en' => 'RM', 'currency_symbol_ar' => 'RM', 'flag_emoji' => '🇲🇾', 'sort_order' => 67],
            ['iso2' => 'SG', 'name_en' => 'Singapore', 'name_ar' => 'سنغافورة', 'phone_code' => '+65', 'currency_code' => 'SGD', 'currency_symbol_en' => 'S$', 'currency_symbol_ar' => 'S$', 'flag_emoji' => '🇸🇬', 'sort_order' => 68],
            ['iso2' => 'TH', 'name_en' => 'Thailand', 'name_ar' => 'تايلاند', 'phone_code' => '+66', 'currency_code' => 'THB', 'currency_symbol_en' => '฿', 'currency_symbol_ar' => '฿', 'flag_emoji' => '🇹🇭', 'sort_order' => 69],
            ['iso2' => 'PH', 'name_en' => 'Philippines', 'name_ar' => 'الفلبين', 'phone_code' => '+63', 'currency_code' => 'PHP', 'currency_symbol_en' => '₱', 'currency_symbol_ar' => '₱', 'flag_emoji' => '🇵🇭', 'sort_order' => 70],
            ['iso2' => 'VN', 'name_en' => 'Vietnam', 'name_ar' => 'فيتنام', 'phone_code' => '+84', 'currency_code' => 'VND', 'currency_symbol_en' => '₫', 'currency_symbol_ar' => '₫', 'flag_emoji' => '🇻🇳', 'sort_order' => 71],
            ['iso2' => 'AU', 'name_en' => 'Australia', 'name_ar' => 'أستراليا', 'phone_code' => '+61', 'currency_code' => 'AUD', 'currency_symbol_en' => 'A$', 'currency_symbol_ar' => 'A$', 'flag_emoji' => '🇦🇺', 'sort_order' => 72],
            ['iso2' => 'NZ', 'name_en' => 'New Zealand', 'name_ar' => 'نيوزيلندا', 'phone_code' => '+64', 'currency_code' => 'NZD', 'currency_symbol_en' => 'NZ$', 'currency_symbol_ar' => 'NZ$', 'flag_emoji' => '🇳🇿', 'sort_order' => 73],

            // South America
            ['iso2' => 'BR', 'name_en' => 'Brazil', 'name_ar' => 'البرازيل', 'phone_code' => '+55', 'currency_code' => 'BRL', 'currency_symbol_en' => 'R$', 'currency_symbol_ar' => 'R$', 'flag_emoji' => '🇧🇷', 'sort_order' => 80],
            ['iso2' => 'AR', 'name_en' => 'Argentina', 'name_ar' => 'الأرجنتين', 'phone_code' => '+54', 'currency_code' => 'ARS', 'currency_symbol_en' => '$', 'currency_symbol_ar' => '$', 'flag_emoji' => '🇦🇷', 'sort_order' => 81],
            ['iso2' => 'CL', 'name_en' => 'Chile', 'name_ar' => 'تشيلي', 'phone_code' => '+56', 'currency_code' => 'CLP', 'currency_symbol_en' => '$', 'currency_symbol_ar' => '$', 'flag_emoji' => '🇨🇱', 'sort_order' => 82],
            ['iso2' => 'CO', 'name_en' => 'Colombia', 'name_ar' => 'كولومبيا', 'phone_code' => '+57', 'currency_code' => 'COP', 'currency_symbol_en' => '$', 'currency_symbol_ar' => '$', 'flag_emoji' => '🇨🇴', 'sort_order' => 83],

            // Africa
            ['iso2' => 'ZA', 'name_en' => 'South Africa', 'name_ar' => 'جنوب أفريقيا', 'phone_code' => '+27', 'currency_code' => 'ZAR', 'currency_symbol_en' => 'R', 'currency_symbol_ar' => 'R', 'flag_emoji' => '🇿🇦', 'sort_order' => 90],
            ['iso2' => 'NG', 'name_en' => 'Nigeria', 'name_ar' => 'نيجيريا', 'phone_code' => '+234', 'currency_code' => 'NGN', 'currency_symbol_en' => '₦', 'currency_symbol_ar' => '₦', 'flag_emoji' => '🇳🇬', 'sort_order' => 91],
            ['iso2' => 'KE', 'name_en' => 'Kenya', 'name_ar' => 'كينيا', 'phone_code' => '+254', 'currency_code' => 'KES', 'currency_symbol_en' => 'KSh', 'currency_symbol_ar' => 'KSh', 'flag_emoji' => '🇰🇪', 'sort_order' => 92],
            ['iso2' => 'GH', 'name_en' => 'Ghana', 'name_ar' => 'غانا', 'phone_code' => '+233', 'currency_code' => 'GHS', 'currency_symbol_en' => 'GH₵', 'currency_symbol_ar' => 'GH₵', 'flag_emoji' => '🇬🇭', 'sort_order' => 93],

            // Rest of World (Alphabetical)
            ['iso2' => 'AF', 'name_en' => 'Afghanistan', 'name_ar' => 'أفغانستان', 'phone_code' => '+93', 'currency_code' => 'AFN', 'flag_emoji' => '🇦🇫', 'sort_order' => 100],
            ['iso2' => 'AL', 'name_en' => 'Albania', 'name_ar' => 'ألبانيا', 'phone_code' => '+355', 'currency_code' => 'ALL', 'flag_emoji' => '🇦🇱', 'sort_order' => 100],
            ['iso2' => 'AM', 'name_en' => 'Armenia', 'name_ar' => 'أرمينيا', 'phone_code' => '+374', 'currency_code' => 'AMD', 'flag_emoji' => '🇦🇲', 'sort_order' => 100],
            ['iso2' => 'AO', 'name_en' => 'Angola', 'name_ar' => 'أنغولا', 'phone_code' => '+244', 'currency_code' => 'AOA', 'flag_emoji' => '🇦🇴', 'sort_order' => 100],
            ['iso2' => 'AZ', 'name_en' => 'Azerbaijan', 'name_ar' => 'أذربيجان', 'phone_code' => '+994', 'currency_code' => 'AZN', 'flag_emoji' => '🇦🇿', 'sort_order' => 100],
            ['iso2' => 'BA', 'name_en' => 'Bosnia and Herzegovina', 'name_ar' => 'البوسنة والهرسك', 'phone_code' => '+387', 'currency_code' => 'BAM', 'flag_emoji' => '🇧🇦', 'sort_order' => 100],
            ['iso2' => 'BG', 'name_en' => 'Bulgaria', 'name_ar' => 'بلغاريا', 'phone_code' => '+359', 'currency_code' => 'BGN', 'flag_emoji' => '🇧🇬', 'sort_order' => 100],
            ['iso2' => 'BO', 'name_en' => 'Bolivia', 'name_ar' => 'بوليفيا', 'phone_code' => '+591', 'currency_code' => 'BOB', 'flag_emoji' => '🇧🇴', 'sort_order' => 100],
            ['iso2' => 'BY', 'name_en' => 'Belarus', 'name_ar' => 'بيلاروسيا', 'phone_code' => '+375', 'currency_code' => 'BYN', 'flag_emoji' => '🇧🇾', 'sort_order' => 100],
            ['iso2' => 'CY', 'name_en' => 'Cyprus', 'name_ar' => 'قبرص', 'phone_code' => '+357', 'currency_code' => 'EUR', 'flag_emoji' => '🇨🇾', 'sort_order' => 100],
            ['iso2' => 'CZ', 'name_en' => 'Czech Republic', 'name_ar' => 'التشيك', 'phone_code' => '+420', 'currency_code' => 'CZK', 'flag_emoji' => '🇨🇿', 'sort_order' => 100],
            ['iso2' => 'EC', 'name_en' => 'Ecuador', 'name_ar' => 'الإكوادور', 'phone_code' => '+593', 'currency_code' => 'USD', 'flag_emoji' => '🇪🇨', 'sort_order' => 100],
            ['iso2' => 'EE', 'name_en' => 'Estonia', 'name_ar' => 'إستونيا', 'phone_code' => '+372', 'currency_code' => 'EUR', 'flag_emoji' => '🇪🇪', 'sort_order' => 100],
            ['iso2' => 'ET', 'name_en' => 'Ethiopia', 'name_ar' => 'إثيوبيا', 'phone_code' => '+251', 'currency_code' => 'ETB', 'flag_emoji' => '🇪🇹', 'sort_order' => 100],
            ['iso2' => 'GE', 'name_en' => 'Georgia', 'name_ar' => 'جورجيا', 'phone_code' => '+995', 'currency_code' => 'GEL', 'flag_emoji' => '🇬🇪', 'sort_order' => 100],
            ['iso2' => 'HK', 'name_en' => 'Hong Kong', 'name_ar' => 'هونغ كونغ', 'phone_code' => '+852', 'currency_code' => 'HKD', 'flag_emoji' => '🇭🇰', 'sort_order' => 100],
            ['iso2' => 'HR', 'name_en' => 'Croatia', 'name_ar' => 'كرواتيا', 'phone_code' => '+385', 'currency_code' => 'EUR', 'flag_emoji' => '🇭🇷', 'sort_order' => 100],
            ['iso2' => 'HU', 'name_en' => 'Hungary', 'name_ar' => 'المجر', 'phone_code' => '+36', 'currency_code' => 'HUF', 'flag_emoji' => '🇭🇺', 'sort_order' => 100],
            ['iso2' => 'IS', 'name_en' => 'Iceland', 'name_ar' => 'آيسلندا', 'phone_code' => '+354', 'currency_code' => 'ISK', 'flag_emoji' => '🇮🇸', 'sort_order' => 100],
            ['iso2' => 'KZ', 'name_en' => 'Kazakhstan', 'name_ar' => 'كازاخستان', 'phone_code' => '+7', 'currency_code' => 'KZT', 'flag_emoji' => '🇰🇿', 'sort_order' => 100],
            ['iso2' => 'LT', 'name_en' => 'Lithuania', 'name_ar' => 'ليتوانيا', 'phone_code' => '+370', 'currency_code' => 'EUR', 'flag_emoji' => '🇱🇹', 'sort_order' => 100],
            ['iso2' => 'LU', 'name_en' => 'Luxembourg', 'name_ar' => 'لوكسمبورغ', 'phone_code' => '+352', 'currency_code' => 'EUR', 'flag_emoji' => '🇱🇺', 'sort_order' => 100],
            ['iso2' => 'LV', 'name_en' => 'Latvia', 'name_ar' => 'لاتفيا', 'phone_code' => '+371', 'currency_code' => 'EUR', 'flag_emoji' => '🇱🇻', 'sort_order' => 100],
            ['iso2' => 'MT', 'name_en' => 'Malta', 'name_ar' => 'مالطا', 'phone_code' => '+356', 'currency_code' => 'EUR', 'flag_emoji' => '🇲🇹', 'sort_order' => 100],
            ['iso2' => 'PE', 'name_en' => 'Peru', 'name_ar' => 'بيرو', 'phone_code' => '+51', 'currency_code' => 'PEN', 'flag_emoji' => '🇵🇪', 'sort_order' => 100],
            ['iso2' => 'RO', 'name_en' => 'Romania', 'name_ar' => 'رومانيا', 'phone_code' => '+40', 'currency_code' => 'RON', 'flag_emoji' => '🇷🇴', 'sort_order' => 100],
            ['iso2' => 'RS', 'name_en' => 'Serbia', 'name_ar' => 'صربيا', 'phone_code' => '+381', 'currency_code' => 'RSD', 'flag_emoji' => '🇷🇸', 'sort_order' => 100],
            ['iso2' => 'SK', 'name_en' => 'Slovakia', 'name_ar' => 'سلوفاكيا', 'phone_code' => '+421', 'currency_code' => 'EUR', 'flag_emoji' => '🇸🇰', 'sort_order' => 100],
            ['iso2' => 'SI', 'name_en' => 'Slovenia', 'name_ar' => 'سلوفينيا', 'phone_code' => '+386', 'currency_code' => 'EUR', 'flag_emoji' => '🇸🇮', 'sort_order' => 100],
            ['iso2' => 'TW', 'name_en' => 'Taiwan', 'name_ar' => 'تايوان', 'phone_code' => '+886', 'currency_code' => 'TWD', 'flag_emoji' => '🇹🇼', 'sort_order' => 100],
            ['iso2' => 'UY', 'name_en' => 'Uruguay', 'name_ar' => 'أوروغواي', 'phone_code' => '+598', 'currency_code' => 'UYU', 'flag_emoji' => '🇺🇾', 'sort_order' => 100],
            ['iso2' => 'UZ', 'name_en' => 'Uzbekistan', 'name_ar' => 'أوزبكستان', 'phone_code' => '+998', 'currency_code' => 'UZS', 'flag_emoji' => '🇺🇿', 'sort_order' => 100],
        ];

        $now = now();
        foreach ($countries as $c) {
            $existing = DB::table('countries')->where('iso2', $c['iso2'])->first();
            if ($existing) {
                DB::table('countries')->where('id', $existing->id)->update([
                    'name_en' => $c['name_en'],
                    'name_ar' => $c['name_ar'],
                    'phone_code' => $c['phone_code'],
                    'currency_code' => $c['currency_code'] ?? $existing->currency_code,
                    'flag_emoji' => $c['flag_emoji'] ?? $existing->flag_emoji,
                    'sort_order' => $c['sort_order'] ?? 100,
                    'is_active' => true,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('countries')->insert(array_merge($c, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }
}
