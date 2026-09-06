<x-layouts.admin 
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
</x-layouts.admin>
