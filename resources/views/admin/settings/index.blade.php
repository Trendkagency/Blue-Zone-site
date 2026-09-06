<x-layouts.admin 
<<<<<<< HEAD
    :pageTitle="__('admin.menu.settings')" 
    pageSubtitle="Configure enterprise store parameters, currencies, low-stock thresholds, tax, and notifications."
    :breadcrumbs="['Settings' => route('admin.settings.index')]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-primary" onclick="alert('Enterprise settings successfully updated!')">
            💾 {{ __('app.actions.save') }} Settings
        </button>
    </x-slot>

    <!-- Settings Tabs -->
    <div class="product-tabs-nav" data-tab-group="admin-settings" style="margin-bottom: 2rem;">
        <button type="button" class="tab-btn active" data-tab-target="tab-general">
            🏢 General & Brand
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-store">
            🏬 Store & Inventory
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-commerce">
            💳 Payments & Tax
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-shipping">
            🚚 Shipping & Logistics
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-alerts">
            🔔 Notification Triggers
        </button>
    </div>

    <!-- Tab 1: General -->
    <div id="tab-general" data-tab-content="admin-settings" style="display: block;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Enterprise Brand & Locale Defaults
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="site_name" label="Brand Name" :value="$settings['site_name']" required />
                <x-forms.input name="tagline" label="Official Brand Tagline" :value="$settings['tagline']" required />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                <x-forms.select 
                    name="default_language" 
                    label="Default Frontline Language" 
                    :selected="$settings['default_language']"
                    :options="['en' => 'English (LTR)', 'ar' => 'العربية (RTL)']" 
                />
                <x-forms.input name="currency" label="Commerce Currency" :value="$settings['currency']" required />
                <x-forms.input name="timezone" label="System Timezone" :value="$settings['timezone']" required />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                <x-forms.input name="contact_email" label="Customer Support Email" :value="$settings['contact_email']" required />
                <x-forms.input name="contact_phone" label="Concierge Hotline Phone" :value="$settings['contact_phone']" required />
            </div>
        </div>
    </div>

    <!-- Tab 2: Store & Inventory -->
    <div id="tab-store" data-tab-content="admin-settings" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Store Operations & Stock Thresholds
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input 
                    name="low_stock_threshold" 
                    type="number" 
                    label="Global Low Stock Threshold (Units)" 
                    :value="$settings['low_stock_threshold']" 
                    hint="When stock reaches this level, visual warnings and inventory lead alerts will trigger." 
                />

                <x-forms.select 
                    name="zero_stock_behavior" 
                    label="Depleted Stock (0 Units) Behavior" 
                    :selected="$settings['zero_stock_behavior']"
                    :options="[
                        'mark_out_of_stock' => 'Mark as Out of Stock (Disable Checkout)',
                        'allow_backorders' => 'Allow Clinical Pre-orders (Backorder)',
                        'hide_product' => 'Hide Product from Customer Catalog',
                    ]" 
                />
            </div>

            <div style="margin-top: 1.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                <x-forms.toggle 
                    name="enable_reviews" 
                    label="Enable Verified Customer Reviews" 
                    description="Allow customers who purchased protocols to submit verified ratings." 
                    :checked="$settings['enable_reviews']" 
                />

                <x-forms.toggle 
                    name="enable_coupons" 
                    label="Enable Promo & Coupon Engine" 
                    description="Permit discount codes during cart and checkout." 
                    :checked="$settings['enable_coupons']" 
                />
            </div>
        </div>
    </div>

    <!-- Tab 3: Payments & Tax -->
    <div id="tab-commerce" data-tab-content="admin-settings" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Tender Gateways & Tax Authority
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <x-forms.input name="tax_percentage" type="number" step="0.1" label="Standard VAT Rate (%)" :value="$settings['tax_percentage']" required />
                <x-forms.input name="tax_number" label="VAT / Tax Identification #" value="31004829100003" required />
            </div>

            <x-forms.toggle 
                name="enable_online_payment" 
                label="Enable Online Payment Gateway (Card, Apple Pay, Mada)" 
                description="Process real-time credit, debit, and Apple Pay transactions via secure TLS." 
                :checked="$settings['enable_online_payment']" 
            />

            <x-forms.toggle 
                name="enable_cod" 
                label="Enable Cash on Delivery (COD)" 
                description="Permit payment upon arrival with courier delivery." 
                :checked="$settings['enable_cod']" 
            />
        </div>
    </div>

    <!-- Tab 4: Shipping -->
    <div id="tab-shipping" data-tab-content="admin-settings" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Logistics & Shipping Rules
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input 
                    name="free_shipping_threshold" 
                    type="number" 
                    step="0.01" 
                    label="Complimentary Shipping Minimum ($)" 
                    :value="$settings['free_shipping_threshold']" 
                />

                <x-forms.input 
                    name="flat_shipping_rate" 
                    type="number" 
                    step="0.01" 
                    label="Standard Insured Shipping Flat Rate ($)" 
                    :value="$settings['flat_shipping_rate']" 
                />
            </div>
        </div>
    </div>

    <!-- Tab 5: Alerts -->
    <div id="tab-alerts" data-tab-content="admin-settings" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                System Event & Notification Triggers
            </h3>

            <x-forms.toggle 
                name="notify_low_stock" 
                label="Email Inventory Leads on Low Stock Warnings" 
                description="Immediately dispatch automated alert to inventory managers when threshold is breached." 
                :checked="$settings['notify_low_stock']" 
            />

            <x-forms.toggle 
                name="notify_new_order" 
                label="Dispatch New Online Order Notifications" 
                description="Alert fulfillment team upon verified payment capture." 
                :checked="$settings['notify_new_order']" 
            />
        </div>
    </div>
=======
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
            </div>
        </div>

        <!-- Tab 2: Payments & Taxes -->
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

                <div style="border-top: 1px solid var(--color-border); padding-top: 1.5rem; margin-top: 1rem;">
                    <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem;">{{ app()->getLocale() == 'ar' ? 'بوابات وطرق الدفع' : 'Payment Gateways' }}</h4>
                    
                    <x-forms.toggle 
                        name="enable_online_payment" 
                        :label="__('admin.settings.fields.enable_online_payment')" 
                        :description="app()->getLocale() == 'ar' ? 'معالجة بطاقات الائتمان ومدى وApple Pay عبر تشفير آمن TLS.' : 'Process real-time credit, debit, and Apple Pay transactions via secure TLS.'" 
                        :checked="$settings['enable_online_payment'] ?? true" 
                    />

                    <x-forms.toggle 
                        name="enable_cod" 
                        :label="__('admin.settings.fields.enable_cod')" 
                        :description="app()->getLocale() == 'ar' ? 'السماح للعملاء بالدفع نقداً أو عبر البطاقة عند استلام الشحنة.' : 'Permit payment upon arrival with courier delivery.'" 
                        :checked="$settings['enable_cod'] ?? true" 
                    />
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
>>>>>>> origin/main
</x-layouts.admin>
