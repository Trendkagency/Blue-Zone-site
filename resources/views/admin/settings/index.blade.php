<x-layouts.admin 
    :pageTitle="__('admin.settings.title')" 
    :pageSubtitle="__('admin.settings.subtitle')"
    :breadcrumbs="[__('admin.menu.settings') => route('admin.settings.index')]"
>
    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--color-success); color: var(--color-success); padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid fa-circle-check text-success"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
        @csrf

        <x-slot name="actions">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('admin.settings.save_settings') }}
            </button>
        </x-slot>

        <!-- Settings Tabs Navigation -->
        <div class="product-tabs-nav" data-tab-group="admin-settings" style="margin-bottom: 2rem;">
            <button type="button" class="tab-btn active" data-tab-target="tab-general">
                <i class="fa-solid fa-sliders mr-1.5 ml-1.5"></i> <span>{{ __('admin.settings.tabs.general') }}</span>
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-landing">
                <i class="fa-solid fa-globe mr-1.5 ml-1.5"></i> <span>{{ __('admin.settings.tabs.landing') }}</span>
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-commerce">
                <i class="fa-solid fa-credit-card mr-1.5 ml-1.5"></i> <span>{{ __('admin.settings.tabs.commerce') }}</span>
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-store">
                <i class="fa-solid fa-store mr-1.5 ml-1.5"></i> <span>{{ __('admin.settings.tabs.store') }}</span>
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-shipping">
                <i class="fa-solid fa-truck-fast mr-1.5 ml-1.5"></i> <span>{{ __('admin.settings.tabs.shipping') }}</span>
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-typography">
                <i class="fa-solid fa-font mr-1.5 ml-1.5"></i> <span>{{ __('admin.settings.tabs.typography') }}</span>
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-alerts">
                <i class="fa-solid fa-volume-high mr-1.5 ml-1.5"></i> <span>{{ __('admin.settings.tabs.alerts') }}</span>
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-fcm">
                <i class="fa-solid fa-satellite-dish mr-1.5 ml-1.5"></i> <span>{{ __('admin.settings.tabs.fcm') }}</span>
            </button>
        </div>



        <!-- Tab 1: General -->
        <div id="tab-general" data-tab-content="admin-settings" style="display: block;">
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    {{ __('admin.settings.sections.general_brand') }}
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <x-forms.input name="site_name" :label="__('admin.settings.fields.site_name')" :value="$settings['site_name'] ?? $settings['store_name'] ?? 'BLUE ZONE™ Longevity & Cellular Health'" required />
                    <x-forms.input name="tagline" :label="__('admin.settings.fields.tagline')" :value="$settings['tagline'] ?? 'Cellular Longevity & Botanical Medicine'" required />
                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-top: 1rem;">
                    <x-forms.select 
                        name="default_language" 
                        :label="__('admin.settings.fields.default_language')" 
                        :selected="$settings['default_language'] ?? $settings['default_locale'] ?? 'en'"
                        :options="['en' => 'English (LTR)', 'ar' => 'العربية (RTL)']" 
                    />
                    <x-forms.input name="timezone" :label="__('admin.settings.fields.timezone')" :value="$settings['timezone'] ?? 'Asia/Riyadh'" required />
                </div>

                <!-- Dynamic Currency & Pricing Engine Section -->
                <div id="section-setting-currency" style="border-top: 1px solid var(--color-border); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <h4 style="font-size: 1rem; font-weight: 800; margin: 0; color: var(--color-text);">
                                <i class="fa-solid fa-coins text-primary mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'إعدادات العملة والأسعار الديناميكية (Currency Settings)' : 'Dynamic Currency & Pricing Settings' }}
                            </h4>
                            <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem;">
                                {{ app()->getLocale() == 'ar' ? 'تحديد عملة النظام والمتاجر والرموز وموضع العرض عبر واجهات المتجر والسلة والطلبات ونقاط البيع.' : 'Configure active storefront currency, localized symbols, and display positions across catalog, cart, checkout, and invoices.' }}
                            </p>
                        </div>
                        <span class="badge badge-primary font-mono text-xs px-2.5 py-1">
                            {{ app()->getLocale() == 'ar' ? 'مزامنة ديناميكية 100%' : '100% Dynamic Sync' }}
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                        <x-forms.select 
                            id="currency_code_select"
                            name="currency" 
                            :label="__('admin.settings.fields.currency')" 
                            :selected="$settings['currency'] ?? $settings['default_currency'] ?? 'SAR'"
                            :options="[
                                'SAR' => 'SAR - Saudi Riyal (ر.س)',
                                'USD' => 'USD - US Dollar ($)',
                                'AED' => 'AED - UAE Dirham (د.إ)',
                                'EUR' => 'EUR - Euro (€)',
                                'GBP' => 'GBP - British Pound (£)',
                                'KWD' => 'KWD - Kuwaiti Dinar (د.ك)',
                                'QAR' => 'QAR - Qatari Riyal (ر.ق)',
                                'BHD' => 'BHD - Bahraini Dinar (د.ب)',
                                'OMR' => 'OMR - Omani Rial (ر.ع)',
                                'EGP' => 'EGP - Egyptian Pound (ج.م)',
                            ]"
                            required 
                        />

                        <x-forms.select 
                            id="currency_position_select"
                            name="currency_position" 
                            :label="__('admin.settings.fields.currency_position')" 
                            :selected="$settings['currency_position'] ?? 'auto'"
                            :options="[
                                'auto' => app()->getLocale() == 'ar' ? 'تلقائي (حسب المعايير القياسية للعملة)' : 'Auto (Standard Convention)',
                                'after' => app()->getLocale() == 'ar' ? 'بعد المبلغ (مثال: 150.00 ر.س)' : 'After Amount (e.g. 150.00 SAR)',
                                'before' => app()->getLocale() == 'ar' ? 'قبل المبلغ (مثال: $150.00)' : 'Before Amount (e.g. $150.00)',
                            ]"
                        />

                        <x-forms.select 
                            id="currency_decimals_select"
                            name="currency_decimals" 
                            :label="__('admin.settings.fields.currency_decimals')" 
                            :selected="$settings['currency_decimals'] ?? 2"
                            :options="[
                                '2' => '2 Decimals (150.00)',
                                '0' => '0 Decimals (150)',
                                '3' => '3 Decimals (150.000)',
                            ]"
                        />

                        <x-forms.input 
                            id="currency_symbol_override"
                            name="currency_symbol" 
                            :label="__('admin.settings.fields.currency_symbol')" 
                            :value="$settings['currency_symbol'] ?? ''" 
                            placeholder="{{ app()->getLocale() == 'ar' ? 'اختياري (اتركه فارغاً للاستخدام التلقائي)' : 'Optional (leave blank for automatic)' }}"
                        />
                    </div>

                    <!-- Real-Time Interactive Live Preview Card -->
                    <div style="margin-top: 1rem; padding: 1rem 1.25rem; background: var(--color-surface-hover, rgba(10, 79, 120, 0.04)); border: 1px dashed var(--color-border); border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(10,79,120,0.1); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                                    {{ app()->getLocale() == 'ar' ? 'معاينة حية لشكل السعر في المتجر' : 'Live Price Format Preview' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                                    {{ app()->getLocale() == 'ar' ? 'عينة سعر: 245.50' : 'Sample amount: 245.50' }}
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                            <div style="text-align: center;">
                                <span style="font-size: 0.7rem; color: var(--color-text-muted); display: block;">{{ app()->getLocale() == 'ar' ? 'بالعربية' : 'Arabic View' }}</span>
                                <span id="preview_currency_ar" style="font-size: 1.15rem; font-weight: 800; color: #0A4F78; font-family: monospace;">
                                    {{ \App\Services\CurrencyService::format(245.50, null, 'ar') }}
                                </span>
                            </div>
                            <div style="height: 30px; width: 1px; background: var(--color-border);"></div>
                            <div style="text-align: center;">
                                <span style="font-size: 0.7rem; color: var(--color-text-muted); display: block;">{{ app()->getLocale() == 'ar' ? 'بالإنجليزية' : 'English View' }}</span>
                                <span id="preview_currency_en" style="font-size: 1.15rem; font-weight: 800; color: #0A4F78; font-family: monospace;">
                                    {{ \App\Services\CurrencyService::format(245.50, null, 'en') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-top: 1rem;">
                    <x-forms.input name="contact_email" :label="__('admin.settings.fields.contact_email')" :value="$settings['contact_email'] ?? $settings['support_email'] ?? 'care@bluezone.com'" required />
                    <x-forms.input name="contact_phone" :label="__('admin.settings.fields.contact_phone')" :value="$settings['contact_phone'] ?? $settings['support_phone'] ?? '+966 800 123 4567'" required />
                </div>

                <!-- WhatsApp Live Support Configuration -->
                <div style="border-top: 1px solid var(--color-border); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <h4 style="font-size: 1rem; font-weight: 800; margin: 0; color: var(--color-text);">
                                <i class="fa-brands fa-whatsapp text-success mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'أيقونة الواتساب العائمة للمتجر (Floating WhatsApp Widget)' : 'Storefront Floating WhatsApp Widget' }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'إظهار أو إخفاء أيقونة واتساب العائمة في المتجر مع تخصيص الرقم والرسالة الترحيبية.' : 'Enable or disable the floating WhatsApp button on the storefront with custom number and prefilled greeting.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="enable_whatsapp" 
                            :label="app()->getLocale() == 'ar' ? 'تفعيل ظهور الواتساب' : 'Enable WhatsApp Widget'" 
                            :checked="$settings['enable_whatsapp'] ?? true" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                        <x-forms.select 
                            name="whatsapp_position" 
                            :label="app()->getLocale() == 'ar' ? 'موضع الأيقونة (Position & RTL/LTR)' : 'Widget Position & Alignment'" 
                            :selected="$settings['whatsapp_position'] ?? 'auto'"
                            :options="[
                                'auto' => app()->getLocale() == 'ar' ? 'تلقائي (يمين بالإنجليزي / يسار بالعربي)' : 'Auto (LTR: Right / RTL: Left)',
                                'bottom_right' => app()->getLocale() == 'ar' ? 'أسفل اليمين دائماً (Bottom-Right)' : 'Always Bottom-Right',
                                'bottom_left' => app()->getLocale() == 'ar' ? 'أسفل اليسار دائماً (Bottom-Left)' : 'Always Bottom-Left',
                            ]" 
                        />
                        <x-forms.input 
                            name="whatsapp_number" 
                            :label="app()->getLocale() == 'ar' ? 'رقم الواتساب (بالرمز الدولي)' : 'WhatsApp Phone Number (with Country Code)'" 
                            :value="$settings['whatsapp_number'] ?? '+966501234567'" 
                            placeholder="+966501234567"
                        />
                        <x-forms.input 
                            name="whatsapp_default_message" 
                            :label="app()->getLocale() == 'ar' ? 'الرسالة الترحيبية المسبقة' : 'Default Consultation Message'" 
                            :value="$settings['whatsapp_default_message'] ?? 'Hello BLUE ZONE, I would like clinical guidance on longevity formulations.'" 
                        />
                    </div>
                </div>

                <!-- Storefront Footer Social Media Channels & Links -->
                <div id="section-setting-social" style="border-top: 1px solid var(--color-border); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                        <div>
                            <h4 style="font-size: 1rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <span style="width: 28px; height: 28px; border-radius: 8px; background: rgba(10,79,120,0.12); color: var(--color-primary); display: inline-flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    <i class="fa-solid fa-share-nodes"></i>
                                </span>
                                {{ app()->getLocale() == 'ar' ? 'روابط مواقع التواصل الاجتماعي في الفوتر (Storefront Footer Social Links)' : 'Storefront Footer Social Media Links' }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.35rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'تحكم كامل بروابط التواصل في فوتر المتجر. أي منصة تضع رابطها ستظهر تلقائياً بأيقونتها الرسمية، والحقول المتروكة فارغة لن تظهر نهائياً.' : 'Full control over footer social channels. Any platform with a URL will dynamically appear in the footer with its brand icon. Blank fields remain hidden.' }}
                            </p>
                        </div>
                        <span class="badge badge-primary font-mono text-xs px-2.5 py-1" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-bolt text-warning"></i>
                            {{ app()->getLocale() == 'ar' ? 'تحكم ديناميكي 100%' : '100% Dynamic' }}
                        </span>
                    </div>

                    <!-- Interactive Live Preview Strip -->
                    <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: var(--color-surface-hover, rgba(10, 79, 120, 0.04)); border: 1px dashed var(--color-border); border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(103,179,74,0.15); color: #67B34A; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                                    {{ app()->getLocale() == 'ar' ? 'معاينة حية لأيقونات الفوتر النشطة' : 'Live Footer Icons Preview' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                                    {{ app()->getLocale() == 'ar' ? 'الأيقونات المضاءة هي ما سيظهر لزوار المتجر' : 'Highlighted icons are currently active on the storefront' }}
                                </div>
                            </div>
                        </div>

                        <div id="social-live-preview" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            @php
                                $previewList = [
                                    'social_instagram' => ['icon' => 'fa-brands fa-instagram', 'name' => 'Instagram', 'color' => '#E1306C'],
                                    'social_x' => ['icon' => 'fa-brands fa-x-twitter', 'name' => 'X', 'color' => '#111111'],
                                    'social_facebook' => ['icon' => 'fa-brands fa-facebook-f', 'name' => 'Facebook', 'color' => '#1877F2'],
                                    'social_linkedin' => ['icon' => 'fa-brands fa-linkedin-in', 'name' => 'LinkedIn', 'color' => '#0A66C2'],
                                    'social_youtube' => ['icon' => 'fa-brands fa-youtube', 'name' => 'YouTube', 'color' => '#FF0000'],
                                    'social_tiktok' => ['icon' => 'fa-brands fa-tiktok', 'name' => 'TikTok', 'color' => '#111111'],
                                    'social_snapchat' => ['icon' => 'fa-brands fa-snapchat', 'name' => 'Snapchat', 'color' => '#EAA300'],
                                    'social_telegram' => ['icon' => 'fa-brands fa-telegram', 'name' => 'Telegram', 'color' => '#24A1DE'],
                                    'social_whatsapp' => ['icon' => 'fa-brands fa-whatsapp', 'name' => 'WhatsApp', 'color' => '#25D366'],
                                    'social_pinterest' => ['icon' => 'fa-brands fa-pinterest-p', 'name' => 'Pinterest', 'color' => '#BD081C'],
                                ];
                            @endphp
                            @foreach($previewList as $sKey => $sMeta)
                                @php $hasVal = !empty(trim($settings[$sKey] ?? '')); @endphp
                                <span id="badge_{{ $sKey }}" 
                                      title="{{ $sMeta['name'] }}"
                                      data-active-color="{{ $sMeta['color'] }}"
                                      style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem; transition: all 0.25s; {{ $hasVal ? 'background: #031827; color: '.$sMeta['color'].'; border: 1px solid rgba(10,79,120,0.5); opacity: 1; transform: scale(1);' : 'background: rgba(0,0,0,0.05); color: #94A3B8; border: 1px dashed rgba(0,0,0,0.15); opacity: 0.45;' }}">
                                    <i class="{{ $sMeta['icon'] }}"></i>
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Social Inputs Grid -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                        <!-- Instagram -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-instagram" style="color: #E1306C; font-size: 1.15rem;"></i>
                                <span>Instagram</span>
                            </label>
                            <input type="url" name="social_instagram" id="input_social_instagram"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_instagram"
                                   value="{{ $settings['social_instagram'] ?? '' }}"
                                   placeholder="https://instagram.com/bluezone"
                                   dir="ltr" />
                        </div>

                        <!-- X / Twitter -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-x-twitter" style="color: #111111; font-size: 1.15rem;"></i>
                                <span>X (Twitter)</span>
                            </label>
                            <input type="url" name="social_x" id="input_social_x"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_x"
                                   value="{{ $settings['social_x'] ?? '' }}"
                                   placeholder="https://x.com/bluezone"
                                   dir="ltr" />
                        </div>

                        <!-- Facebook -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-facebook-f" style="color: #1877F2; font-size: 1.15rem;"></i>
                                <span>Facebook</span>
                            </label>
                            <input type="url" name="social_facebook" id="input_social_facebook"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_facebook"
                                   value="{{ $settings['social_facebook'] ?? '' }}"
                                   placeholder="https://facebook.com/bluezone"
                                   dir="ltr" />
                        </div>

                        <!-- LinkedIn -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-linkedin-in" style="color: #0A66C2; font-size: 1.15rem;"></i>
                                <span>LinkedIn</span>
                            </label>
                            <input type="url" name="social_linkedin" id="input_social_linkedin"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_linkedin"
                                   value="{{ $settings['social_linkedin'] ?? '' }}"
                                   placeholder="https://linkedin.com/company/bluezone"
                                   dir="ltr" />
                        </div>

                        <!-- YouTube -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-youtube" style="color: #FF0000; font-size: 1.15rem;"></i>
                                <span>YouTube</span>
                            </label>
                            <input type="url" name="social_youtube" id="input_social_youtube"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_youtube"
                                   value="{{ $settings['social_youtube'] ?? '' }}"
                                   placeholder="https://youtube.com/@bluezone"
                                   dir="ltr" />
                        </div>

                        <!-- TikTok -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-tiktok" style="color: #111111; font-size: 1.15rem;"></i>
                                <span>TikTok</span>
                            </label>
                            <input type="url" name="social_tiktok" id="input_social_tiktok"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_tiktok"
                                   value="{{ $settings['social_tiktok'] ?? '' }}"
                                   placeholder="https://tiktok.com/@bluezone"
                                   dir="ltr" />
                        </div>

                        <!-- Snapchat -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-snapchat" style="color: #EAA300; font-size: 1.15rem;"></i>
                                <span>Snapchat</span>
                            </label>
                            <input type="url" name="social_snapchat" id="input_social_snapchat"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_snapchat"
                                   value="{{ $settings['social_snapchat'] ?? '' }}"
                                   placeholder="https://snapchat.com/add/bluezone"
                                   dir="ltr" />
                        </div>

                        <!-- Telegram -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-telegram" style="color: #24A1DE; font-size: 1.15rem;"></i>
                                <span>Telegram</span>
                            </label>
                            <input type="url" name="social_telegram" id="input_social_telegram"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_telegram"
                                   value="{{ $settings['social_telegram'] ?? '' }}"
                                   placeholder="https://t.me/bluezone"
                                   dir="ltr" />
                        </div>

                        <!-- WhatsApp Channel / Group -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-whatsapp" style="color: #25D366; font-size: 1.15rem;"></i>
                                <span>WhatsApp Channel / Group</span>
                            </label>
                            <input type="url" name="social_whatsapp" id="input_social_whatsapp"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_whatsapp"
                                   value="{{ $settings['social_whatsapp'] ?? '' }}"
                                   placeholder="https://whatsapp.com/channel/... or https://wa.me/..."
                                   dir="ltr" />
                        </div>

                        <!-- Pinterest -->
                        <div>
                            <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem;">
                                <i class="fa-brands fa-pinterest-p" style="color: #BD081C; font-size: 1.15rem;"></i>
                                <span>Pinterest</span>
                            </label>
                            <input type="url" name="social_pinterest" id="input_social_pinterest"
                                   class="form-control social-setting-input"
                                   data-target="badge_social_pinterest"
                                   value="{{ $settings['social_pinterest'] ?? '' }}"
                                   placeholder="https://pinterest.com/bluezone"
                                   dir="ltr" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Landing Page & Storefront Configuration -->
        <div id="tab-landing" data-tab-content="admin-settings" style="display: none;">
            <!-- Master Announcement & Live Storefront Action Bar -->
            <div class="card" style="padding: 1.5rem 2rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(10, 79, 120, 0.08) 0%, rgba(103, 179, 74, 0.08) 100%); border: 1px solid rgba(10, 79, 120, 0.2); border-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                <div>
                    <span style="display: inline-block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-primary); background: rgba(10, 79, 120, 0.12); padding: 0.25rem 0.75rem; border-radius: 9999px; margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-sliders mr-1 ml-1"></i> {{ __('admin.settings.landing.overview') }}
                    </span>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--color-text);">
                        {{ __('admin.settings.sections.landing_page') }}
                    </h3>
                    <p style="font-size: 0.875rem; color: var(--color-text-muted); margin: 0.35rem 0 0 0; max-width: 680px;">
                        {{ __('admin.settings.landing.overview_desc') }}
                    </p>
                </div>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <a href="{{ route('customer.home') }}" target="_blank" class="btn btn-outline" style="border-color: var(--color-primary); color: var(--color-primary); font-weight: 700;">
                        <i class="fa-solid fa-arrow-up-right-from-square mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'معاينة الواجهة المباشرة' : 'Live Storefront Preview' }}
                    </a>
                </div>
            </div>

            <!-- 0. INTERACTIVE SECTION REORDERING & MASTER ON/OFF MANAGER -->
            <div class="card landing-builder-card" style="padding: 2rem; margin-bottom: 2rem; border: 2px solid rgba(10, 79, 120, 0.2); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border-radius: var(--radius-xl);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--color-border); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <span class="badge" style="background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: white; font-weight: 800; font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 9999px;">
                                <i class="fa-solid fa-layer-group"></i> {{ app()->getLocale() == 'ar' ? 'التحكم الإداري الحصري' : 'Admin Exclusive Control' }}
                            </span>
                            <span id="active_sections_count_badge" class="badge" style="background: rgba(103, 179, 74, 0.15); color: #67B34A; font-weight: 800; font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 9999px; border: 1px solid rgba(103, 179, 74, 0.3);">
                                12 / 12 {{ app()->getLocale() == 'ar' ? 'أقسام مفعّلة' : 'Sections Active' }}
                            </span>
                        </div>
                        <h3 style="font-size: 1.35rem; font-weight: 900; margin: 0; color: var(--color-text);">
                            <i class="fa-solid fa-sliders text-primary mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'مصمم وترتيب وتفعيل أقسام الصفحة الرئيسية' : 'Landing Page Sections Ordering & Master Visibility Builder' }}
                        </h3>
                        <p style="font-size: 0.875rem; color: var(--color-text-muted); margin: 0.35rem 0 0 0;">
                            {{ app()->getLocale() == 'ar' 
                                ? 'تحكّم في إظهار أو إخفاء أي قسم من أقسام الصفحة الرئيسية بنقرة زر، وأعد ترتيب ظهور الأقسام بالسحب والإفلات أو أزرار الأسهم لأعلى ولأسفل.' 
                                : 'Control On/Off visibility for each section and reorder live display sequence effortlessly using drag-and-drop or up/down controls.' }}
                        </p>
                    </div>

                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                        <button type="button" class="btn btn-sm btn-outline" onclick="window.enableAllLandingSections(true)" style="font-weight: 700;">
                            <i class="fa-solid fa-eye mr-1 ml-1 text-success"></i> {{ app()->getLocale() == 'ar' ? 'تفعيل الكل' : 'Enable All' }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="window.enableAllLandingSections(false)" style="font-weight: 700;">
                            <i class="fa-solid fa-eye-slash mr-1 ml-1 text-danger"></i> {{ app()->getLocale() == 'ar' ? 'تعطيل الكل' : 'Disable All' }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="window.resetLandingSectionsOrder()" style="font-weight: 700; border-color: var(--color-border);">
                            <i class="fa-solid fa-rotate-left mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'الترتيب الافتراضي' : 'Reset Order' }}
                        </button>
                    </div>
                </div>

                <!-- Hidden Inputs for Form Submission -->
                <input type="hidden" name="landing_sections_order" id="landing_sections_order_input" value="{{ json_encode($landingSectionsOrder) }}">
                <input type="hidden" name="landing_sections_builder_submitted" value="1">

                <!-- Draggable Sortable List -->
                <div id="landing_sections_sortable_container" class="landing-sections-sortable-list" style="display: flex; flex-direction: column; gap: 0.85rem;">
                    @foreach($landingSectionsOrder as $idx => $sKey)
                        @php
                            $meta = $landingSectionsMeta[$sKey] ?? [
                                'name_en' => ucfirst(str_replace('_', ' ', $sKey)),
                                'name_ar' => $sKey,
                                'icon' => 'fa-solid fa-cube',
                                'desc_en' => 'Section component',
                                'desc_ar' => 'قسم المحتوى',
                            ];
                            $isEnabled = $settings['landing_' . $sKey . '_enabled'] ?? true;
                            if (is_string($isEnabled)) {
                                $isEnabled = filter_var($isEnabled, FILTER_VALIDATE_BOOLEAN);
                            }
                        @endphp
                        <div class="landing-section-row {{ $isEnabled ? 'is-active' : 'is-inactive' }}" 
                             data-section-key="{{ $sKey }}" 
                             draggable="true" 
                             style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: var(--color-bg); border: 1.5px solid {{ $isEnabled ? 'rgba(10, 79, 120, 0.2)' : 'var(--color-border)' }}; border-radius: var(--radius-lg); transition: all 0.25s ease; box-shadow: {{ $isEnabled ? '0 4px 12px rgba(0,0,0,0.03)' : 'none' }}; opacity: {{ $isEnabled ? '1' : '0.65' }};">
                             
                            <!-- Drag Handle + Position + Icon + Names -->
                            <div style="display: flex; align-items: center; gap: 1rem; flex: 1; min-width: 0;">
                                <div class="drag-handle" style="cursor: grab; color: var(--color-text-muted); padding: 0.5rem 0.25rem; font-size: 1.1rem;" title="{{ app()->getLocale() == 'ar' ? 'اسحب لإعادة الترتيب' : 'Drag to reorder' }}">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>

                                <div class="section-order-badge" style="width: 2.25rem; height: 2.25rem; border-radius: 50%; background: {{ $isEnabled ? 'linear-gradient(135deg, #0A4F78, #2A8FC2)' : 'var(--color-border)' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.875rem; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <span class="order-num">{{ $idx + 1 }}</span>
                                </div>

                                <div style="width: 2.5rem; height: 2.5rem; border-radius: var(--radius-md); background: rgba(10, 79, 120, 0.08); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                                    <i class="{{ $meta['icon'] ?? 'fa-solid fa-cube' }}"></i>
                                </div>

                                <div style="min-width: 0; flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                        <h4 style="font-size: 0.95rem; font-weight: 800; margin: 0; color: var(--color-text);">
                                            {{ app()->getLocale() == 'ar' ? $meta['name_ar'] : $meta['name_en'] }}
                                        </h4>
                                        <span style="font-family: monospace; font-size: 0.7rem; font-weight: 700; color: var(--color-primary); background: rgba(10, 79, 120, 0.08); padding: 0.15rem 0.5rem; border-radius: 4px;">
                                            #{{ $sKey }}
                                        </span>
                                        @if(app()->getLocale() == 'ar')
                                            <span style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 500;">
                                                ({{ $meta['name_en'] }})
                                            </span>
                                        @else
                                            <span style="font-size: 0.8rem; color: var(--color-text-muted); font-weight: 500;">
                                                ({{ $meta['name_ar'] }})
                                            </span>
                                        @endif
                                    </div>
                                    <p style="font-size: 0.8rem; color: var(--color-text-muted); margin: 0.2rem 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 600px;">
                                        {{ app()->getLocale() == 'ar' ? $meta['desc_ar'] : $meta['desc_en'] }}
                                    </p>
                                </div>
                            </div>

                            <!-- Up / Down Actions + Master Toggle -->
                            <div style="display: flex; align-items: center; gap: 1rem; flex-shrink: 0;">
                                <!-- Up / Down Buttons -->
                                <div style="display: flex; gap: 0.25rem;">
                                    <button type="button" class="btn-order-up btn btn-sm btn-icon" onclick="window.moveLandingSectionRow(this, -1)" title="{{ app()->getLocale() == 'ar' ? 'تحريك لأعلى' : 'Move Up' }}" style="width: 2rem; height: 2rem; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-bg-alt); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fa-solid fa-arrow-up" style="font-size: 0.8rem;"></i>
                                    </button>
                                    <button type="button" class="btn-order-down btn btn-sm btn-icon" onclick="window.moveLandingSectionRow(this, 1)" title="{{ app()->getLocale() == 'ar' ? 'تحريك لأسفل' : 'Move Down' }}" style="width: 2rem; height: 2rem; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-bg-alt); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fa-solid fa-arrow-down" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>

                                <!-- Master ON / OFF Toggle -->
                                <div style="display: flex; align-items: center; gap: 0.5rem; background: var(--color-bg-alt); padding: 0.35rem 0.75rem; border-radius: 9999px; border: 1px solid var(--color-border);">
                                    <span class="status-indicator-text" style="font-size: 0.75rem; font-weight: 800; color: {{ $isEnabled ? '#10B981' : 'var(--color-text-muted)' }};">
                                        {{ $isEnabled ? (app()->getLocale() == 'ar' ? 'مفعّل (ON)' : 'ON / Visible') : (app()->getLocale() == 'ar' ? 'معطّل (OFF)' : 'OFF / Hidden') }}
                                    </span>
                                    <label class="switch-toggle" style="position: relative; display: inline-block; width: 42px; height: 22px; margin: 0; cursor: pointer;">
                                        <input type="checkbox" 
                                               name="landing_{{ $sKey }}_enabled" 
                                               value="1" 
                                               class="landing-section-toggle-input" 
                                               onchange="window.handleLandingSectionToggle(this)"
                                               {{ $isEnabled ? 'checked' : '' }} 
                                               style="opacity: 0; width: 0; height: 0;">
                                        <span class="slider-round" style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $isEnabled ? '#10B981' : '#cbd5e1' }}; transition: .3s; border-radius: 34px;">
                                            <span class="slider-dot" style="position: absolute; height: 16px; width: 16px; left: {{ $isEnabled ? '23px' : '3px' }}; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.3);"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- 1. Top Announcement Bar -->
                <div class="card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-bullhorn text-primary"></i> {{ __('admin.settings.landing.announcement') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'الشريط العلوي للشحن المبرد المجاني والتنبيهات السريرية العالمية.' : 'Top bar for global cold-chain shipping incentives and clinical research alerts.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="landing_announcement_enabled" 
                            :label="__('admin.settings.landing.enable_section')" 
                            :checked="$settings['landing_announcement_enabled'] ?? true" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input 
                            name="landing_announcement_badge_en" 
                            :label="__('admin.settings.landing.badge_en')" 
                            :value="$settings['landing_announcement_badge_en'] ?? 'GLOBAL CLINICAL EXPEDITION'" 
                        />
                        <x-forms.input 
                            name="landing_announcement_badge_ar" 
                            :label="__('admin.settings.landing.badge_ar')" 
                            :value="$settings['landing_announcement_badge_ar'] ?? 'بعثة الأبحاث السريرية العالمية'" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input 
                            name="landing_announcement_text_en" 
                            :label="__('admin.settings.landing.subtitle_en')" 
                            :value="$settings['landing_announcement_text_en'] ?? 'Complimentary worldwide cold-chain shipping on all longevity orders over $75'" 
                        />
                        <x-forms.input 
                            name="landing_announcement_text_ar" 
                            :label="__('admin.settings.landing.subtitle_ar')" 
                            :value="$settings['landing_announcement_text_ar'] ?? 'شحن مبرد مجاني لجميع طلبات تعزيز طول العمر التي تتجاوز 75 دولاراً'" 
                        />
                    </div>

                    <div>
                        <x-forms.input 
                            name="landing_announcement_link" 
                            :label="__('admin.settings.landing.cta_primary_link')" 
                            :value="$settings['landing_announcement_link'] ?? '/shop'" 
                            placeholder="/shop"
                        />
                    </div>
                </div>

                <!-- 2. Hero Main Showcase & CTAs -->
                <div class="card" style="padding: 2rem;">
                    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                        <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-wand-magic-sparkles text-primary"></i> {{ __('admin.settings.landing.hero') }}
                        </h4>
                        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                            {{ app()->getLocale() == 'ar' ? 'العنوان البصري الرئيسي، الشارات، وأزرار التوجيه المزدوجة لاكتشاف المستحضرات والأبحاث.' : 'Main visual headlines, clinical badges, and dual call-to-action buttons.' }}
                        </p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input 
                            name="landing_hero_badge_en" 
                            :label="__('admin.settings.landing.badge_en')" 
                            :value="$settings['landing_hero_badge_en'] ?? 'CENTENARIAN WISDOM & CELLULAR MEDICINE'" 
                        />
                        <x-forms.input 
                            name="landing_hero_badge_ar" 
                            :label="__('admin.settings.landing.badge_ar')" 
                            :value="$settings['landing_hero_badge_ar'] ?? 'حكمة المعمرين والطب الخلوي المتقدم'" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input 
                            name="landing_hero_title_en" 
                            :label="__('admin.settings.landing.title_en')" 
                            :value="$settings['landing_hero_title_en'] ?? 'LIVE LONG. LIVE WELL.'" 
                        />
                        <x-forms.input 
                            name="landing_hero_title_ar" 
                            :label="__('admin.settings.landing.title_ar')" 
                            :value="$settings['landing_hero_title_ar'] ?? 'عش أطول. عش بحيوية فائقة.'" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <x-forms.textarea 
                            name="landing_hero_subtitle_en" 
                            :label="__('admin.settings.landing.subtitle_en')" 
                            rows="3"
                        >{{ $settings['landing_hero_subtitle_en'] ?? 'Translating the lifestyle, diet, and biological resilience of the world’s 5 longest-lived communities into modern wellness formulations.' }}</x-forms.textarea>

                        <x-forms.textarea 
                            name="landing_hero_subtitle_ar" 
                            :label="__('admin.settings.landing.subtitle_ar')" 
                            rows="3"
                        >{{ $settings['landing_hero_subtitle_ar'] ?? 'ترجمة أسلوب الحياة والتغذية والمرونة البيولوجية لأطول 5 مجتمعات عمراً في العالم إلى تركيبات وقائية متطورة.' }}</x-forms.textarea>
                    </div>

                    <!-- Hero Action Buttons -->
                    <div style="background: rgba(0,0,0,0.02); border: 1px solid var(--color-border); padding: 1.5rem; border-radius: var(--radius-md);">
                        <h5 style="font-size: 0.95rem; font-weight: 800; margin-bottom: 1rem; color: var(--color-text);">
                            <i class="fa-solid fa-link mr-1 ml-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'أزرار الحث على اتخاذ إجراء (Dual CTAs)' : 'Dual Call-to-Action Buttons' }}
                        </h5>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <x-forms.input 
                                name="landing_hero_cta_primary_text_en" 
                                :label="__('admin.settings.landing.cta_primary_en')" 
                                :value="$settings['landing_hero_cta_primary_text_en'] ?? 'DISCOVER OUR STORY'" 
                            />
                            <x-forms.input 
                                name="landing_hero_cta_primary_text_ar" 
                                :label="__('admin.settings.landing.cta_primary_ar')" 
                                :value="$settings['landing_hero_cta_primary_text_ar'] ?? 'اكتشف قصتنا وأبحاثنا'" 
                            />
                            <x-forms.input 
                                name="landing_hero_cta_primary_link" 
                                :label="__('admin.settings.landing.cta_primary_link')" 
                                :value="$settings['landing_hero_cta_primary_link'] ?? '#who-we-are'" 
                            />
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <x-forms.input 
                                name="landing_hero_cta_secondary_text_en" 
                                :label="__('admin.settings.landing.cta_secondary_en')" 
                                :value="$settings['landing_hero_cta_secondary_text_en'] ?? 'EXPLORE FORMULATIONS'" 
                            />
                            <x-forms.input 
                                name="landing_hero_cta_secondary_text_ar" 
                                :label="__('admin.settings.landing.cta_secondary_ar')" 
                                :value="$settings['landing_hero_cta_secondary_text_ar'] ?? 'استكشف المستحضرات الطبية'" 
                            />
                            <x-forms.input 
                                name="landing_hero_cta_secondary_link" 
                                :label="__('admin.settings.landing.cta_secondary_link')" 
                                :value="$settings['landing_hero_cta_secondary_link'] ?? '/shop'" 
                            />
                        </div>
                    </div>
                </div>

                <!-- 3. Clinical Trust & Active Purity Stats Bar -->
                <div class="card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-chart-line text-success"></i> {{ __('admin.settings.landing.stats') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'مؤشرات النقاء القياسية والأقاليم المعتمدة المعروضة أسفل الواجهة الرئيسية مباشرة.' : 'Purity percentages, verified ecosystem counts, and clinical availability highlights.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="landing_stats_enabled" 
                            :label="__('admin.settings.landing.enable_section')" 
                            :checked="$settings['landing_stats_enabled'] ?? true" 
                        />
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        <!-- Stat 1 -->
                        <div style="display: grid; grid-template-columns: 1fr 2fr 2fr; gap: 1rem; align-items: center; background: rgba(0,0,0,0.02); padding: 1rem; border-radius: var(--radius-md);">
                            <x-forms.input name="landing_stat_1_val" label="Stat 1 Value" :value="$settings['landing_stat_1_val'] ?? '99.8%'" />
                            <x-forms.input name="landing_stat_1_label_en" label="Label (EN)" :value="$settings['landing_stat_1_label_en'] ?? 'Standardized Active Molecular Purity'" />
                            <x-forms.input name="landing_stat_1_label_ar" label="Label (AR)" :value="$settings['landing_stat_1_label_ar'] ?? 'نقاء جزيئي قياسي معتمد للمواد الفعالة'" />
                        </div>

                        <!-- Stat 2 -->
                        <div style="display: grid; grid-template-columns: 1fr 2fr 2fr; gap: 1rem; align-items: center; background: rgba(0,0,0,0.02); padding: 1rem; border-radius: var(--radius-md);">
                            <x-forms.input name="landing_stat_2_val" label="Stat 2 Value" :value="$settings['landing_stat_2_val'] ?? '5 Regions'" />
                            <x-forms.input name="landing_stat_2_label_en" label="Label (EN)" :value="$settings['landing_stat_2_label_en'] ?? 'Blue Zones Validated Longevity Ecosystems'" />
                            <x-forms.input name="landing_stat_2_label_ar" label="Label (AR)" :value="$settings['landing_stat_2_label_ar'] ?? 'أقاليم المناطق الزرقاء الموثقة سريرياً'" />
                        </div>

                        <!-- Stat 3 -->
                        <div style="display: grid; grid-template-columns: 1fr 2fr 2fr; gap: 1rem; align-items: center; background: rgba(0,0,0,0.02); padding: 1rem; border-radius: var(--radius-md);">
                            <x-forms.input name="landing_stat_3_val" label="Stat 3 Value" :value="$settings['landing_stat_3_val'] ?? '100%'" />
                            <x-forms.input name="landing_stat_3_label_en" label="Label (EN)" :value="$settings['landing_stat_3_label_en'] ?? 'Bio-Identical Cellular Bioavailability'" />
                            <x-forms.input name="landing_stat_3_label_ar" label="Label (AR)" :value="$settings['landing_stat_3_label_ar'] ?? 'توافر حيوي خلوي مطابق حيوياً بنسبة 100%'" />
                        </div>

                        <!-- Stat 4 -->
                        <div style="display: grid; grid-template-columns: 1fr 2fr 2fr; gap: 1rem; align-items: center; background: rgba(0,0,0,0.02); padding: 1rem; border-radius: var(--radius-md);">
                            <x-forms.input name="landing_stat_4_val" label="Stat 4 Value" :value="$settings['landing_stat_4_val'] ?? '24/7'" />
                            <x-forms.input name="landing_stat_4_label_en" label="Label (EN)" :value="$settings['landing_stat_4_label_en'] ?? 'Longevity Guidance & Clinical Protocol Advisory'" />
                            <x-forms.input name="landing_stat_4_label_ar" label="Label (AR)" :value="$settings['landing_stat_4_label_ar'] ?? 'إرشاد طبي متخصص واستشارات بروتوكولات طول العمر'" />
                        </div>
                    </div>
                </div>

                <!-- 4. Who We Are & Longevity Philosophy Section -->
                <div class="card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-dna text-primary"></i> {{ __('admin.settings.landing.philosophy') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'سرد قصة العلامة، استكشاف المعمرين، والبيولوجيا الخلوية المتقدمة.' : 'Story of Blue Zone origins, centenarian longevity biology, and formulation philosophy.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="landing_philosophy_enabled" 
                            :label="__('admin.settings.landing.enable_section')" 
                            :checked="$settings['landing_philosophy_enabled'] ?? true" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_philosophy_badge_en" :label="__('admin.settings.landing.badge_en')" :value="$settings['landing_philosophy_badge_en'] ?? 'CENTENARIAN WISDOM'" />
                        <x-forms.input name="landing_philosophy_badge_ar" :label="__('admin.settings.landing.badge_ar')" :value="$settings['landing_philosophy_badge_ar'] ?? 'حكمة المعمرين البيولوجية'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_philosophy_title_en" :label="__('admin.settings.landing.title_en')" :value="$settings['landing_philosophy_title_en'] ?? 'Rooted in Nature. Validated by Modern Cellular Biology.'" />
                        <x-forms.input name="landing_philosophy_title_ar" :label="__('admin.settings.landing.title_ar')" :value="$settings['landing_philosophy_title_ar'] ?? 'متجذرة في الطبيعة، ومثبتة بأحدث علوم البيولوجيا الخلوية.'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <x-forms.textarea name="landing_philosophy_desc_en" :label="__('admin.settings.landing.subtitle_en')" rows="3">{{ $settings['landing_philosophy_desc_en'] ?? 'For over two decades, longevity researchers studied the world’s Blue Zones—remote pockets on Earth where individuals regularly thrive past 100 with extraordinary physical vitality. BLUE ZONE™ was founded to formulate these precise biological mechanisms.' }}</x-forms.textarea>
                        <x-forms.textarea name="landing_philosophy_desc_ar" :label="__('admin.settings.landing.subtitle_ar')" rows="3">{{ $settings['landing_philosophy_desc_ar'] ?? 'على مدار أكثر من عقدين، عكف علماء أبحاث طول العمر على دراسة المناطق الزرقاء، تلك البقاع الفريدة حول العالم التي يتجاوز سكانها سن المائة بحيوية ونشاط استثنائي. تأسست بلو زون™ لترجمة هذه المسارات الحيوية إلى مستحضرات دقيقة.' }}</x-forms.textarea>
                    </div>
                </div>

                <!-- 5. Five Blue Zones Interactive Geographic Section -->
                <div class="card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-earth-americas text-primary"></i> {{ __('admin.settings.landing.zones') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'الخريطة التفاعلية للأقاليم الخمسة: أوكيناوا، سردينيا، نيكويا، إيكاريا، ولوما ليندا.' : 'Interactive ecosystem map covering Okinawa, Sardinia, Nicoya, Ikaria, and Loma Linda.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="landing_zones_enabled" 
                            :label="__('admin.settings.landing.enable_section')" 
                            :checked="$settings['landing_zones_enabled'] ?? true" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_zones_badge_en" :label="__('admin.settings.landing.badge_en')" :value="$settings['landing_zones_badge_en'] ?? 'THE FIVE LONGEVITY ECOSYSTEMS'" />
                        <x-forms.input name="landing_zones_badge_ar" :label="__('admin.settings.landing.badge_ar')" :value="$settings['landing_zones_badge_ar'] ?? 'الأقاليم الخمسة المعمرة حول العالم'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_zones_title_en" :label="__('admin.settings.landing.title_en')" :value="$settings['landing_zones_title_en'] ?? 'Explore the Blueprint of Longevity Across Continents'" />
                        <x-forms.input name="landing_zones_title_ar" :label="__('admin.settings.landing.title_ar')" :value="$settings['landing_zones_title_ar'] ?? 'استكشف خارطة طول العمر والصحة الخلوية عبر القارات'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <x-forms.textarea name="landing_zones_desc_en" :label="__('admin.settings.landing.subtitle_en')" rows="3">{{ $settings['landing_zones_desc_en'] ?? 'From Okinawa’s marine polyphenols to Sardinia’s mountain flavonoids, discover the geographical sources behind our formulations.' }}</x-forms.textarea>
                        <x-forms.textarea name="landing_zones_desc_ar" :label="__('admin.settings.landing.subtitle_ar')" rows="3">{{ $settings['landing_zones_desc_ar'] ?? 'من بوليفينولات أوكيناوا البحرية إلى فلافونويدات جبال سردينيا، اكتشف المصادر الجغرافية الأصيلة وراء تركيباتنا.' }}</x-forms.textarea>
                    </div>
                </div>

                <!-- 6. Featured Clinical Formulations Showcase -->
                <div class="card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-flask-vial text-primary"></i> {{ __('admin.settings.landing.products') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'عرض الكتالوج والمنتجات الأكثر مبيعاً والأحدث بالصفحة الرئيسية مع تحديد الحد الأقصى للمنتجات.' : 'Showcase featured, best-seller, and new formulations on the landing page.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="landing_products_enabled" 
                            :label="__('admin.settings.landing.enable_section')" 
                            :checked="$settings['landing_products_enabled'] ?? true" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_products_badge_en" :label="__('admin.settings.landing.badge_en')" :value="$settings['landing_products_badge_en'] ?? 'CLINICAL FORMULATIONS'" />
                        <x-forms.input name="landing_products_badge_ar" :label="__('admin.settings.landing.badge_ar')" :value="$settings['landing_products_badge_ar'] ?? 'التركيبات الطبية السريرية'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_products_title_en" :label="__('admin.settings.landing.title_en')" :value="$settings['landing_products_title_en'] ?? 'Engineered for Systemic Longevity & Vitality'" />
                        <x-forms.input name="landing_products_title_ar" :label="__('admin.settings.landing.title_ar')" :value="$settings['landing_products_title_ar'] ?? 'مصممة خصيصاً للصحة الخلوية وطول العمر المديد'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.textarea name="landing_products_subtitle_en" :label="__('admin.settings.landing.subtitle_en')" rows="2">{{ $settings['landing_products_subtitle_en'] ?? 'Targeted botanical bio-compounds designed to support cellular repair, cognitive sharpness, and daily metabolic energy.' }}</x-forms.textarea>
                        <x-forms.textarea name="landing_products_subtitle_ar" :label="__('admin.settings.landing.subtitle_ar')" rows="2">{{ $settings['landing_products_subtitle_ar'] ?? 'مركبات نباتية نشطة بيولوجياً تستهدف تحفيز الترميم الخلوي، تعزيز صفاء الذهن، ودعم الطاقة الأيضية اليومية.' }}</x-forms.textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                        <x-forms.input 
                            name="landing_products_limit" 
                            type="number" 
                            min="1" 
                            max="24"
                            :label="__('admin.settings.landing.products_limit')" 
                            :value="$settings['landing_products_limit'] ?? 6" 
                        />
                        <x-forms.input 
                            name="landing_products_cta_text_en" 
                            :label="__('admin.settings.landing.btn_en')" 
                            :value="$settings['landing_products_cta_text_en'] ?? 'VIEW ALL FORMULATIONS'" 
                        />
                        <x-forms.input 
                            name="landing_products_cta_text_ar" 
                            :label="__('admin.settings.landing.btn_ar')" 
                            :value="$settings['landing_products_cta_text_ar'] ?? 'عرض جميع المستحضرات'" 
                        />
                    </div>
                </div>

                <!-- 7. Clinical Quality & Verification Standards -->
                <div class="card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-shield-halved text-success"></i> {{ __('admin.settings.landing.quality') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'شارات الاعتماد الدوائي cGMP، فحوصات HPLC الثلاثية، وخلو المنتجات من المواد المالئة.' : 'cGMP, FDA facility registrations, and third-party HPLC laboratory assay badges.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="landing_quality_enabled" 
                            :label="__('admin.settings.landing.enable_section')" 
                            :checked="$settings['landing_quality_enabled'] ?? true" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_quality_badge_en" :label="__('admin.settings.landing.badge_en')" :value="$settings['landing_quality_badge_en'] ?? 'CLINICAL INTEGRITY & PURITY'" />
                        <x-forms.input name="landing_quality_badge_ar" :label="__('admin.settings.landing.badge_ar')" :value="$settings['landing_quality_badge_ar'] ?? 'النزاهة السريرية ومعايير النقاء'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_quality_title_en" :label="__('admin.settings.landing.title_en')" :value="$settings['landing_quality_title_en'] ?? 'Uncompromising Pharmaceutical-Grade Standards'" />
                        <x-forms.input name="landing_quality_title_ar" :label="__('admin.settings.landing.title_ar')" :value="$settings['landing_quality_title_ar'] ?? 'معايير تصنيع صيدلانية صارمة لا تقبل المساومة'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <x-forms.textarea name="landing_quality_desc_en" :label="__('admin.settings.landing.subtitle_en')" rows="2">{{ $settings['landing_quality_desc_en'] ?? 'Every single formulation is manufactured in cGMP-certified, FDA-registered facilities and undergoes rigorous triple third-party HPLC assays.' }}</x-forms.textarea>
                        <x-forms.textarea name="landing_quality_desc_ar" :label="__('admin.settings.landing.subtitle_ar')" rows="2">{{ $settings['landing_quality_desc_ar'] ?? 'تُصنع جميع تركيباتنا داخل منشآت معتمدة وفق معايير التصنيع الدوائي cGMP ومسجلة لدى هيئات الغذاء والدواء، وتخضع لفحوصات ثلاثية مخبرية مستقلة.' }}</x-forms.textarea>
                    </div>
                </div>

                <!-- 8. Testimonials & Medical Advisory Endorsements -->
                <div class="card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-comments text-primary"></i> {{ __('admin.settings.landing.testimonials') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'شهادات المرضى، الأطباء، ومراجعات مستخدمي البروتوكولات الخلوية.' : 'Verified patient and clinician reviews and medical advisory board endorsements.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="landing_testimonials_enabled" 
                            :label="__('admin.settings.landing.enable_section')" 
                            :checked="$settings['landing_testimonials_enabled'] ?? true" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_testimonials_badge_en" :label="__('admin.settings.landing.badge_en')" :value="$settings['landing_testimonials_badge_en'] ?? 'CLINICAL & CLIENT ENDORSEMENTS'" />
                        <x-forms.input name="landing_testimonials_badge_ar" :label="__('admin.settings.landing.badge_ar')" :value="$settings['landing_testimonials_badge_ar'] ?? 'شهادات وتجارب العملاء والأطباء'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_testimonials_title_en" :label="__('admin.settings.landing.title_en')" :value="$settings['landing_testimonials_title_en'] ?? 'Trusted by Clinicians and Longevity Seekers Worldwide'" />
                        <x-forms.input name="landing_testimonials_title_ar" :label="__('admin.settings.landing.title_ar')" :value="$settings['landing_testimonials_title_ar'] ?? 'موثوق من كبار الأطباء والباحثين عن جودة الحياة حول العالم'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <x-forms.textarea name="landing_testimonials_subtitle_en" :label="__('admin.settings.landing.subtitle_en')" rows="2">{{ $settings['landing_testimonials_subtitle_en'] ?? 'Real experiences from patients, biohackers, and longevity physicians integrating Blue Zone into daily protocols.' }}</x-forms.textarea>
                        <x-forms.textarea name="landing_testimonials_subtitle_ar" :label="__('admin.settings.landing.subtitle_ar')" rows="2">{{ $settings['landing_testimonials_subtitle_ar'] ?? 'تجارب حقيقية من ممارسي الرعاية الصحية والأفراد الملتزمين بنمط حياة حيوي مستدام.' }}</x-forms.textarea>
                    </div>
                </div>

                <!-- 9. FAQ Accordion Section -->
                <div class="card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-circle-question text-primary"></i> {{ __('admin.settings.landing.faqs') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'قسم الأسئلة الطبية والتنظيمية الشائعة لزوّار الصفحة الرئيسية.' : 'Interactive accordion for dosages, synergistic stacking, and logistics FAQs.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="landing_faqs_enabled" 
                            :label="__('admin.settings.landing.enable_section')" 
                            :checked="$settings['landing_faqs_enabled'] ?? true" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_faqs_badge_en" :label="__('admin.settings.landing.badge_en')" :value="$settings['landing_faqs_badge_en'] ?? 'FREQUENTLY ASKED QUESTIONS'" />
                        <x-forms.input name="landing_faqs_badge_ar" :label="__('admin.settings.landing.badge_ar')" :value="$settings['landing_faqs_badge_ar'] ?? 'الأسئلة الشائعة والإرشادات'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_faqs_title_en" :label="__('admin.settings.landing.title_en')" :value="$settings['landing_faqs_title_en'] ?? 'Everything You Need to Know About Our Formulations'" />
                        <x-forms.input name="landing_faqs_title_ar" :label="__('admin.settings.landing.title_ar')" :value="$settings['landing_faqs_title_ar'] ?? 'كل ما تود معرفته حول تركيباتنا وبروتوكولات الاستخدام'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <x-forms.textarea name="landing_faqs_subtitle_en" :label="__('admin.settings.landing.subtitle_en')" rows="2">{{ $settings['landing_faqs_subtitle_en'] ?? 'Find clinical answers regarding dosages, synergies, sourcing purity, and subscription delivery schedules.' }}</x-forms.textarea>
                        <x-forms.textarea name="landing_faqs_subtitle_ar" :label="__('admin.settings.landing.subtitle_ar')" rows="2">{{ $settings['landing_faqs_subtitle_ar'] ?? 'إجابات طبية دقيقة حول الجرعات، التناغم بين المستحضرات، مصادر النقاء، وجداول الشحن والتسليم.' }}</x-forms.textarea>
                    </div>
                </div>

                <!-- 10. Newsletter & Longevity Protocol Lead Capture -->
                <div class="card" style="padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-envelope-open-text text-primary"></i> {{ __('admin.settings.landing.newsletter') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'بانر الاشتراك البريدي وبروتوكول طول العمر وتقديم قسيمة الخصم الترحيبية للمشتركين الجدد.' : 'Newsletter email capture banner offering welcome discounts and clinical research digests.' }}
                            </p>
                        </div>
                        <x-forms.toggle 
                            name="landing_newsletter_enabled" 
                            :label="__('admin.settings.landing.enable_section')" 
                            :checked="$settings['landing_newsletter_enabled'] ?? true" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_newsletter_badge_en" :label="__('admin.settings.landing.badge_en')" :value="$settings['landing_newsletter_badge_en'] ?? 'JOIN THE LONGEVITY COLLECTIVE'" />
                        <x-forms.input name="landing_newsletter_badge_ar" :label="__('admin.settings.landing.badge_ar')" :value="$settings['landing_newsletter_badge_ar'] ?? 'انضم إلى مجتمع طول العمر والعافية'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input name="landing_newsletter_title_en" :label="__('admin.settings.landing.title_en')" :value="$settings['landing_newsletter_title_en'] ?? 'Begin Your Biological Longevity Protocol Today'" />
                        <x-forms.input name="landing_newsletter_title_ar" :label="__('admin.settings.landing.title_ar')" :value="$settings['landing_newsletter_title_ar'] ?? 'ابدأ بروتوكولك الخلوي للوقاية وطول العمر اليوم'" />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.textarea name="landing_newsletter_desc_en" :label="__('admin.settings.landing.subtitle_en')" rows="2">{{ $settings['landing_newsletter_desc_en'] ?? 'Subscribe to receive exclusive clinical research briefings, early access to new micro-batch formulations, and 15% off your initial order.' }}</x-forms.textarea>
                        <x-forms.textarea name="landing_newsletter_desc_ar" :label="__('admin.settings.landing.subtitle_ar')" rows="2">{{ $settings['landing_newsletter_desc_ar'] ?? 'اشترك لتصلك أحدث أوراق الأبحاث الطبية، وأسبقية الحصول على التشغيلات الإنتاجية المحدودة، مع خصم 15% على طلبك الأول.' }}</x-forms.textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                        <x-forms.input 
                            name="landing_newsletter_discount_badge" 
                            :label="__('admin.settings.landing.discount_badge')" 
                            :value="$settings['landing_newsletter_discount_badge'] ?? '15% WELCOME OFFER'" 
                        />
                        <x-forms.input 
                            name="landing_newsletter_btn_en" 
                            :label="__('admin.settings.landing.btn_en')" 
                            :value="$settings['landing_newsletter_btn_en'] ?? 'SUBSCRIBE NOW'" 
                        />
                        <x-forms.input 
                            name="landing_newsletter_btn_ar" 
                            :label="__('admin.settings.landing.btn_ar')" 
                            :value="$settings['landing_newsletter_btn_ar'] ?? 'اشترك الآن مجاناً'" 
                        />
                    </div>
                </div>

                <!-- 11. Homepage SEO & Social Meta -->
                <div class="card" style="padding: 2rem;">
                    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                        <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-magnifying-glass text-primary"></i> {{ __('admin.settings.landing.seo') }}
                        </h4>
                        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                            {{ app()->getLocale() == 'ar' ? 'تخصيص عنوان ووصف الصفحة الرئيسية لمحركات البحث (Google / Bing) وبطاقات المشاركة في التواصل الاجتماعي.' : 'Search engine title tags, meta descriptions, and Open Graph card data for the storefront root URL.' }}
                        </p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.input 
                            name="landing_meta_title_en" 
                            :label="__('admin.settings.landing.meta_title_en')" 
                            :value="$settings['landing_meta_title_en'] ?? 'BLUE ZONE™ — Cellular Longevity & Botanical Medicine'" 
                        />
                        <x-forms.input 
                            name="landing_meta_title_ar" 
                            :label="__('admin.settings.landing.meta_title_ar')" 
                            :value="$settings['landing_meta_title_ar'] ?? 'بلو زون™ — الطب الخلوي وطول العمر والمستحضرات النباتية'" 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                        <x-forms.textarea 
                            name="landing_meta_desc_en" 
                            :label="__('admin.settings.landing.meta_desc_en')" 
                            rows="2"
                        >{{ $settings['landing_meta_desc_en'] ?? 'Discover pharmaceutical-grade cellular formulations inspired by the world’s longest-lived centenarian communities. Standardized bio-actives for NAD+ and mitochondrial vitality.' }}</x-forms.textarea>

                        <x-forms.textarea 
                            name="landing_meta_desc_ar" 
                            :label="__('admin.settings.landing.meta_desc_ar')" 
                            rows="2"
                        >{{ $settings['landing_meta_desc_ar'] ?? 'اكتشف تركيبات خلوية صيدلانية مستوحاة من أطول مجتمعات العالم عمراً. مستخلصات قياسية نقية لدعم طاقة الميتوكوندريا وإنزيم NAD+ والتجدد الخلوي.' }}</x-forms.textarea>
                    </div>

                    <div>
                        <x-forms.input 
                            name="landing_meta_keywords" 
                            :label="__('admin.settings.landing.meta_keywords')" 
                            :value="$settings['landing_meta_keywords'] ?? 'longevity, blue zones, cellular health, NAD+, mitochondrial energy, Nootropics, anti-aging, botanical medicine'" 
                        />
                    </div>
                </div>

            </div>
        </div>

        <!-- Tab 3: Payments & Taxes -->
        <div id="tab-commerce" data-tab-content="admin-settings" style="display: none;">
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    {{ __('admin.settings.sections.gateways_tax') }}
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <x-forms.input 
                        name="tax_percentage" 
                        type="number" 
                        step="0.01" 
                        min="0"
                        max="100"
                        :label="__('admin.settings.fields.tax_percentage')" 
                        :value="$settings['tax_percentage'] ?? 15" 
                        :hint="app()->getLocale() == 'ar' ? 'النسبة الضريبية المطبقة على المنتجات والفواتير (افتراضياً 15% VAT).' : 'VAT rate applied to products, cart, POS, and tax invoices.'"
                        required 
                    />
                    <x-forms.input 
                        name="tax_number" 
                        :label="__('admin.settings.fields.tax_number')" 
                        :value="$settings['tax_number'] ?? '31004829100003'" 
                        :hint="app()->getLocale() == 'ar' ? 'الرقم الضريبي المعتمد الصادر من هيئة الزكاة والضريبة والجمارك (ZATCA).' : 'Official corporate Tax ID registered with tax authorities.'"
                        required 
                    />
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <x-forms.toggle 
                        name="enable_tax" 
                        :label="app()->getLocale() == 'ar' ? 'تفعيل حساب ضريبة القيمة المضافة (VAT)' : 'Enable Dynamic VAT Calculations'" 
                        :description="app()->getLocale() == 'ar' ? 'عند التفعيل يتم احتساب الضريبة تلقائياً في المتجر، ونقطة البيع، والفواتير.' : 'When enabled, VAT will be automatically computed on store checkout, POS cashier, and tax invoices.'" 
                        :checked="$settings['enable_tax'] ?? true" 
                    />

                    <x-forms.toggle 
                        name="prices_include_tax" 
                        :label="app()->getLocale() == 'ar' ? 'أسعار المنتجات في الكتالوج شاملة الضريبة' : 'Catalog Prices Include Tax'" 
                        :description="app()->getLocale() == 'ar' ? 'حدد ما إذا كانت أسعار البيع المعروضة في المتجر تتضمن ضريبة القيمة المضافة مسبقاً.' : 'Specify whether entered retail prices already include VAT or if tax is added at checkout.'" 
                        :checked="$settings['prices_include_tax'] ?? false" 
                    />
                </div>

                <div style="border-top: 1px solid var(--color-border); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h4 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text);">
                                <i class="fa-solid fa-credit-card text-primary mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'إعدادات بوابات الدفع والربط المالي' : 'Payment Gateways & Webhook Architecture' }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'التحكم الديناميكي في بوابات الدفع والمفاتيح ونقاط الويبهوك بدون تعديل الكود المصدري.' : 'Dynamically manage payment keys, modes, and webhook secrets from config and database.' }}
                            </p>
                        </div>

                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <x-forms.select 
                                name="payment_default_gateway" 
                                :label="app()->getLocale() == 'ar' ? 'البوابة الافتراضية' : 'Default Gateway'" 
                                :selected="$settings['payment_default_gateway'] ?? 'stripe'"
                                :options="['stripe' => 'Stripe (Credit / Debit / Mada)', 'cod' => 'Cash on Delivery (COD)']" 
                            />
                        </div>
                    </div>

                    <!-- Stripe Gateway Configuration Box -->
                    <div style="background: var(--color-bg-alt); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="background: #635bff; color: white; border-radius: var(--radius-sm); padding: 0.35rem 0.65rem; font-weight: 900; font-size: 0.85rem; letter-spacing: 0.05em;">
                                    STRIPE
                                </div>
                                <div>
                                    <h5 style="margin: 0; font-size: 1rem; font-weight: 800;">Stripe / Credit & Debit Cards / Mada</h5>
                                    <span style="font-size: 0.75rem; color: var(--color-text-muted);">256-bit SSL encrypted credit card, debit, and Apple Pay payment processing.</span>
                                </div>
                            </div>

                            <x-forms.toggle 
                                name="payment_stripe_enabled" 
                                :label="app()->getLocale() == 'ar' ? 'تفعيل بوابة Stripe' : 'Enable Stripe Gateway'" 
                                :checked="$settings['payment_stripe_enabled'] ?? $settings['enable_online_payment'] ?? true" 
                            />
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                            <x-forms.select 
                                name="payment_stripe_mode" 
                                :label="app()->getLocale() == 'ar' ? 'بيئة التشغيل (Mode)' : 'Gateway Mode'" 
                                :selected="$settings['payment_stripe_mode'] ?? 'test'"
                                :options="[
                                    'test' => app()->getLocale() == 'ar' ? 'بيئة الاختبار التجريبية (Sandbox / Test Mode)' : 'Sandbox / Test Mode (No Real Charges)',
                                    'live' => app()->getLocale() == 'ar' ? 'بيئة الإنتاج الحية (Production / Live Mode)' : 'Production / Live Mode (Real Transactions)'
                                ]" 
                            />

                            <x-forms.input 
                                name="payment_stripe_public_key" 
                                :label="app()->getLocale() == 'ar' ? 'المفتاح العام (Publishable Key)' : 'Stripe Publishable Key'" 
                                :value="$settings['payment_stripe_public_key'] ?? config('payment.gateways.stripe.public_key', '')" 
                                placeholder="pk_test_... or pk_live_..."
                            />
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                            <x-forms.input 
                                name="payment_stripe_secret_key" 
                                type="password"
                                :label="app()->getLocale() == 'ar' ? 'المفتاح السري (Secret Key)' : 'Stripe Secret Key'" 
                                :value="$settings['payment_stripe_secret_key'] ?? config('payment.gateways.stripe.secret_key', '')" 
                                placeholder="sk_test_... or sk_live_..."
                            />

                            <x-forms.input 
                                name="payment_stripe_webhook_secret" 
                                type="password"
                                :label="app()->getLocale() == 'ar' ? 'سر توقيع الويبهوك (Webhook Signing Secret)' : 'Stripe Webhook Secret'" 
                                :value="$settings['payment_stripe_webhook_secret'] ?? config('payment.gateways.stripe.webhook_secret', '')" 
                                placeholder="whsec_..."
                            />
                        </div>

                        <!-- Webhook Endpoint URL Box -->
                        <div style="background: rgba(10, 79, 120, 0.05); border: 1px dashed var(--bz-accent-blue); padding: 1rem 1.25rem; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                            <div>
                                <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-primary); display: block; margin-bottom: 0.2rem;">
                                    <i class="fa-solid fa-link mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'رابط الويبهوك المباشر (Webhook Endpoint URL):' : 'Production Webhook Endpoint URL:' }}
                                </span>
                                <code id="stripeWebhookUrl" style="font-size: 0.85rem; font-weight: 700; color: var(--color-text);">
                                    {{ url('/webhooks/payment/stripe') }}
                                </code>
                            </div>

                            <div style="display: flex; gap: 0.5rem;">
                                <button type="button" class="btn btn-outline btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('stripeWebhookUrl').innerText.trim()); if(window.toast) window.toast.success('Webhook URL copied to clipboard!');">
                                    <i class="fa-solid fa-copy mr-1 ml-1"></i> Copy URL
                                </button>
                                <button type="button" class="btn btn-outline btn-sm" onclick="testWebhookPing('stripe')">
                                    <i class="fa-solid fa-bolt mr-1 ml-1 text-warning"></i> Simulate Webhook Ping
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cash on Delivery (COD) Configuration Box -->
                    <div style="background: var(--color-bg-alt); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 1.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="background: #10b981; color: white; border-radius: var(--radius-sm); padding: 0.35rem 0.65rem; font-weight: 900; font-size: 0.85rem; letter-spacing: 0.05em;">
                                    COD
                                </div>
                                <div>
                                    <h5 style="margin: 0; font-size: 1rem; font-weight: 800;">Cash on Delivery (COD)</h5>
                                    <span style="font-size: 0.75rem; color: var(--color-text-muted);">Allow customers to settle payment upon arrival with verified courier handover.</span>
                                </div>
                            </div>

                            <x-forms.toggle 
                                name="payment_cod_enabled" 
                                :label="app()->getLocale() == 'ar' ? 'تفعيل الدفع عند الاستلام' : 'Enable Cash on Delivery'" 
                                :checked="$settings['payment_cod_enabled'] ?? $settings['enable_cod'] ?? true" 
                            />
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <x-forms.input 
                                name="payment_cod_extra_fee" 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                :label="app()->getLocale() == 'ar' ? 'رسوم خدمة الدفع عند الاستلام الإضافية (إن وجدت)' : 'COD Handling Fee ($ / SAR)'" 
                                :value="$settings['payment_cod_extra_fee'] ?? 0.00" 
                                :hint="app()->getLocale() == 'ar' ? 'رسوم إضافية اختيارية تُضاف إلى الإجمالي عند اختيار الدفع عند الاستلام.' : 'Optional handling surcharge added when customer selects COD.'"
                            />

                            <div style="display: flex; align-items: center; padding-top: 1.5rem; font-size: 0.85rem; color: var(--color-text-muted);">
                                <span><i class="fa-solid fa-truck text-success mr-1.5 ml-1.5"></i> Courier white-glove signature and payment capture logged into order history.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Store & Inventory -->
        <div id="tab-store" data-tab-content="admin-settings" style="display: none;">
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    {{ __('admin.settings.sections.store_ops') }}
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <x-forms.input 
                        name="low_stock_threshold" 
                        type="number" 
                        :label="__('admin.settings.fields.low_stock_threshold')" 
                        :value="$settings['low_stock_threshold'] ?? $settings['inventory_low_stock_global_threshold'] ?? 10" 
                        :hint="app()->getLocale() == 'ar' ? 'عند وصول المخزون لهذا الحد يتم إرسال تنبيهات تلقائية لمدراء المخزون.' : 'When stock reaches this level, visual warnings and inventory lead alerts will trigger.'" 
                    />

                    <x-forms.select 
                        name="zero_stock_behavior" 
                        :label="__('admin.settings.fields.zero_stock_behavior')" 
                        :selected="$settings['zero_stock_behavior'] ?? 'mark_out_of_stock'"
                        :options="app()->getLocale() == 'ar' ? [
                            'mark_out_of_stock' => 'تمييز كنفاد من المخزون (إيقاف الشراء)',
                            'allow_backorders' => 'السماح بالطلب المسبق (Backorder)',
                            'hide_product' => 'إخفاء المنتج تماماً من الواجهة',
                        ] : [
                            'mark_out_of_stock' => 'Mark as Out of Stock (Disable Checkout)',
                            'allow_backorders' => 'Allow Clinical Pre-orders (Backorder)',
                            'hide_product' => 'Hide Product from Customer Catalog',
                        ]" 
                    />
                </div>

                <div style="margin-top: 1.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                    <x-forms.toggle 
                        name="enable_reviews" 
                        :label="__('admin.settings.fields.enable_reviews')" 
                        :description="app()->getLocale() == 'ar' ? 'السماح للعملاء الذين اشتروا التركيبات بإضافة تقييماتهم الموثقة.' : 'Allow customers who purchased protocols to submit verified ratings.'" 
                        :checked="$settings['enable_reviews'] ?? true" 
                    />

                    <x-forms.toggle 
                        name="enable_coupons" 
                        :label="__('admin.settings.fields.enable_coupons')" 
                        :description="app()->getLocale() == 'ar' ? 'السماح بتطبيق كوبونات ورموز الخصم في السلة والدفع.' : 'Permit discount codes during cart and checkout.'" 
                        :checked="$settings['enable_coupons'] ?? true" 
                    />
                </div>
            </div>
        </div>

        <!-- Tab 4: Shipping -->
        <div id="tab-shipping" data-tab-content="admin-settings" style="display: none;">
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    {{ __('admin.settings.sections.shipping_rules') }}
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <x-forms.input 
                        name="free_shipping_threshold" 
                        type="number" 
                        step="0.01" 
                        :label="__('admin.settings.fields.free_shipping_threshold')" 
                        :value="$settings['free_shipping_threshold'] ?? 75.00" 
                    />

                    <x-forms.input 
                        name="flat_shipping_rate" 
                        type="number" 
                        step="0.01" 
                        :label="__('admin.settings.fields.flat_shipping_rate')" 
                        :value="$settings['flat_shipping_rate'] ?? 9.99" 
                    />
                </div>
            </div>
        </div>

        <!-- Tab 5: Alerts & Audio Micro-Interactions -->
        <div id="tab-alerts" data-tab-content="admin-settings" style="display: none;">
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    {{ __('admin.settings.sections.notification_triggers') }}
                </h3>

                <x-forms.toggle 
                    name="toast_sound_enabled" 
                    :label="app()->getLocale() == 'ar' ? 'تفعيل المؤثرات الصوتية للإشعارات (Toast Audio Chimes)' : 'Enable Notification Sound Effects (Toast Chimes)'" 
                    :description="app()->getLocale() == 'ar' ? 'تشغيل نغمات صوتية مميزة وفائقة النقاء عند ظهور إشعارات النجاح، الخطأ، والتنبيهات في لوحة التحكم والمتجر.' : 'Play distinct synthetic audio cues when success, error, warning, or notice toasts appear.'" 
                    :checked="$settings['toast_sound_enabled'] ?? true" 
                />

                <div style="margin-top: 1.5rem; background: var(--color-bg-alt); padding: 1.5rem; border-radius: var(--radius-md); border: 1px dashed var(--color-border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <h4 style="font-size: 1rem; font-weight: 800; margin: 0; color: var(--color-text);">
                                <i class="fa-solid fa-volume-high text-primary mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'اختبار وفحص أصوات الإشعارات المباشرة' : 'Live Notification Sound Tester' }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? 'اضغط على أي زر لتجربة النغمة الصوتية وتصميم التوست المرتبط بها فوراً:' : 'Click any button below to preview the acoustic sound and toast animation in real-time:' }}
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <button type="button" class="btn btn-outline" onclick="window.toast.testSound('success')" style="border-color: #10B981; color: #10B981; font-weight: 700;">
                            <i class="fa-solid fa-bell mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'فحص نغمة النجاح' : 'Test Success Sound' }}
                        </button>
                        <button type="button" class="btn btn-outline" onclick="window.toast.testSound('error')" style="border-color: #EF4444; color: #EF4444; font-weight: 700;">
                            <i class="fa-solid fa-circle-xmark mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'فحص نغمة الخطأ' : 'Test Error Sound' }}
                        </button>
                        <button type="button" class="btn btn-outline" onclick="window.toast.testSound('warning')" style="border-color: #F59E0B; color: #F59E0B; font-weight: 700;">
                            <i class="fa-solid fa-triangle-exclamation mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'فحص نغمة التحذير' : 'Test Warning Sound' }}
                        </button>
                        <button type="button" class="btn btn-outline" onclick="window.toast.testSound('info')" style="border-color: #0284C7; color: #0284C7; font-weight: 700;">
                            <i class="fa-solid fa-circle-info mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'فحص نغمة الإشعار' : 'Test Info Sound' }}
                        </button>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; border-top: 1px solid var(--color-border); padding-top: 1.5rem;">
                    <x-forms.toggle 
                        name="notify_low_stock" 
                        :label="__('admin.settings.fields.notify_low_stock')" 
                        :description="app()->getLocale() == 'ar' ? 'إرسال تنبيهات بريدية فورية لمدراء المستودع عند انخفاض الكميات عن الحد المحدد.' : 'Immediately dispatch automated alert to inventory managers when threshold is breached.'" 
                        :checked="$settings['notify_low_stock'] ?? true" 
                    />

                    <x-forms.toggle 
                        name="notify_new_order" 
                        :label="__('admin.settings.fields.notify_new_order')" 
                        :description="app()->getLocale() == 'ar' ? 'تنبيه فريق التجهيز واللوجستيات فور سداد أو تأكيد الطلب الجديد.' : 'Alert fulfillment team upon verified payment capture.'" 
                        :checked="$settings['notify_new_order'] ?? true" 
                    />
                </div>
            </div>
        </div>

        <!-- Tab: Typography & Fonts (Live Interactive System Control) -->
        <div id="tab-typography" data-tab-content="admin-settings" style="display: none;">
            @php
                $availableFonts = \App\Services\TypographyService::getAvailableFonts();
                $activeConfig = \App\Services\TypographyService::getActiveConfig();
            @endphp

            <!-- Hidden inputs synchronized for main settings form submission -->
            <input type="hidden" name="font_family" id="bz_input_font_family" value="{{ $settings['font_family'] ?? $activeConfig['font_family'] }}">
            <input type="hidden" name="font_heading_family" id="bz_input_font_heading_family" value="{{ $settings['font_heading_family'] ?? $activeConfig['font_heading_family'] }}">
            <input type="hidden" name="font_size_base" id="bz_input_font_size_base" value="{{ $settings['font_size_base'] ?? $activeConfig['font_size_base'] }}">
            <input type="hidden" name="font_weight_headings" id="bz_input_font_weight_headings" value="{{ $settings['font_weight_headings'] ?? $activeConfig['font_weight_headings'] }}">
            <input type="hidden" name="font_weight_body" id="bz_input_font_weight_body" value="{{ $settings['font_weight_body'] ?? $activeConfig['font_weight_body'] }}">
            <input type="hidden" name="font_letter_spacing" id="bz_input_font_letter_spacing" value="{{ $settings['font_letter_spacing'] ?? $activeConfig['font_letter_spacing'] }}">

            <div class="bz-typo-container">
                <style>
                    .bz-typo-container {
                        display: flex;
                        flex-direction: column;
                        gap: 1.5rem;
                    }
                    .bz-typo-hero {
                        background: linear-gradient(135deg, #0A4F78 0%, #062B49 55%, #031827 100%);
                        color: #FFFFFF;
                        border-radius: 1rem;
                        padding: 1.75rem;
                        box-shadow: 0 10px 25px -5px rgba(10, 79, 120, 0.35);
                        border: 1px solid rgba(255, 255, 255, 0.12);
                    }
                    .bz-typo-hero-header {
                        display: flex;
                        flex-direction: column;
                        gap: 1.25rem;
                        justify-content: space-between;
                    }
                    @media (min-width: 900px) {
                        .bz-typo-hero-header {
                            flex-direction: row;
                            align-items: center;
                        }
                    }
                    .bz-typo-brand {
                        display: flex;
                        align-items: center;
                        gap: 1.1rem;
                    }
                    .bz-typo-icon {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 3.5rem;
                        height: 3.5rem;
                        border-radius: 0.85rem;
                        background: rgba(255, 255, 255, 0.12);
                        font-size: 1.85rem;
                        backdrop-filter: blur(8px);
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        color: #60A5FA;
                        flex-shrink: 0;
                    }
                    .bz-typo-title {
                        font-size: 1.45rem;
                        font-weight: 800;
                        margin: 0;
                        color: #FFFFFF;
                        letter-spacing: -0.02em;
                    }
                    .bz-typo-desc {
                        font-size: 0.875rem;
                        margin: 0.3rem 0 0 0;
                        color: rgba(255, 255, 255, 0.82);
                        line-height: 1.5;
                        max-width: 650px;
                    }
                    .bz-typo-toolbar {
                        display: flex;
                        flex-wrap: wrap;
                        align-items: center;
                        gap: 0.75rem;
                    }
                    .bz-typo-toggle-label {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        background: rgba(255, 255, 255, 0.12);
                        padding: 0.6rem 0.95rem;
                        border-radius: 0.625rem;
                        font-size: 0.8rem;
                        font-weight: 700;
                        cursor: pointer;
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        transition: background 0.2s;
                        user-select: none;
                    }
                    .bz-typo-toggle-label:hover {
                        background: rgba(255, 255, 255, 0.2);
                    }
                    .bz-typo-btn-reset {
                        background: rgba(255, 255, 255, 0.08);
                        border: 1px solid rgba(255, 255, 255, 0.25);
                        color: #FFFFFF;
                        font-weight: 700;
                        font-size: 0.825rem;
                        padding: 0.6rem 1.1rem;
                        border-radius: 0.625rem;
                        cursor: pointer;
                        transition: all 0.2s;
                    }
                    .bz-typo-btn-reset:hover {
                        background: rgba(255, 255, 255, 0.18);
                    }
                    .bz-typo-btn-save {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        background: #10B981;
                        color: #FFFFFF;
                        font-weight: 800;
                        font-size: 0.825rem;
                        padding: 0.65rem 1.35rem;
                        border-radius: 0.625rem;
                        border: none;
                        cursor: pointer;
                        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
                        transition: all 0.2s;
                        text-transform: uppercase;
                        letter-spacing: 0.03em;
                    }
                    .bz-typo-btn-save:hover {
                        background: #059669;
                        transform: translateY(-1px);
                    }
                    .bz-typo-pills-row {
                        margin-top: 1.25rem;
                        padding-top: 1.1rem;
                        border-top: 1px solid rgba(255, 255, 255, 0.15);
                        display: flex;
                        flex-wrap: wrap;
                        align-items: center;
                        gap: 0.6rem;
                        font-size: 0.8rem;
                    }
                    .bz-typo-pill {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.35rem;
                        padding: 0.3rem 0.75rem;
                        border-radius: 9999px;
                        background: rgba(255, 255, 255, 0.14);
                        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                        font-weight: 600;
                    }

                    /* 2-Column Responsive Grid */
                    .bz-typo-grid {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 1.5rem;
                    }
                    @media (min-width: 1080px) {
                        .bz-typo-grid {
                            grid-template-columns: 7fr 5fr;
                        }
                    }

                    .bz-typo-panel {
                        background: #FFFFFF;
                        border: 1px solid var(--color-border, #E2E8F0);
                        border-radius: 1rem;
                        padding: 1.75rem;
                        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
                    }
                    .bz-typo-panel-title {
                        font-size: 1.125rem;
                        font-weight: 800;
                        margin: 0 0 1.25rem 0;
                        color: var(--color-text, #0F172A);
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }

                    /* Choice button groups */
                    .bz-btn-group-5 {
                        display: grid;
                        grid-template-columns: repeat(5, 1fr);
                        gap: 0.35rem;
                    }
                    .bz-btn-group-3 {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 0.35rem;
                    }
                    .bz-choice-btn {
                        padding: 0.55rem 0.25rem;
                        text-align: center;
                        font-size: 0.785rem;
                        font-weight: 700;
                        border-radius: 0.5rem;
                        border: 1px solid #CBD5E1;
                        background: #F8FAFC;
                        color: #334155;
                        cursor: pointer;
                        transition: all 0.15s;
                    }
                    .bz-choice-btn:hover {
                        border-color: #2A8FC2;
                        background: #F1F5F9;
                    }
                    .bz-choice-btn.active {
                        background: #0A4F78;
                        color: #FFFFFF;
                        border-color: #0A4F78;
                        box-shadow: 0 2px 6px rgba(10, 79, 120, 0.3);
                    }

                    /* Catalog Section */
                    .bz-cat-header {
                        display: flex;
                        flex-direction: column;
                        gap: 0.75rem;
                        margin-bottom: 1.1rem;
                    }
                    @media (min-width: 640px) {
                        .bz-cat-header {
                            flex-direction: row;
                            align-items: center;
                            justify-content: space-between;
                        }
                    }
                    .bz-cat-pills {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 0.4rem;
                    }
                    .bz-cat-pill-btn {
                        padding: 0.35rem 0.75rem;
                        font-size: 0.75rem;
                        font-weight: 700;
                        border-radius: 0.5rem;
                        border: 1px solid #E2E8F0;
                        background: #F8FAFC;
                        color: #475569;
                        cursor: pointer;
                        transition: all 0.15s;
                    }
                    .bz-cat-pill-btn:hover {
                        border-color: #2A8FC2;
                    }
                    .bz-cat-pill-btn.active {
                        background: #2A8FC2;
                        color: #FFFFFF;
                        border-color: #2A8FC2;
                    }
                    .bz-font-grid-cards {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 0.85rem;
                        max-height: 520px;
                        overflow-y: auto;
                        padding-right: 0.35rem;
                    }
                    @media (min-width: 640px) {
                        .bz-font-grid-cards {
                            grid-template-columns: 1fr 1fr;
                        }
                    }
                    .bz-font-card-item {
                        padding: 0.95rem;
                        border-radius: 0.75rem;
                        border: 1px solid #E2E8F0;
                        background: #FFFFFF;
                        cursor: pointer;
                        transition: all 0.15s ease-in-out;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                    }
                    .bz-font-card-item:hover {
                        border-color: #2A8FC2;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(42, 143, 194, 0.12);
                    }
                    .bz-font-card-item.active {
                        border-color: #0A4F78;
                        background: rgba(10, 79, 120, 0.04);
                        box-shadow: 0 0 0 2px #0A4F78;
                    }

                    /* Sandbox & Sticky Preview */
                    .bz-sticky-sandbox-box {
                        position: sticky;
                        top: 2rem;
                    }
                    .bz-sandbox-view {
                        background: #FAFAF9;
                        border: 2px dashed #CBD5E1;
                        border-radius: 0.85rem;
                        padding: 1.5rem;
                        display: flex;
                        flex-direction: column;
                        gap: 1.25rem;
                        transition: all 0.2s ease-in-out;
                    }
                    .bz-live-pulse-badge {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.35rem;
                        background: rgba(16, 185, 129, 0.15);
                        color: #059669;
                        font-size: 0.75rem;
                        font-weight: 800;
                        padding: 0.25rem 0.65rem;
                        border-radius: 9999px;
                        text-transform: uppercase;
                    }
                    .bz-pulse-circle {
                        width: 7px;
                        height: 7px;
                        border-radius: 9999px;
                        background-color: #10B981;
                        animation: bz-pulse-anim 1.5s infinite;
                    }
                    @keyframes bz-pulse-anim {
                        0% { transform: scale(0.9); opacity: 0.7; }
                        50% { transform: scale(1.3); opacity: 1; }
                        100% { transform: scale(0.9); opacity: 0.7; }
                    }
                </style>

                <!-- Hero Section with Action Toolbar -->
                <div class="bz-typo-hero">
                    <div class="bz-typo-hero-header">
                        <div class="bz-typo-brand">
                            <div class="bz-typo-icon">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </div>
                            <div>
                                <h2 class="bz-typo-title">
                                    {{ app()->getLocale() == 'ar' ? 'المعاينة الحية والتحكم في خطوط النظام (Live Interactive Typography)' : 'Live Typography & System Font Engine' }}
                                </h2>
                                <p class="bz-typo-desc">
                                    {{ app()->getLocale() == 'ar' 
                                        ? 'التحكم الفوري في خطوط المتجر ولوحة التحكم مع معاينة لحظية وتطبيق فوري على مستوى النظام بالكامل بدون الانتقال لأي لوحة أخرى.' 
                                        : 'Select and update fonts with instantaneous real-time preview across Admin Dashboard, Storefront, and System Management.' }}
                                </p>
                            </div>
                        </div>

                        <div class="bz-typo-toolbar">
                            <label class="bz-typo-toggle-label" title="{{ app()->getLocale() == 'ar' ? 'تطبيق الخطوط المحددة مباشرة على لوحة الإدارة الحالية للمعاينة' : 'Preview fonts live on this admin panel' }}">
                                <input type="checkbox" id="bz_toggle_admin_preview" style="accent-color: #2A8FC2; width: 1.1rem; height: 1.1rem; cursor: pointer;">
                                <span>{{ app()->getLocale() == 'ar' ? 'معاينة حية على لوحة الإدارة' : 'Live Admin Panel Preview' }}</span>
                            </label>

                            <button type="button" class="bz-typo-btn-reset" onclick="BzTypography.resetDefaults()">
                                <i class="fa-solid fa-arrow-rotate-left mr-1 ml-1"></i>
                                {{ app()->getLocale() == 'ar' ? 'استعادة الافتراضي' : 'Reset Defaults' }}
                            </button>

                            <button type="button" class="bz-typo-btn-save" onclick="BzTypography.saveGlobally(this)">
                                <i class="fa-solid fa-check mr-1 ml-1"></i>
                                {{ app()->getLocale() == 'ar' ? 'حفظ وتطبيق على كامل النظام' : 'SAVE & APPLY GLOBALLY' }}
                            </button>
                        </div>
                    </div>

                    <!-- Active Config Badges -->
                    <div class="bz-typo-pills-row">
                        <span style="opacity: 0.9; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'الإعدادات النشطة:' : 'Active Config:' }}</span>
                        <div class="bz-typo-pill">
                            <span style="opacity: 0.7;">Primary:</span>
                            <span id="pill_primary" style="color: #67E8F9;">{{ $activeConfig['font_family'] }}</span>
                        </div>
                        <div class="bz-typo-pill">
                            <span style="opacity: 0.7;">Headings:</span>
                            <span id="pill_headings" style="color: #67E8F9;">{{ $activeConfig['font_heading_family'] }}</span>
                        </div>
                        <div class="bz-typo-pill">
                            <span style="opacity: 0.7;">Size:</span>
                            <span id="pill_size" style="color: #FDE047;">{{ $activeConfig['font_size_base'] }}</span>
                        </div>
                        <div class="bz-typo-pill">
                            <span style="opacity: 0.7;">Headings Weight:</span>
                            <span id="pill_hweight" style="color: #A7F3D0;">{{ $activeConfig['font_weight_headings'] }}</span>
                        </div>
                        <div class="bz-typo-pill">
                            <span style="opacity: 0.7;">Body Weight:</span>
                            <span id="pill_bweight" style="color: #A7F3D0;">{{ $activeConfig['font_weight_body'] }}</span>
                        </div>
                        <div class="bz-typo-pill">
                            <span style="opacity: 0.7;">Spacing:</span>
                            <span id="pill_spacing" style="color: #DDD6FE;">{{ $activeConfig['font_letter_spacing'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Main 2-Column Layout -->
                <div class="bz-typo-grid">
                    
                    <!-- Left Column: Controls and Font Catalog -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        
                        <!-- Panel 1: Typography Parameters -->
                        <div class="bz-typo-panel">
                            <div class="bz-typo-panel-title">
                                <span><i class="fa-solid fa-sliders text-primary mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'خصائص الخطوط والأحجام' : 'Typography Parameters' }}</span>
                                <span style="font-size: 0.75rem; color: #10B981; font-weight: 800; background: rgba(16, 185, 129, 0.1); padding: 0.2rem 0.5rem; border-radius: 9999px;">
                                    <i class="fa-solid fa-bolt mr-1 ml-1"></i> Real-Time Sync
                                </span>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #475569; margin-bottom: 0.4rem;">
                                        {{ app()->getLocale() == 'ar' ? 'الخط الأساسي للنصوص (Primary Body Font)' : 'Primary Body Font' }}
                                    </label>
                                    <select id="bz_select_primary" class="form-input" style="font-weight: 700; width: 100%;" onchange="BzTypography.setPrimaryFont(this.value)">
                                        @foreach($availableFonts as $key => $f)
                                            <option value="{{ $key }}" {{ ($settings['font_family'] ?? $activeConfig['font_family']) === $key ? 'selected' : '' }}>
                                                {{ $f['label'] }} — {{ $f['category'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #475569; margin-bottom: 0.4rem;">
                                        {{ app()->getLocale() == 'ar' ? 'خط العناوين والهوية (Headings & Brand Font)' : 'Headings & Brand Font' }}
                                    </label>
                                    <select id="bz_select_headings" class="form-input" style="font-weight: 700; width: 100%;" onchange="BzTypography.setHeadingFont(this.value)">
                                        @foreach($availableFonts as $key => $f)
                                            <option value="{{ $key }}" {{ ($settings['font_heading_family'] ?? $activeConfig['font_heading_family']) === $key ? 'selected' : '' }}>
                                                {{ $f['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Buttons: Sizes & Weights -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                                <!-- Base Size -->
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #475569; margin-bottom: 0.4rem;">
                                        {{ app()->getLocale() == 'ar' ? 'حجم الخط الأساسي' : 'Base Font Size' }}
                                    </label>
                                    <div class="bz-btn-group-5" id="group_sizes">
                                        @foreach(['14px', '15px', '16px', '17px', '18px'] as $sz)
                                            <button type="button" class="bz-choice-btn {{ ($settings['font_size_base'] ?? $activeConfig['font_size_base']) === $sz ? 'active' : '' }}" onclick="BzTypography.setFontSize('{{ $sz }}')">
                                                {{ $sz }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Headings Weight -->
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #475569; margin-bottom: 0.4rem;">
                                        {{ app()->getLocale() == 'ar' ? 'سُمك العناوين' : 'Headings Weight' }}
                                    </label>
                                    <div class="bz-btn-group-5" id="group_hweight">
                                        @foreach(['500' => 'Med', '600' => 'Semi', '700' => 'Bold', '800' => 'XBold', '900' => 'Black'] as $wVal => $wLabel)
                                            <button type="button" class="bz-choice-btn {{ ($settings['font_weight_headings'] ?? $activeConfig['font_weight_headings']) == $wVal ? 'active' : '' }}" onclick="BzTypography.setHeadingWeight('{{ $wVal }}')">
                                                {{ $wLabel }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Body Weight -->
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #475569; margin-bottom: 0.4rem;">
                                        {{ app()->getLocale() == 'ar' ? 'سُمك نصوص المحتوى' : 'Body Text Weight' }}
                                    </label>
                                    <div class="bz-btn-group-3" id="group_bweight">
                                        @foreach(['300' => 'Light', '400' => 'Regular', '500' => 'Medium'] as $bwVal => $bwLabel)
                                            <button type="button" class="bz-choice-btn {{ ($settings['font_weight_body'] ?? $activeConfig['font_weight_body']) == $bwVal ? 'active' : '' }}" onclick="BzTypography.setBodyWeight('{{ $bwVal }}')">
                                                {{ $bwLabel }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Letter Spacing -->
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #475569; margin-bottom: 0.4rem;">
                                        {{ app()->getLocale() == 'ar' ? 'تباعد الحروف اللاتينية' : 'Letter Spacing' }}
                                    </label>
                                    <div class="bz-btn-group-3" id="group_spacing">
                                        @foreach(['tight' => 'Tight', 'normal' => 'Normal', 'relaxed' => 'Relaxed'] as $spVal => $spLabel)
                                            <button type="button" class="bz-choice-btn {{ ($settings['font_letter_spacing'] ?? $activeConfig['font_letter_spacing']) === $spVal ? 'active' : '' }}" onclick="BzTypography.setLetterSpacing('{{ $spVal }}')">
                                                {{ $spLabel }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel 2: Curated Font Library with Live Samples -->
                        <div class="bz-typo-panel">
                            <div class="bz-cat-header">
                                <div>
                                    <h4 style="font-size: 1.05rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                                        <i class="fa-solid fa-shapes text-primary mr-1.5 ml-1.5"></i>
                                        {{ app()->getLocale() == 'ar' ? 'مكتبة الخطوط المعتمدة وسريعة التحميل' : 'Curated Font Library' }}
                                    </h4>
                                    <p style="font-size: 0.8rem; color: #64748B; margin: 0;">
                                        {{ app()->getLocale() == 'ar' ? 'اختر الخط مباشرة للمعاينة الحية بضغطة زر' : 'Click on any font card to apply instantaneously to sandbox' }}
                                    </p>
                                </div>

                                <div style="position: relative; min-width: 200px;">
                                    <input type="text" id="bz_font_search" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث عن خط...' : 'Search font...' }}" class="form-input" style="font-size: 0.825rem; padding: 0.45rem 0.85rem;" oninput="BzTypography.filterCatalog()">
                                </div>
                            </div>

                            <!-- Category Filter Pills -->
                            <div class="bz-cat-pills" style="margin-bottom: 1rem;" id="bz_category_pills">
                                <button type="button" class="bz-cat-pill-btn active" onclick="BzTypography.setCategory('All', this)">All</button>
                                <button type="button" class="bz-cat-pill-btn" onclick="BzTypography.setCategory('Arabic', this)">Arabic Specialized</button>
                                <button type="button" class="bz-cat-pill-btn" onclick="BzTypography.setCategory('Modern Sans', this)">Modern Sans</button>
                                <button type="button" class="bz-cat-pill-btn" onclick="BzTypography.setCategory('Clean UI', this)">Clean UI</button>
                                <button type="button" class="bz-cat-pill-btn" onclick="BzTypography.setCategory('Geometric', this)">Geometric</button>
                            </div>

                            <!-- Font Cards Grid -->
                            <div class="bz-font-grid-cards" id="bz_font_cards_grid">
                                @foreach($availableFonts as $fKey => $fMeta)
                                    <div 
                                        class="bz-font-card-item {{ ($settings['font_family'] ?? $activeConfig['font_family']) === $fKey ? 'active' : '' }}" 
                                        data-font-name="{{ $fKey }}"
                                        data-font-cat="{{ $fMeta['category'] }}"
                                        onclick="BzTypography.selectFontDirect('{{ $fKey }}')"
                                    >
                                        <div>
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                                <div style="font-size: 0.925rem; font-weight: 800; color: #0F172A;">
                                                    {{ $fMeta['name'] }}
                                                </div>
                                                <span style="font-size: 0.65rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 9999px; background: #F1F5F9; color: #64748B;">
                                                    {{ $fMeta['category'] }}
                                                </span>
                                            </div>

                                            <div style="font-family: '{{ $fKey }}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', sans-serif; font-size: 1.05rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; line-height: 1.3;">
                                                {{ $fMeta['preview_ar'] }}
                                            </div>

                                            <div style="font-family: '{{ $fKey }}', sans-serif; font-size: 0.775rem; color: #64748B; margin-bottom: 0.6rem;">
                                                {{ $fMeta['preview_en'] }}
                                            </div>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #F1F5F9; padding-top: 0.5rem; margin-top: 0.5rem; font-size: 0.725rem;">
                                            <button type="button" class="btn btn-outline btn-xs" style="font-weight: 700; font-size: 0.7rem; padding: 0.2rem 0.5rem;" onclick="event.stopPropagation(); BzTypography.setHeadingFont('{{ $fKey }}');">
                                                <i class="fa-solid fa-heading mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'للعناوين' : 'Headings' }}
                                            </button>
                                            <span style="color: #94A3B8; font-size: 0.7rem;">
                                                {{ count($fMeta['weights']) }} weights
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Interactive Real-Time Live Preview Sandbox -->
                    <div>
                        <div class="bz-sticky-sandbox-box">
                            <div class="bz-typo-panel" style="box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);">
                                <div class="bz-typo-panel-title">
                                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fa-solid fa-bolt text-amber-500"></i>
                                        <span>{{ app()->getLocale() == 'ar' ? 'المعاينة الحية الفورية (Live Sandbox)' : 'Real-Time Live Preview Sandbox' }}</span>
                                    </span>
                                    <span class="bz-live-pulse-badge">
                                        <span class="bz-pulse-circle"></span> Live
                                    </span>
                                </div>

                                <!-- The Dynamic Live Sandbox Container -->
                                <div 
                                    id="bz_live_sandbox" 
                                    class="bz-sandbox-view"
                                    style="
                                        font-family: '{{ $settings['font_family'] ?? $activeConfig['font_family'] }}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, sans-serif;
                                        font-size: {{ $settings['font_size_base'] ?? $activeConfig['font_size_base'] }};
                                        font-weight: {{ $settings['font_weight_body'] ?? $activeConfig['font_weight_body'] }};
                                        letter-spacing: {{ $settings['font_letter_spacing'] ?? $activeConfig['font_letter_spacing'] }};
                                    "
                                >
                                    <!-- Arabic Headline -->
                                    <div>
                                        <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em; display: block; margin-bottom: 0.2rem;">
                                            Arabic Headline (H1)
                                        </span>
                                        <h1 
                                            id="bz_preview_h1"
                                            style="
                                                margin: 0;
                                                font-family: '{{ $settings['font_heading_family'] ?? $activeConfig['font_heading_family'] }}', '{{ $settings['font_family'] ?? $activeConfig['font_family'] }}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', sans-serif; 
                                                font-weight: {{ $settings['font_weight_headings'] ?? $activeConfig['font_weight_headings'] }};
                                                font-size: 1.55rem;
                                                color: #0A4F78;
                                                line-height: 1.35;
                                            "
                                        >
                                            بلوزون — هندسة الصحة الخلوية وطول العمر
                                        </h1>
                                    </div>

                                    <!-- English Headline -->
                                    <div>
                                        <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em; display: block; margin-bottom: 0.2rem;">
                                            English Headline (H2)
                                        </span>
                                        <h2 
                                            id="bz_preview_h2"
                                            style="
                                                margin: 0;
                                                font-family: '{{ $settings['font_heading_family'] ?? $activeConfig['font_heading_family'] }}', '{{ $settings['font_family'] ?? $activeConfig['font_family'] }}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', sans-serif; 
                                                font-weight: {{ $settings['font_weight_headings'] ?? $activeConfig['font_weight_headings'] }};
                                                font-size: 1.2rem;
                                                color: #0F172A;
                                                line-height: 1.35;
                                            "
                                        >
                                            Cellular Optimization & Longevity Medicine
                                        </h2>
                                    </div>

                                    <!-- Body Paragraphs -->
                                    <div>
                                        <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em; display: block; margin-bottom: 0.2rem;">
                                            Body Typography
                                        </span>
                                        <p style="margin: 0; font-size: 0.95rem; color: #334155; line-height: 1.6;">
                                            نظام رقمي متكامل يربط إدارة المخزون والمبيعات وتجربة العميل بأعلى معايير الجودة والأداء.
                                        </p>
                                        <p style="margin: 0.4rem 0 0 0; font-size: 0.85rem; color: #64748B; line-height: 1.5;">
                                            Precision engineered for omnichannel retail, multi-warehouse inventory routing, and clinical-grade formulations.
                                        </p>
                                    </div>

                                    <!-- Interactive Buttons & Badges -->
                                    <div>
                                        <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em; display: block; margin-bottom: 0.35rem;">
                                            Buttons & Badges
                                        </span>
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                                            <button 
                                                type="button" 
                                                style="background: #0A4F78; color: #FFFFFF; font-weight: 700; font-size: 0.775rem; padding: 0.5rem 0.95rem; border-radius: 0.5rem; border: none; cursor: pointer;"
                                            >
                                                أضف للسلة • Add to Cart
                                            </button>
                                            <button 
                                                type="button" 
                                                style="background: #FFFFFF; color: #0A4F78; border: 1px solid #2A8FC2; font-weight: 700; font-size: 0.775rem; padding: 0.5rem 0.95rem; border-radius: 0.5rem; cursor: pointer;"
                                            >
                                                تفاصيل المنتج
                                            </button>
                                            <span style="background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 700; font-size: 0.725rem; padding: 0.25rem 0.65rem; border-radius: 9999px;">
                                                <i class="fa-solid fa-circle-check mr-1 ml-1"></i> متوفر في المخزون
                                            </span>
                                        </div>
                                    </div>

                                    <!-- E-commerce Price Card Preview -->
                                    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 0.85rem 1rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);">
                                        <div>
                                            <div style="font-size: 0.85rem; font-weight: 800; color: #0F172A;">
                                                NMN Longevity Complex 500mg
                                            </div>
                                            <div style="font-size: 0.725rem; color: #94A3B8; margin-top: 0.15rem;">
                                                المخزون المتوفر: 48 عبوة
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-size: 1rem; font-weight: 900; color: #0A4F78;">
                                                350.00 ر.س
                                            </div>
                                            <div style="font-size: 0.725rem; color: #94A3B8; text-decoration: line-through;">
                                                420.00 ر.س
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Numbers & Monospace Sample -->
                                    <div style="background: rgba(0, 0, 0, 0.04); border-radius: 0.5rem; padding: 0.6rem; text-align: center; font-size: 0.75rem; font-weight: 700; color: #475569;">
                                        الأرقام والرموز: 0123456789 • SAR 1,299.00 • VAT 15% (31004829100003)
                                    </div>
                                </div>

                                <div style="margin-top: 1.1rem; padding-top: 0.85rem; border-top: 1px solid #F1F5F9; font-size: 0.775rem; color: #64748B; line-height: 1.5;">
                                    <i class="fa-solid fa-lightbulb text-amber-500 mr-1.5 ml-1.5"></i> <strong>{{ app()->getLocale() == 'ar' ? 'المعاينة التفاعلية فورية:' : 'Instant live preview:' }}</strong>
                                    {{ app()->getLocale() == 'ar' ? 'التغييرات تنعكس مباشرة في المعاينة أعلاه. اضغط "حفظ وتطبيق على كامل النظام" لاعتمادها في قاعدة البيانات.' : 'Changes update live in this dashboard. Click "SAVE & APPLY GLOBALLY" to commit to the system database.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- JavaScript Engine for Live Interactive Typography inside Main System Dashboard -->
            <script>
                (function() {
                    const loadedFonts = new Set(['Cairo', 'Mont Blanc']);

                    window.BzTypography = {
                        state: {
                            primaryFont: '{{ $settings["font_family"] ?? $activeConfig["font_family"] }}',
                            headingFont: '{{ $settings["font_heading_family"] ?? $activeConfig["font_heading_family"] }}',
                            fontSize: '{{ $settings["font_size_base"] ?? $activeConfig["font_size_base"] }}',
                            headingWeight: '{{ $settings["font_weight_headings"] ?? $activeConfig["font_weight_headings"] }}',
                            bodyWeight: '{{ $settings["font_weight_body"] ?? $activeConfig["font_weight_body"] }}',
                            letterSpacing: '{{ $settings["font_letter_spacing"] ?? $activeConfig["font_letter_spacing"] }}',
                            adminLivePreview: false,
                            activeCategory: 'All',
                        },

                        init() {
                            this.loadFont(this.state.primaryFont);
                            if (this.state.headingFont && this.state.headingFont !== this.state.primaryFont) {
                                this.loadFont(this.state.headingFont);
                            }

                            const toggleAdmin = document.getElementById('bz_toggle_admin_preview');
                            if (toggleAdmin) {
                                toggleAdmin.addEventListener('change', (e) => {
                                    this.state.adminLivePreview = e.target.checked;
                                    if (this.state.adminLivePreview) {
                                        this.applyAdminPreview();
                                    } else {
                                        this.removeAdminPreview();
                                    }
                                });
                            }

                            this.updateSandbox();
                            this.updatePills();
                        },

                        loadFont(fontName) {
                            if (!fontName || loadedFonts.has(fontName)) return;

                            if (fontName === 'Mont Blanc') {
                                this.loadFont('Montserrat');
                                this.loadFont('Tajawal');
                                loadedFonts.add('Mont Blanc');
                                return;
                            }

                            const kebab = fontName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                            const formattedGoogle = fontName.replace(/\s+/g, '+');
                            const linkId = 'bz-font-link-' + kebab;

                            if (!document.getElementById(linkId)) {
                                const link = document.createElement('link');
                                link.id = linkId;
                                link.rel = 'stylesheet';
                                link.href = `https://fonts.bunny.net/css?family=${kebab}:300,400,500,600,700,800,900&display=swap`;
                                link.onerror = () => {
                                    link.href = `https://fonts.googleapis.com/css2?family=${formattedGoogle}:wght@300;400;500;600;700;800;900&display=swap`;
                                };
                                document.head.appendChild(link);
                                loadedFonts.add(fontName);
                            }
                        },

                        selectFontDirect(fontName) {
                            this.setPrimaryFont(fontName);
                            this.setHeadingFont(fontName);
                        },

                        setPrimaryFont(fontName) {
                            this.state.primaryFont = fontName;
                            this.loadFont(fontName);

                            const select = document.getElementById('bz_select_primary');
                            if (select) select.value = fontName;

                            const input = document.getElementById('bz_input_font_family');
                            if (input) input.value = fontName;

                            this.highlightActiveCards();
                            this.updateSandbox();
                            this.updatePills();

                            if (this.state.adminLivePreview) {
                                this.applyAdminPreview();
                            }
                        },

                        setHeadingFont(fontName) {
                            this.state.headingFont = fontName;
                            this.loadFont(fontName);

                            const select = document.getElementById('bz_select_headings');
                            if (select) select.value = fontName;

                            const input = document.getElementById('bz_input_font_heading_family');
                            if (input) input.value = fontName;

                            this.updateSandbox();
                            this.updatePills();

                            if (this.state.adminLivePreview) {
                                this.applyAdminPreview();
                            }
                        },

                        setFontSize(size) {
                            this.state.fontSize = size;
                            const input = document.getElementById('bz_input_font_size_base');
                            if (input) input.value = size;

                            this.updateButtonGroup('group_sizes', size);
                            this.updateSandbox();
                            this.updatePills();
                        },

                        setHeadingWeight(weight) {
                            this.state.headingWeight = weight;
                            const input = document.getElementById('bz_input_font_weight_headings');
                            if (input) input.value = weight;

                            this.updateButtonGroup('group_hweight', weight);
                            this.updateSandbox();
                            this.updatePills();

                            if (this.state.adminLivePreview) {
                                this.applyAdminPreview();
                            }
                        },

                        setBodyWeight(weight) {
                            this.state.bodyWeight = weight;
                            const input = document.getElementById('bz_input_font_weight_body');
                            if (input) input.value = weight;

                            this.updateButtonGroup('group_bweight', weight);
                            this.updateSandbox();
                            this.updatePills();
                        },

                        setLetterSpacing(spacing) {
                            this.state.letterSpacing = spacing;
                            const input = document.getElementById('bz_input_font_letter_spacing');
                            if (input) input.value = spacing;

                            this.updateButtonGroup('group_spacing', spacing);
                            this.updateSandbox();
                            this.updatePills();
                        },

                        updateButtonGroup(groupId, activeVal) {
                            const group = document.getElementById(groupId);
                            if (!group) return;
                            const buttons = group.querySelectorAll('.bz-choice-btn');
                            buttons.forEach(btn => {
                                const text = btn.textContent.trim();
                                if (text.startsWith(activeVal) || text === activeVal || (groupId === 'group_hweight' && (
                                    (activeVal === '500' && text.startsWith('Med')) ||
                                    (activeVal === '600' && text.startsWith('Semi')) ||
                                    (activeVal === '700' && text.startsWith('Bold')) ||
                                    (activeVal === '800' && text.startsWith('XBold')) ||
                                    (activeVal === '900' && text.startsWith('Black'))
                                )) || (groupId === 'group_bweight' && (
                                    (activeVal === '300' && text.startsWith('Light')) ||
                                    (activeVal === '400' && text.startsWith('Regular')) ||
                                    (activeVal === '500' && text.startsWith('Medium'))
                                )) || (groupId === 'group_spacing' && (
                                    (activeVal === 'tight' && text.startsWith('Tight')) ||
                                    (activeVal === 'normal' && text.startsWith('Normal')) ||
                                    (activeVal === 'relaxed' && text.startsWith('Relaxed'))
                                ))) {
                                    btn.classList.add('active');
                                } else {
                                    btn.classList.remove('active');
                                }
                            });
                        },

                        highlightActiveCards() {
                            const cards = document.querySelectorAll('.bz-font-card-item');
                            cards.forEach(card => {
                                if (card.getAttribute('data-font-name') === this.state.primaryFont) {
                                    card.classList.add('active');
                                } else {
                                    card.classList.remove('active');
                                }
                            });
                        },

                        updateSandbox() {
                            const sandbox = document.getElementById('bz_live_sandbox');
                            const h1 = document.getElementById('bz_preview_h1');
                            const h2 = document.getElementById('bz_preview_h2');

                            const primaryStack = `'${this.state.primaryFont}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, sans-serif`;
                            const headingStack = `'${this.state.headingFont}', '${this.state.primaryFont}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, sans-serif`;

                            let spacingCss = 'normal';
                            if (this.state.letterSpacing === 'tight') spacingCss = '-0.02em';
                            if (this.state.letterSpacing === 'relaxed') spacingCss = '0.04em';

                            if (sandbox) {
                                sandbox.style.fontFamily = primaryStack;
                                sandbox.style.fontSize = this.state.fontSize;
                                sandbox.style.fontWeight = this.state.bodyWeight;
                                sandbox.style.letterSpacing = spacingCss;
                            }

                            if (h1) {
                                h1.style.fontFamily = headingStack;
                                h1.style.fontWeight = this.state.headingWeight;
                            }

                            if (h2) {
                                h2.style.fontFamily = headingStack;
                                h2.style.fontWeight = this.state.headingWeight;
                            }
                        },

                        updatePills() {
                            const p1 = document.getElementById('pill_primary');
                            const p2 = document.getElementById('pill_headings');
                            const p3 = document.getElementById('pill_size');
                            const p4 = document.getElementById('pill_hweight');
                            const p5 = document.getElementById('pill_bweight');
                            const p6 = document.getElementById('pill_spacing');

                            if (p1) p1.textContent = this.state.primaryFont;
                            if (p2) p2.textContent = this.state.headingFont;
                            if (p3) p3.textContent = this.state.fontSize;
                            if (p4) p4.textContent = this.state.headingWeight;
                            if (p5) p5.textContent = this.state.bodyWeight;
                            if (p6) p6.textContent = this.state.letterSpacing;
                        },

                        applyAdminPreview() {
                            let styleTag = document.getElementById('bz-admin-live-font-override');
                            if (!styleTag) {
                                styleTag = document.createElement('style');
                                styleTag.id = 'bz-admin-live-font-override';
                                document.head.appendChild(styleTag);
                            }

                            const primaryStack = `'${this.state.primaryFont}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, sans-serif`;
                            const headingStack = `'${this.state.headingFont}', '${this.state.primaryFont}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, sans-serif`;

                            styleTag.innerHTML = `
                                body, .app-sidebar, .app-header, .card, table, input, select, button, .tab-btn {
                                    font-family: ${primaryStack} !important;
                                }
                                h1, h2, h3, h4, h5, h6, .card-title, .page-header-title {
                                    font-family: ${headingStack} !important;
                                    font-weight: ${this.state.headingWeight} !important;
                                }
                            `;
                        },

                        removeAdminPreview() {
                            const styleTag = document.getElementById('bz-admin-live-font-override');
                            if (styleTag) styleTag.remove();
                        },

                        resetDefaults() {
                            this.setPrimaryFont('Mont Blanc');
                            this.setHeadingFont('Mont Blanc');
                            this.setFontSize('16px');
                            this.setHeadingWeight('700');
                            this.setBodyWeight('400');
                            this.setLetterSpacing('normal');
                        },

                        setCategory(cat, btn) {
                            this.state.activeCategory = cat;
                            const catButtons = document.querySelectorAll('#bz_category_pills .bz-cat-pill-btn');
                            catButtons.forEach(b => b.classList.remove('active'));
                            if (btn) btn.classList.add('active');
                            this.filterCatalog();
                        },

                        filterCatalog() {
                            const query = (document.getElementById('bz_font_search')?.value || '').toLowerCase().trim();
                            const cat = this.state.activeCategory;
                            const cards = document.querySelectorAll('.bz-font-card-item');

                            cards.forEach(card => {
                                const name = (card.getAttribute('data-font-name') || '').toLowerCase();
                                const fontCat = (card.getAttribute('data-font-cat') || '').toLowerCase();

                                const matchesQuery = !query || name.includes(query) || fontCat.includes(query);
                                const matchesCat = cat === 'All' || fontCat.includes(cat.toLowerCase()) || (cat === 'Arabic' && (name === 'tajawal' || name === 'cairo' || name === 'alexandria' || name === 'almarai' || name.includes('arabic') || name === 'readex pro'));

                                if (matchesQuery && matchesCat) {
                                    card.style.display = 'flex';
                                } else {
                                    card.style.display = 'none';
                                }
                            });
                        },

                        saveGlobally(btn) {
                            const originalHtml = btn ? btn.innerHTML : '';
                            if (btn) {
                                btn.disabled = true;
                                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1 ml-1"></i> {{ app()->getLocale() == "ar" ? "جاري الحفظ..." : "Saving..." }}';
                            }

                            const form = document.getElementById('settingsForm');
                            const formData = form ? new FormData(form) : new FormData();

                            formData.set('font_family', this.state.primaryFont);
                            formData.set('font_heading_family', this.state.headingFont);
                            formData.set('font_size_base', this.state.fontSize);
                            formData.set('font_weight_headings', this.state.headingWeight);
                            formData.set('font_weight_body', this.state.bodyWeight);
                            formData.set('font_letter_spacing', this.state.letterSpacing);

                            fetch('{{ route("admin.settings.update") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (window.showAdminToast) {
                                    window.showAdminToast(
                                        '{{ app()->getLocale() == "ar" ? "تم حفظ وتطبيق الخطوط بنجاح!" : "Typography Saved Globally!" }}',
                                        '{{ app()->getLocale() == "ar" ? "تم تحديث خطوط المتجر ولوحة الإدارة وتطبيقها فوراً." : "New typography applied across Storefront and Admin Dashboard." }}',
                                        'fa-solid fa-circle-check text-emerald-500'
                                    );
                                } else {
                                    alert('{{ app()->getLocale() == "ar" ? "تم حفظ وتطبيق الخطوط بنجاح!" : "Typography settings saved successfully!" }}');
                                }
                            })
                            .catch(err => {
                                console.error('Save error:', err);
                                // Fallback: submit regular form
                                if (form) form.submit();
                            })
                            .finally(() => {
                                if (btn) {
                                    btn.disabled = false;
                                    btn.innerHTML = originalHtml;
                                }
                            });
                        }
                    };

                    document.addEventListener('DOMContentLoaded', function() {
                        window.BzTypography.init();
                    });
                })();
            </script>
        </div>

        <!-- Tab: Firebase Cloud Messaging (FCM) & Push Notifications -->
        <div id="tab-fcm" data-tab-content="admin-settings" style="display: none;">
            <!-- Configuration Guide & Status Banner -->
            <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid #0284C7; background: linear-gradient(135deg, rgba(2, 132, 199, 0.05) 0%, rgba(10, 79, 120, 0.02) 100%);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h4 style="font-size: 1.125rem; font-weight: 800; margin: 0 0 0.35rem 0; color: var(--color-text);">
                            <i class="fa-solid fa-satellite-dish text-sky-500 mr-1.5 ml-1.5"></i>
                            {{ app()->getLocale() == 'ar' ? 'إعدادات وتهيئة إشعارات Firebase (FCM Cloud Messaging)' : 'Firebase Cloud Messaging (FCM) Configuration & Push Engine' }}
                        </h4>
                        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0; max-width: 800px; line-height: 1.5;">
                            {{ app()->getLocale() == 'ar' 
                                ? 'تحكم في مفاتيح الربط السحابي لإرسال تنبيهات لحظية فورية للمتصفح والأجهزة المحمولة عند حدوث حركات المخزون، نقص الكميات، التوالف ونقل البضائع.' 
                                : 'Configure cloud push notification keys to broadcast instant operational alerts to browsers and devices upon inventory transfers, low stock buffers, and product issues.' }}
                        </p>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <a href="https://console.firebase.google.com/" target="_blank" class="btn btn-outline btn-sm font-bold" style="border-color: #0284C7; color: #0284C7;">
                            <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1"></i>
                            {{ app()->getLocale() == 'ar' ? 'لوحة تحكم Firebase' : 'Firebase Console' }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 1: Cloud Credentials -->
            <div class="card" style="padding: 2rem; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 800; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-key text-amber-500"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'مفاتيح وبيانات الاتصال بـ Firebase (Project Credentials)' : 'Firebase Project & API Credentials' }}</span>
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <x-forms.input 
                        name="fcm_project_id" 
                        :label="app()->getLocale() == 'ar' ? 'معرّف المشروع (Firebase Project ID)' : 'Firebase Project ID'" 
                        :value="$settings['fcm_project_id'] ?? ''" 
                        placeholder="e.g. blue-zone-health"
                    />

                    <x-forms.input 
                        name="fcm_api_key" 
                        :label="app()->getLocale() == 'ar' ? 'مفتاح Web API Key' : 'Web API Key (apiKey)'" 
                        :value="$settings['fcm_api_key'] ?? ''" 
                        placeholder="AIzaSy..."
                    />
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <x-forms.input 
                        name="fcm_server_key" 
                        type="password"
                        :label="app()->getLocale() == 'ar' ? 'مفتاح الخادم السحابي (FCM Server Key / Secret)' : 'FCM Server Key / Secret'" 
                        :value="$settings['fcm_server_key'] ?? ''" 
                        placeholder="AAAA... or Service Account Key"
                    />
                    <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem;">
                        {{ app()->getLocale() == 'ar' ? 'يُستخدم للإرسال من السيرفر (PHP Backend Dispatcher). يمكن العثور عليه في Firebase Project Settings > Cloud Messaging.' : 'Used by PHP Backend to dispatch push payloads via FCM API.' }}
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <x-forms.input 
                        name="fcm_auth_domain" 
                        :label="app()->getLocale() == 'ar' ? 'نطاق المصادقة (Auth Domain)' : 'Auth Domain (authDomain)'" 
                        :value="$settings['fcm_auth_domain'] ?? ''" 
                        placeholder="bluezone-998e6.firebaseapp.com"
                    />

                    <x-forms.input 
                        name="fcm_storage_bucket" 
                        :label="app()->getLocale() == 'ar' ? 'مستودع التخزين (Storage Bucket)' : 'Storage Bucket (storageBucket)'" 
                        :value="$settings['fcm_storage_bucket'] ?? ''" 
                        placeholder="bluezone-998e6.firebasestorage.app"
                    />
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <x-forms.input 
                        name="fcm_messaging_sender_id" 
                        :label="app()->getLocale() == 'ar' ? 'معرّف المرسل (Messaging Sender ID)' : 'Messaging Sender ID'" 
                        :value="$settings['fcm_messaging_sender_id'] ?? ''" 
                        placeholder="e.g. 1029384756"
                    />

                    <x-forms.input 
                        name="fcm_app_id" 
                        :label="app()->getLocale() == 'ar' ? 'معرّف التطبيق (Web App ID)' : 'Firebase Web App ID (appId)'" 
                        :value="$settings['fcm_app_id'] ?? ''" 
                        placeholder="1:1029384756:web:..."
                    />
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <x-forms.input 
                        name="fcm_measurement_id" 
                        :label="app()->getLocale() == 'ar' ? 'معرّف القياس (Measurement ID)' : 'Analytics / Measurement ID (measurementId)'" 
                        :value="$settings['fcm_measurement_id'] ?? ''" 
                        placeholder="G-JKHXY1LDD8"
                    />
                </div>

                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                        <span class="text-xs font-bold" style="color: var(--color-text);">
                            {{ app()->getLocale() == 'ar' ? 'مفتاح شهادة الويب العامة (Web Push VAPID Public Key)' : 'Web Push VAPID Public Key' }}
                        </span>
                        @if(empty($settings['fcm_vapid_key']))
                            <span class="badge badge-warning text-[11px] font-bold" style="background: rgba(245, 158, 11, 0.15); color: #D97706; padding: 0.2rem 0.6rem; border-radius: 9999px;">
                                <i class="fa-solid fa-triangle-exclamation mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'مطلوب لتوليد توكن المتصفح' : 'Required for Browser Push Token' }}
                            </span>
                        @endif
                    </div>
                    <x-forms.input 
                        name="fcm_vapid_key" 
                        :value="$settings['fcm_vapid_key'] ?? ''" 
                        placeholder="e.g. BOnv9Kj8a..."
                    />
                    <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.4rem; line-height: 1.4;">
                        {{ app()->getLocale() == 'ar' ? 'يتم استخراجه من Firebase Console > Project Settings > Cloud Messaging > Web configuration > Generate key pair. بدون هذا المفتاح يرفض المتصفح إصدار توكن الإشعارات السحابية.' : 'Generated in Firebase Console > Project Settings > Cloud Messaging > Web configuration > Generate key pair. Without this key, browsers cannot issue a device push token.' }}
                    </div>
                </div>
            </div>

            <!-- Card 2: Real-Time Event Triggers -->
            <div class="card" style="padding: 2rem; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 800; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-bell-concierge text-primary"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'أحداث التنبيه التلقائي للمخزون (Operational Event Triggers)' : 'Automated Operational Event Triggers' }}</span>
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <x-forms.toggle 
                        name="fcm_notify_low_stock" 
                        :label="app()->getLocale() == 'ar' ? 'تنبيهات انخفاض رصيد المخزون عن حد الأمان (Low Stock Alerts)' : 'Low Stock Alerts (Below Buffer Threshold)'" 
                        :description="app()->getLocale() == 'ar' ? 'إرسال إشعار فوري لحظي للمدراء عند وصول رصيد أي منتج لحد إعادة الطلب.' : 'Broadcast real-time push alert when any product stock drops to or below its minimum threshold.'" 
                        :checked="$settings['fcm_notify_low_stock'] ?? true" 
                    />

                    <x-forms.toggle 
                        name="fcm_notify_out_stock" 
                        :label="app()->getLocale() == 'ar' ? 'تنبيهات نفاد المخزون بالكامل (Out of Stock Critical Alerts)' : 'Out of Stock Depleted Alerts (0 Units)'" 
                        :description="app()->getLocale() == 'ar' ? 'إشعار فوري عاجل باللون الأحمر عند وصول رصيد الصنف إلى 0 في أي موقع أو مستودع.' : 'Urgent critical alert triggered immediately when any item stock reaches 0 units.'" 
                        :checked="$settings['fcm_notify_out_stock'] ?? true" 
                    />

                    <x-forms.toggle 
                        name="fcm_notify_transfers" 
                        :label="app()->getLocale() == 'ar' ? 'حركات نقل وتوزيع المخزون (Stock Transfers)' : 'Stock Transfers & Distribution Movements'" 
                        :description="app()->getLocale() == 'ar' ? 'تنبيه مباشر عند إنشاء أمر نقل بين المستودع المركزي والمعارض أو العيادات.' : 'Push notification when stock is transferred between warehouse depots and clinic boutiques.'" 
                        :checked="$settings['fcm_notify_transfers'] ?? true" 
                    />

                    <x-forms.toggle 
                        name="fcm_notify_issues" 
                        :label="app()->getLocale() == 'ar' ? 'مشاكل وتوالف المنتجات (Damaged & Expired Issues)' : 'Product Issues (Damaged, Expired & Discrepancies)'" 
                        :description="app()->getLocale() == 'ar' ? 'إشعار المدراء فور تسجيل بضاعة تالفة، منتهية الصلاحية، أو فروقات جرد.' : 'Instant notification when damaged, expired, or discrepancy issues are reported.'" 
                        :checked="$settings['fcm_notify_issues'] ?? true" 
                    />

                    <x-forms.toggle 
                        name="fcm_sound_enabled" 
                        :label="app()->getLocale() == 'ar' ? 'تشغيل نغمة صوتية عند استلام الإشعار (Acoustic Audio Chime)' : 'Play Acoustic Chime Upon Push Arrival'" 
                        :description="app()->getLocale() == 'ar' ? 'تشغيل نغمة ويب صوتية نقية عند استلام أي إشعار لحظي في لوحة التحكم.' : 'Plays a distinct synthetic Web Audio chime when a push notification is received.'" 
                        :checked="$settings['fcm_sound_enabled'] ?? true" 
                    />
                </div>
            </div>

            <!-- Card 3: Interactive Live Push Tester -->
            <div class="card" style="padding: 2rem; background: var(--color-bg-subtle); border: 1px solid rgba(10, 79, 120, 0.2);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 800; margin: 0; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-vial-circle-check text-emerald-500"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'أداة الفحص والاختبار المباشر لإشعارات FCM (Live Push Tester)' : 'Live FCM Push Notification Tester' }}</span>
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                            {{ app()->getLocale() == 'ar' ? 'أرسل إشعاراً تجريبياً حياً لاختبار وصول الإشعار وصوت التنبيه وشريط التوست والربط السحابي فوراً.' : 'Dispatch a customized test notification to verify real-time arrival, audio feedback, and dropdown sync.' }}
                        </p>
                    </div>

                    <!-- Device Token Pill -->
                    <div id="fcmTokenBadgeContainer" style="display: flex; align-items: center; gap: 0.5rem;">
                        @php
                            $myToken = auth()->user()?->fcm_token;
                        @endphp
                        @if($myToken)
                            <span class="badge badge-success text-xs font-bold" style="padding: 0.35rem 0.65rem;">
                                <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'جهازك متصل بـ FCM' : 'Device FCM Linked' }}
                            </span>
                            <button type="button" class="btn btn-outline btn-xs" onclick="copyFcmToken('{{ $myToken }}')">
                                <i class="fa-regular fa-copy mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'نسخ التوكن' : 'Copy Token' }}
                            </button>
                        @else
                            <span class="badge badge-neutral text-xs" style="padding: 0.35rem 0.65rem;">
                                <i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'بانتظار توكن المتصفح' : 'Awaiting Browser Token' }}
                            </span>
                            <button type="button" class="btn btn-primary btn-xs" onclick="window.activateFcmTokenDirectly(this)">
                                <i class="fa-solid fa-bell mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'تفعيل وتوليد التوكن' : 'Activate & Link' }}
                            </button>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() == 'ar' ? 'عنوان الإشعار التجريبي' : 'Notification Title' }}
                        </label>
                        <input type="text" id="testFcmTitle" class="form-control text-sm" value="BlueZone Realtime Push Alert">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() == 'ar' ? 'نوع التنبيه (Event Category)' : 'Notification Category' }}
                        </label>
                        <select id="testFcmType" class="form-control text-sm">
                            <option value="stock">{{ app()->getLocale() == 'ar' ? 'تنبيه مخزون (Low / Out Stock)' : 'Stock Alert' }}</option>
                            <option value="transfer">{{ app()->getLocale() == 'ar' ? 'حركة نقل مخزون (Stock Transfer)' : 'Stock Transfer' }}</option>
                            <option value="issue">{{ app()->getLocale() == 'ar' ? 'مشكلة منتج أو تلف (Product Issue)' : 'Product Issue' }}</option>
                            <option value="order">{{ app()->getLocale() == 'ar' ? 'طلب جديد (New Order)' : 'New Order' }}</option>
                            <option value="system" selected>{{ app()->getLocale() == 'ar' ? 'نظام إداري عام (System Notice)' : 'System Notice' }}</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() == 'ar' ? 'نص رسالة الإشعار' : 'Notification Message / Body' }}
                    </label>
                    <textarea id="testFcmMessage" class="form-control text-sm" rows="2">تم تأكيد استلام إشعار تجريبي فوري بنجاح. Real-time push notification delivered successfully to BlueZone Admin Console.</textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                    <div>
                        <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() == 'ar' ? 'وجهة الإرسال (Target)' : 'Delivery Target' }}
                        </label>
                        <select id="testFcmTarget" class="form-control text-sm" onchange="toggleCustomTokenInput(this.value)">
                            <option value="self">{{ app()->getLocale() == 'ar' ? 'متصفحي الحالي (Current Active Browser)' : 'Current Active Device' }}</option>
                            <option value="all_admins">{{ app()->getLocale() == 'ar' ? 'جميع مدراء النظام (All Administrators)' : 'All Administrators' }}</option>
                            <option value="token">{{ app()->getLocale() == 'ar' ? 'رمز توكن محدد (Specific FCM Token)' : 'Custom Token' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() == 'ar' ? 'رابط الإجراء عند الضغط (Action URL)' : 'Click Action URL' }}
                        </label>
                        <input type="text" id="testFcmActionUrl" class="form-control text-sm" value="/admin/inventory">
                    </div>
                </div>

                <div id="customTokenWrapper" style="display: none; margin-bottom: 1.5rem;">
                    <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() == 'ar' ? 'رمز الجهاز المستهدف (Device FCM Token)' : 'Target Device FCM Token' }}
                    </label>
                    <input type="text" id="testFcmCustomToken" class="form-control text-sm" placeholder="e.g. fD-12948... (paste token here)">
                </div>

                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <button type="button" id="btnDispatchFcmTest" class="btn btn-primary" onclick="runLiveFcmTest()" style="background: linear-gradient(135deg, #0A4F78, #0284C7); border: none; font-weight: 800; padding: 0.75rem 1.5rem;">
                        <i class="fa-solid fa-paper-plane mr-1.5 ml-1.5"></i>
                        {{ app()->getLocale() == 'ar' ? 'إرسال إشعار تجريبي فوري الآن (Dispatch Push)' : 'Dispatch Live FCM Test Push' }}
                    </button>
                    <span id="fcmTestStatus" class="text-xs text-muted font-bold"></span>
                </div>

                <!-- Live Diagnostic Log Box -->
                <div id="fcmLogTerminal" style="display: none; margin-top: 1.5rem; background: #031827; color: #38BDF8; font-family: monospace; font-size: 0.775rem; padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(56, 189, 248, 0.2); max-height: 200px; overflow-y: auto;">
                    <div style="color: #94A3B8; margin-bottom: 0.35rem;">// FCM Dispatcher Diagnostic Log:</div>
                    <pre id="fcmLogContent" style="margin: 0; white-space: pre-wrap; word-break: break-all;"></pre>
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('admin.settings.save_settings') }}
            </button>
        </div>

    </form>

    <script>
        // ==========================================
        // LANDING PAGE SECTIONS BUILDER & REORDERING
        // ==========================================
        window.defaultLandingSectionsOrder = [
            'hero_slider',
            'who_we_are',
            'philosophy',
            'new_arrivals',
            'featured_products',
            'products_vertical',
            'blue_mind_flagship',
            'five_blue_zones',
            'bluemint_preps',
            'our_science',
            'journal_news',
            'final_cta'
        ];

        window.updateLandingSectionsOrderInput = function() {
            const container = document.getElementById('landing_sections_sortable_container');
            if (!container) return;
            const rows = container.querySelectorAll('.landing-section-row');
            const order = [];
            let activeCount = 0;
            
            rows.forEach((row, index) => {
                const key = row.getAttribute('data-section-key');
                if (key) order.push(key);
                
                // Update number badge
                const numSpan = row.querySelector('.order-num');
                if (numSpan) numSpan.textContent = (index + 1);
                
                // Check active toggle
                const checkbox = row.querySelector('.landing-section-toggle-input');
                if (checkbox && checkbox.checked) activeCount++;
            });
            
            const input = document.getElementById('landing_sections_order_input');
            if (input) input.value = JSON.stringify(order);
            
            const badge = document.getElementById('active_sections_count_badge');
            if (badge) {
                const isAr = document.documentElement.lang === 'ar' || document.dir === 'rtl';
                badge.textContent = `${activeCount} / ${rows.length} ${isAr ? 'أقسام مفعّلة' : 'Sections Active'}`;
            }
        };

        window.moveLandingSectionRow = function(btn, direction) {
            const row = btn.closest('.landing-section-row');
            if (!row) return;
            const container = row.parentElement;
            
            if (direction === -1 && row.previousElementSibling) {
                container.insertBefore(row, row.previousElementSibling);
                window.updateLandingSectionsOrderInput();
                row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else if (direction === 1 && row.nextElementSibling) {
                container.insertBefore(row.nextElementSibling, row);
                window.updateLandingSectionsOrderInput();
                row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        };

        window.handleLandingSectionToggle = function(input) {
            const row = input.closest('.landing-section-row');
            if (!row) return;
            const isChecked = input.checked;
            
            const statusText = row.querySelector('.status-indicator-text');
            const isAr = document.documentElement.lang === 'ar' || document.dir === 'rtl';
            if (statusText) {
                statusText.textContent = isChecked 
                    ? (isAr ? 'مفعّل (ON)' : 'ON / Visible') 
                    : (isAr ? 'معطّل (OFF)' : 'OFF / Hidden');
                statusText.style.color = isChecked ? '#10B981' : 'var(--color-text-muted)';
            }
            
            const slider = row.querySelector('.slider-round');
            const dot = row.querySelector('.slider-dot');
            if (slider) slider.style.backgroundColor = isChecked ? '#10B981' : '#cbd5e1';
            if (dot) dot.style.left = isChecked ? '23px' : '3px';
            
            row.style.opacity = isChecked ? '1' : '0.65';
            row.style.borderColor = isChecked ? 'rgba(10, 79, 120, 0.2)' : 'var(--color-border)';
            
            window.updateLandingSectionsOrderInput();
            
            if (window.toast) {
                window.toast.info(isChecked 
                    ? (isAr ? 'تم تفعيل ظهور القسم بالصفحة الرئيسية' : 'Section Enabled on Storefront') 
                    : (isAr ? 'تم إخفاء القسم من الصفحة الرئيسية' : 'Section Hidden from Storefront'));
            }
        };

        window.enableAllLandingSections = function(enable) {
            const container = document.getElementById('landing_sections_sortable_container');
            if (!container) return;
            const checkboxes = container.querySelectorAll('.landing-section-toggle-input');
            checkboxes.forEach(cb => {
                cb.checked = enable;
                window.handleLandingSectionToggle(cb);
            });
        };

        window.resetLandingSectionsOrder = function() {
            const container = document.getElementById('landing_sections_sortable_container');
            if (!container) return;
            
            window.defaultLandingSectionsOrder.forEach(key => {
                const row = container.querySelector(`.landing-section-row[data-section-key="${key}"]`);
                if (row) container.appendChild(row);
            });
            
            window.updateLandingSectionsOrderInput();
            const isAr = document.documentElement.lang === 'ar' || document.dir === 'rtl';
            if (window.toast) {
                window.toast.success(isAr ? 'تم استعادة الترتيب الافتراضي للأقسام بنجاح!' : 'Landing page default section order restored!');
            }
        };

        // Initialize Drag & Drop
        document.addEventListener('DOMContentLoaded', function() {
            let draggedRow = null;
            const container = document.getElementById('landing_sections_sortable_container');
            if (!container) return;
            
            container.addEventListener('dragstart', (e) => {
                const row = e.target.closest('.landing-section-row');
                if (!row) return;
                draggedRow = row;
                row.style.opacity = '0.35';
                e.dataTransfer.effectAllowed = 'move';
            });
            
            container.addEventListener('dragend', (e) => {
                const row = e.target.closest('.landing-section-row');
                if (row) {
                    const isChecked = row.querySelector('.landing-section-toggle-input')?.checked;
                    row.style.opacity = isChecked ? '1' : '0.65';
                }
                draggedRow = null;
                window.updateLandingSectionsOrderInput();
            });
            
            container.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                const targetRow = e.target.closest('.landing-section-row');
                if (targetRow && targetRow !== draggedRow) {
                    const rect = targetRow.getBoundingClientRect();
                    const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                    container.insertBefore(draggedRow, next ? targetRow.nextSibling : targetRow);
                }
            });
        });

        async function testWebhookPing(gateway) {
            try {
                if (window.toast) window.toast.info('Pinging webhook endpoint simulator...');
                const res = await fetch('{{ route('payment.webhook.simulate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ gateway: gateway, order_number: 'BZ-TEST-PING' })
                });
                const data = await res.json();
                if (data.received) {
                    if (window.toast) {
                        window.toast.success('Webhook Ping Successful! Event: ' + data.event + ' (' + data.status + ')');
                    } else {
                        alert('Webhook Ping Successful! Event: ' + data.event);
                    }
                } else {
                    if (window.toast) {
                        window.toast.error('Webhook Ping Error: ' + (data.error || 'Check logs'));
                    } else {
                        alert('Webhook Error: ' + data.error);
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.toast) window.toast.error('Connection failed: ' + err.message);
            }
        }

        /* =========================================================================
           FCM Live Notification Tester & Diagnostics
           ========================================================================= */
        window.toggleCustomTokenInput = function(targetVal) {
            const wrapper = document.getElementById('customTokenWrapper');
            if (wrapper) {
                wrapper.style.display = targetVal === 'token' ? 'block' : 'none';
            }
        };

        window.copyFcmToken = function(token) {
            if (!token) return;
            navigator.clipboard.writeText(token).then(() => {
                if (window.toast) {
                    window.toast.success('تم نسخ توكن الجهاز إلى الحافظة بنجاح! FCM Device Token copied.');
                } else {
                    alert('FCM Device Token copied to clipboard!');
                }
            }).catch(() => {
                const el = document.createElement('textarea');
                el.value = token;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                alert('Token copied to clipboard!');
            });
        };

        window.runLiveFcmTest = function() {
            const btn = document.getElementById('btnDispatchFcmTest');
            const statusEl = document.getElementById('fcmTestStatus');
            const terminal = document.getElementById('fcmLogTerminal');
            const logContent = document.getElementById('fcmLogContent');

            const title = document.getElementById('testFcmTitle')?.value || 'Test Notification';
            const message = document.getElementById('testFcmMessage')?.value || '';
            const type = document.getElementById('testFcmType')?.value || 'system';
            const target = document.getElementById('testFcmTarget')?.value || 'self';
            const actionUrl = document.getElementById('testFcmActionUrl')?.value || '/admin/inventory';
            const targetToken = document.getElementById('testFcmCustomToken')?.value || '';

            if (target === 'self' && !window.currentAdminFcmToken && document.querySelector('#fcmTokenBadgeContainer .badge-neutral')) {
                if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
                    if (statusEl) {
                        statusEl.textContent = 'بانتظار الموافقة على إذن المتصفح لتوليد التوكن...';
                        statusEl.style.color = '#F59E0B';
                    }
                    window.activateFcmTokenDirectly(btn);
                    return;
                }
            }

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1 ml-1"></i> <span>جاري الإرسال عبر FCM...</span>';
            }
            if (statusEl) {
                statusEl.textContent = 'جاري الإرسال والمعالجة...';
                statusEl.style.color = '#0284C7';
            }

            const startTime = performance.now();

            fetch('/admin/notifications/test-push', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    title: title,
                    message: message,
                    type: type,
                    target: target,
                    action_url: actionUrl,
                    target_token: targetToken,
                    client_token: window.currentAdminFcmToken || ''
                })
            })
            .then(res => res.json())
            .then(data => {
                const latency = Math.round(performance.now() - startTime);

                if (terminal && logContent) {
                    terminal.style.display = 'block';
                    logContent.textContent = JSON.stringify({
                        timestamp: new Date().toISOString(),
                        latency_ms: latency,
                        response: data
                    }, null, 2);
                }

                if (data.success) {
                    if (statusEl) {
                        if (data.details && data.details.reason === 'no_token') {
                            statusEl.innerHTML = `<i class="fa-solid fa-circle-check"></i> تم تسجيل الإشعار بالنظام بنجاح (${latency}ms) — [بانتظار تفعيل توكن المتصفح]`;
                            statusEl.style.color = '#F59E0B';
                        } else {
                            statusEl.innerHTML = `<i class="fa-solid fa-circle-check"></i> تم الإرسال السحابي بنجاح (${latency}ms)`;
                            statusEl.style.color = '#10B981';
                        }
                    }

                    // Trigger client toast and audio chime
                    if (typeof showAdminToast === 'function') {
                        showAdminToast(data.title, data.body, data.icon, data.action_url);
                    }
                    if (typeof prependNotificationToDropdown === 'function') {
                        prependNotificationToDropdown(data.title, data.body, data.icon, data.action_url);
                    }
                } else {
                    if (statusEl) {
                        statusEl.innerHTML = `<i class="fa-solid fa-circle-xmark"></i> فشل الإرسال: ${data.message || 'خطأ غير معروف'}`;
                        statusEl.style.color = '#EF4444';
                    }
                }
            })
            .catch(err => {
                console.error('FCM Test Error:', err);
                if (statusEl) {
                    statusEl.innerHTML = `<i class="fa-solid fa-circle-xmark"></i> تعذر الاتصال: ${err.message}`;
                    statusEl.style.color = '#EF4444';
                }
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-1.5 ml-1.5"></i> {{ app()->getLocale() == "ar" ? "إرسال إشعار تجريبي فوري الآن (Dispatch Push)" : "Dispatch Live FCM Test Push" }}';
                }
            });
        };

        
        // Dynamic Currency Live Preview Synchronizer
        const currMap = {
            'SAR': { sym_ar: 'ر.س', sym_en: 'SAR', pos: 'after' },
            'USD': { sym_ar: '$', sym_en: '$', pos: 'before' },
            'AED': { sym_ar: 'د.إ', sym_en: 'AED', pos: 'after' },
            'EUR': { sym_ar: '€', sym_en: '€', pos: 'before' },
            'GBP': { sym_ar: '£', sym_en: '£', pos: 'before' },
            'KWD': { sym_ar: 'د.ك', sym_en: 'KWD', pos: 'after' },
            'QAR': { sym_ar: 'ر.ق', sym_en: 'QAR', pos: 'after' },
            'BHD': { sym_ar: 'د.ب', sym_en: 'BHD', pos: 'after' },
            'OMR': { sym_ar: 'ر.ع', sym_en: 'OMR', pos: 'after' },
            'EGP': { sym_ar: 'ج.م', sym_en: 'EGP', pos: 'after' }
        };

        function updateCurrencyPreview() {
            const code = document.getElementById('currency_code_select')?.value || 'SAR';
            const posSetting = document.getElementById('currency_position_select')?.value || 'auto';
            const dec = parseInt(document.getElementById('currency_decimals_select')?.value || '2', 10);
            const customSym = document.getElementById('currency_symbol_override')?.value?.trim();

            const curr = currMap[code] || { sym_ar: code, sym_en: code, pos: 'after' };
            const num = (245.50).toFixed(dec);

            const symAr = customSym || curr.sym_ar;
            const symEn = customSym || curr.sym_en;

            const pos = (posSetting === 'auto') ? curr.pos : posSetting;

            const textAr = (pos === 'before') ? (symAr + num) : (num + ' ' + symAr);
            const textEn = (pos === 'before') ? (symEn + (symEn.length > 1 ? ' ' : '') + num) : (num + ' ' + symEn);

            const prevAr = document.getElementById('preview_currency_ar');
            const prevEn = document.getElementById('preview_currency_en');
            if (prevAr) prevAr.textContent = textAr;
            if (prevEn) prevEn.textContent = textEn;
        }

        ['currency_code_select', 'currency_position_select', 'currency_decimals_select', 'currency_symbol_override'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', updateCurrencyPreview);
                el.addEventListener('input', updateCurrencyPreview);
            }
        });

        // Dynamic Social Media Preview in Admin
        document.querySelectorAll('.social-setting-input').forEach(input => {
            const updateBadge = () => {
                const targetId = input.getAttribute('data-target');
                const badge = document.getElementById(targetId);
                if (!badge) return;
                const val = input.value.trim();
                const activeColor = badge.getAttribute('data-active-color') || '#0A4F78';
                if (val.length > 0) {
                    badge.style.background = '#031827';
                    badge.style.color = activeColor;
                    badge.style.border = '1px solid rgba(10,79,120,0.5)';
                    badge.style.opacity = '1';
                    badge.style.transform = 'scale(1.05)';
                } else {
                    badge.style.background = 'rgba(0,0,0,0.05)';
                    badge.style.color = '#94A3B8';
                    badge.style.border = '1px dashed rgba(0,0,0,0.15)';
                    badge.style.opacity = '0.45';
                    badge.style.transform = 'scale(1)';
                }
            };
            input.addEventListener('input', updateBadge);
            input.addEventListener('change', updateBadge);
        });

        // Open specific tab from URL hash if provided (e.g. #tab-fcm)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash) {
                const tabId = window.location.hash.replace('#', '');
                const targetBtn = document.querySelector(`[data-tab-target="${tabId}"]`);
                if (targetBtn) {
                    targetBtn.click();
                }
            }
        });
    </script>
</x-layouts.admin>


