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
                {{ __('admin.settings.tabs.general') }}
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-landing">
                🌐 {{ __('admin.settings.tabs.landing') }}
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-commerce">
                {{ __('admin.settings.tabs.commerce') }}
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-store">
                {{ __('admin.settings.tabs.store') }}
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-shipping">
                {{ __('admin.settings.tabs.shipping') }}
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-typography">
                🔤 {{ __('admin.settings.tabs.typography') ?? 'Typography & Fonts' }}
            </button>
            <button type="button" class="tab-btn" data-tab-target="tab-alerts">
                {{ __('admin.settings.tabs.alerts') }}
            </button>
        </div>

        <!-- Tab 1: General -->
        <div id="tab-general" data-tab-content="admin-settings" style="display: block;">
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    {{ __('admin.settings.sections.general_brand') }}
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <x-forms.input name="site_name" :label="__('admin.settings.fields.site_name')" :value="$settings['site_name'] ?? $settings['store_name'] ?? 'BLUE ZONE™ Longevity & Cellular Health'" required />
                    <x-forms.input name="tagline" :label="__('admin.settings.fields.tagline')" :value="$settings['tagline'] ?? 'Cellular Longevity & Botanical Medicine'" required />
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                    <x-forms.select 
                        name="default_language" 
                        :label="__('admin.settings.fields.default_language')" 
                        :selected="$settings['default_language'] ?? $settings['default_locale'] ?? 'en'"
                        :options="['en' => 'English (LTR)', 'ar' => 'العربية (RTL)']" 
                    />
                    <x-forms.input name="currency" :label="__('admin.settings.fields.currency')" :value="$settings['currency'] ?? $settings['default_currency'] ?? 'USD'" required />
                    <x-forms.input name="timezone" :label="__('admin.settings.fields.timezone')" :value="$settings['timezone'] ?? 'Asia/Riyadh'" required />
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
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

                    <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 1.5rem;">
                        <x-forms.select 
                            name="whatsapp_position" 
                            :label="app()->getLocale() == 'ar' ? 'موضع الأيقونة (Position & RTL/LTR)' : 'Widget Position & Alignment'" 
                            :selected="$settings['whatsapp_position'] ?? 'auto'"
                            :options="[
                                'auto' => app()->getLocale() == 'ar' ? '🌐 تلقائي (يمين بالإنجليزي / يسار بالعربي)' : '🌐 Auto (LTR: Right / RTL: Left)',
                                'bottom_right' => app()->getLocale() == 'ar' ? '👉 أسفل اليمين دائماً (Bottom-Right)' : '👉 Always Bottom-Right',
                                'bottom_left' => app()->getLocale() == 'ar' ? '👈 أسفل اليسار دائماً (Bottom-Left)' : '👈 Always Bottom-Left',
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
                            {{ app()->getLocale() == 'ar' ? '🎛️ مصمم وترتيب وتفعيل أقسام الصفحة الرئيسية' : '🎛️ Landing Page Sections Ordering & Master Visibility Builder' }}
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

                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 1.25rem;">
                            <x-forms.select 
                                name="payment_stripe_mode" 
                                :label="app()->getLocale() == 'ar' ? 'بيئة التشغيل (Mode)' : 'Gateway Mode'" 
                                :selected="$settings['payment_stripe_mode'] ?? 'test'"
                                :options="[
                                    'test' => '🧪 Sandbox / Test Mode (No Real Charges)',
                                    'live' => '🚀 Production / Live Mode (Real Transactions)'
                                ]" 
                            />

                            <x-forms.input 
                                name="payment_stripe_public_key" 
                                :label="app()->getLocale() == 'ar' ? 'المفتاح العام (Publishable Key)' : 'Stripe Publishable Key'" 
                                :value="$settings['payment_stripe_public_key'] ?? config('payment.gateways.stripe.public_key', '')" 
                                placeholder="pk_test_... or pk_live_..."
                            />
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.25rem;">
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

        <!-- Tab: Typography & Fonts -->
        <div id="tab-typography" data-tab-content="admin-settings" style="display: none;">
            <div class="card" style="padding: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.25rem;">
                            🔤 {{ app()->getLocale() == 'ar' ? 'الخطوط والطباعة للنظام بالكامل' : 'System-Wide Typography & Font Styles' }}
                        </h3>
                        <p style="font-size: 0.875rem; color: var(--color-text-muted); margin: 0;">
                            {{ app()->getLocale() == 'ar' ? 'التحكم في خط الموقع والمتجر ولوحة التحكم مع المعاينة الحية الفورية.' : 'Control typography across customer storefront, custom admin, and Filament.' }}
                        </p>
                    </div>

                    <a href="{{ route('filament.admin.pages.manage-typography') }}" class="btn btn-primary" target="_blank" style="background: linear-gradient(135deg, #0A4F78, #2A8FC2); border: none; font-weight: 800;">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1.5 ml-1.5"></i>
                        {{ app()->getLocale() == 'ar' ? 'المعاينة الحية الفورية في لوحة فيلامنت' : 'Open Live Interactive Customizer in Filament' }}
                    </a>
                </div>

                @php
                    $availableFonts = \App\Services\TypographyService::getAvailableFonts();
                    $activeConfig = \App\Services\TypographyService::getActiveConfig();
                @endphp

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <x-forms.select 
                        name="font_family" 
                        :label="app()->getLocale() == 'ar' ? 'الخط الأساسي للنظام (Primary Font)' : 'Primary System Font'" 
                        :selected="$settings['font_family'] ?? $activeConfig['font_family']"
                        :options="collect($availableFonts)->mapWithKeys(fn($f, $k) => [$k => $f['label'] . ' — ' . $f['category']])->toArray()" 
                    />

                    <x-forms.select 
                        name="font_heading_family" 
                        :label="app()->getLocale() == 'ar' ? 'خط العناوين الرئيسية (Headings Font)' : 'Headings & Brand Font'" 
                        :selected="$settings['font_heading_family'] ?? $activeConfig['font_heading_family']"
                        :options="collect($availableFonts)->mapWithKeys(fn($f, $k) => [$k => $f['label']])->toArray()" 
                    />
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                    <x-forms.select 
                        name="font_size_base" 
                        :label="app()->getLocale() == 'ar' ? 'حجم الخط الأساسي' : 'Base Font Size'" 
                        :selected="$settings['font_size_base'] ?? $activeConfig['font_size_base']"
                        :options="['14px' => '14px (Compact)', '15px' => '15px (Medium)', '16px' => '16px (Standard / Recommended)', '17px' => '17px (Spacious)', '18px' => '18px (Large)']" 
                    />

                    <x-forms.select 
                        name="font_weight_headings" 
                        :label="app()->getLocale() == 'ar' ? 'سُمك العناوين (Weight)' : 'Headings Weight'" 
                        :selected="$settings['font_weight_headings'] ?? $activeConfig['font_weight_headings']"
                        :options="['500' => 'Medium (500)', '600' => 'Semi-Bold (600)', '700' => 'Bold (700)', '800' => 'Extra-Bold (800)', '900' => 'Black (900)']" 
                    />

                    <x-forms.select 
                        name="font_weight_body" 
                        :label="app()->getLocale() == 'ar' ? 'سُمك نصوص المحتوى' : 'Body Text Weight'" 
                        :selected="$settings['font_weight_body'] ?? $activeConfig['font_weight_body']"
                        :options="['300' => 'Light (300)', '400' => 'Regular (400)', '500' => 'Medium (500)']" 
                    />
                </div>

                <!-- Preview Banner inside custom admin -->
                <div style="margin-top: 1.5rem; background: rgba(10, 79, 120, 0.05); border: 1px dashed var(--bz-accent-blue); padding: 1.25rem; border-radius: var(--radius-lg);">
                    <div style="font-size: 0.8125rem; font-weight: 700; color: var(--color-primary); margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-eye mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'معاينة الخط النشط حالياً:' : 'Active Applied Font:' }} {{ $activeConfig['font_family'] }}
                    </div>
                    <p style="margin: 0; font-size: 1.125rem; font-weight: 700;">
                        {{ app()->getLocale() == 'ar' ? 'بلوزون — التجربة الاستثنائية للصحة الخلوية وحلول إطالة العمر.' : 'BLUE ZONE — Peak Cellular Longevity & Botanical Medicine.' }}
                    </p>
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
    </script>
</x-layouts.admin>
