<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\View\ViewModels\SettingViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $defaults = SettingViewModel::all();
        $saved = Setting::getAll();
        $settings = array_merge($defaults, $saved);

        $defaultOrder = $defaults['landing_sections_order'] ?? array_keys(SettingViewModel::landingSections());
        $configuredOrder = $settings['landing_sections_order'] ?? $defaultOrder;
        if (is_string($configuredOrder)) {
            $decoded = json_decode($configuredOrder, true);
            if (is_array($decoded)) {
                $configuredOrder = $decoded;
            }
        }
        if (!is_array($configuredOrder) || empty($configuredOrder)) {
            $configuredOrder = $defaultOrder;
        }

        $allKnownSections = array_keys(SettingViewModel::landingSections());
        $landingSectionsOrder = array_values(array_intersect($configuredOrder, $allKnownSections));
        $missingSections = array_diff($allKnownSections, $landingSectionsOrder);
        foreach ($missingSections as $missing) {
            $landingSectionsOrder[] = $missing;
        }

        return view('admin.settings.index', [
            'settings' => $settings,
            'landingSectionsOrder' => $landingSectionsOrder,
            'landingSectionsMeta' => SettingViewModel::landingSections(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // General
            'site_name' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'default_language' => ['nullable', 'string', 'in:en,ar'],
            'currency' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'enable_whatsapp' => ['nullable', 'boolean'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'whatsapp_default_message' => ['nullable', 'string', 'max:255'],
            'whatsapp_position' => ['nullable', 'string', 'in:auto,bottom_right,bottom_left'],

            // Typography & Fonts
            'font_family' => ['nullable', 'string', 'max:100'],
            'font_heading_family' => ['nullable', 'string', 'max:100'],
            'font_size_base' => ['nullable', 'string', 'max:20'],
            'font_weight_headings' => ['nullable', 'string', 'max:20'],
            'font_weight_body' => ['nullable', 'string', 'max:20'],

            // Store & Inventory
            'low_stock_threshold' => ['nullable', 'integer', 'min:1'],
            'zero_stock_behavior' => ['nullable', 'string', 'in:mark_out_of_stock,allow_backorders,hide_product'],
            'enable_reviews' => ['nullable', 'boolean'],
            'enable_coupons' => ['nullable', 'boolean'],

            // Payments & Tax Controls
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'tax_number' => ['required', 'string', 'max:50'],
            'enable_tax' => ['nullable', 'boolean'],
            'prices_include_tax' => ['nullable', 'boolean'],
            'enable_online_payment' => ['nullable', 'boolean'],
            'enable_cod' => ['nullable', 'boolean'],

            // Payment Gateways & Webhooks
            'payment_stripe_enabled' => ['nullable', 'boolean'],
            'payment_stripe_mode' => ['nullable', 'string', 'in:test,live'],
            'payment_stripe_public_key' => ['nullable', 'string', 'max:255'],
            'payment_stripe_secret_key' => ['nullable', 'string', 'max:255'],
            'payment_stripe_webhook_secret' => ['nullable', 'string', 'max:255'],
            'payment_cod_enabled' => ['nullable', 'boolean'],
            'payment_cod_extra_fee' => ['nullable', 'numeric', 'min:0'],
            'payment_default_gateway' => ['nullable', 'string', 'in:stripe,cod'],

            // Shipping
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'flat_shipping_rate' => ['nullable', 'numeric', 'min:0'],

            // Notifications & Audio
            'notify_low_stock' => ['nullable', 'boolean'],
            'notify_new_order' => ['nullable', 'boolean'],
            'toast_sound_enabled' => ['nullable', 'boolean'],

            // Landing Page Section Order
            'landing_sections_order' => ['nullable'],

            // Master Section Switches
            'landing_hero_slider_enabled' => ['nullable', 'boolean'],
            'landing_who_we_are_enabled' => ['nullable', 'boolean'],
            'landing_philosophy_enabled' => ['nullable', 'boolean'],
            'landing_new_arrivals_enabled' => ['nullable', 'boolean'],
            'landing_featured_products_enabled' => ['nullable', 'boolean'],
            'landing_products_vertical_enabled' => ['nullable', 'boolean'],
            'landing_blue_mind_flagship_enabled' => ['nullable', 'boolean'],
            'landing_five_blue_zones_enabled' => ['nullable', 'boolean'],
            'landing_bluemint_preps_enabled' => ['nullable', 'boolean'],
            'landing_our_science_enabled' => ['nullable', 'boolean'],
            'landing_journal_news_enabled' => ['nullable', 'boolean'],
            'landing_final_cta_enabled' => ['nullable', 'boolean'],

            // Landing Page Detailed Elements
            'landing_announcement_enabled' => ['nullable', 'boolean'],
            'landing_announcement_badge_en' => ['nullable', 'string', 'max:255'],
            'landing_announcement_badge_ar' => ['nullable', 'string', 'max:255'],
            'landing_announcement_text_en' => ['nullable', 'string', 'max:500'],
            'landing_announcement_text_ar' => ['nullable', 'string', 'max:500'],
            'landing_announcement_link' => ['nullable', 'string', 'max:255'],

            'landing_hero_badge_en' => ['nullable', 'string', 'max:255'],
            'landing_hero_badge_ar' => ['nullable', 'string', 'max:255'],
            'landing_hero_title_en' => ['nullable', 'string', 'max:255'],
            'landing_hero_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_hero_subtitle_en' => ['nullable', 'string', 'max:1000'],
            'landing_hero_subtitle_ar' => ['nullable', 'string', 'max:1000'],
            'landing_hero_cta_primary_text_en' => ['nullable', 'string', 'max:100'],
            'landing_hero_cta_primary_text_ar' => ['nullable', 'string', 'max:100'],
            'landing_hero_cta_primary_link' => ['nullable', 'string', 'max:255'],
            'landing_hero_cta_secondary_text_en' => ['nullable', 'string', 'max:100'],
            'landing_hero_cta_secondary_text_ar' => ['nullable', 'string', 'max:100'],
            'landing_hero_cta_secondary_link' => ['nullable', 'string', 'max:255'],

            'landing_stats_enabled' => ['nullable', 'boolean'],
            'landing_stat_1_val' => ['nullable', 'string', 'max:50'],
            'landing_stat_1_label_en' => ['nullable', 'string', 'max:255'],
            'landing_stat_1_label_ar' => ['nullable', 'string', 'max:255'],
            'landing_stat_2_val' => ['nullable', 'string', 'max:50'],
            'landing_stat_2_label_en' => ['nullable', 'string', 'max:255'],
            'landing_stat_2_label_ar' => ['nullable', 'string', 'max:255'],
            'landing_stat_3_val' => ['nullable', 'string', 'max:50'],
            'landing_stat_3_label_en' => ['nullable', 'string', 'max:255'],
            'landing_stat_3_label_ar' => ['nullable', 'string', 'max:255'],
            'landing_stat_4_val' => ['nullable', 'string', 'max:50'],
            'landing_stat_4_label_en' => ['nullable', 'string', 'max:255'],
            'landing_stat_4_label_ar' => ['nullable', 'string', 'max:255'],

            'landing_philosophy_badge_en' => ['nullable', 'string', 'max:255'],
            'landing_philosophy_badge_ar' => ['nullable', 'string', 'max:255'],
            'landing_philosophy_title_en' => ['nullable', 'string', 'max:255'],
            'landing_philosophy_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_philosophy_desc_en' => ['nullable', 'string', 'max:2000'],
            'landing_philosophy_desc_ar' => ['nullable', 'string', 'max:2000'],

            'landing_zones_badge_en' => ['nullable', 'string', 'max:255'],
            'landing_zones_badge_ar' => ['nullable', 'string', 'max:255'],
            'landing_zones_title_en' => ['nullable', 'string', 'max:255'],
            'landing_zones_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_zones_desc_en' => ['nullable', 'string', 'max:2000'],
            'landing_zones_desc_ar' => ['nullable', 'string', 'max:2000'],

            'landing_products_badge_en' => ['nullable', 'string', 'max:255'],
            'landing_products_badge_ar' => ['nullable', 'string', 'max:255'],
            'landing_products_title_en' => ['nullable', 'string', 'max:255'],
            'landing_products_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_products_subtitle_en' => ['nullable', 'string', 'max:1000'],
            'landing_products_subtitle_ar' => ['nullable', 'string', 'max:1000'],
            'landing_products_limit' => ['nullable', 'integer', 'min:1', 'max:24'],
            'landing_products_cta_text_en' => ['nullable', 'string', 'max:100'],
            'landing_products_cta_text_ar' => ['nullable', 'string', 'max:100'],

            'landing_quality_enabled' => ['nullable', 'boolean'],
            'landing_quality_badge_en' => ['nullable', 'string', 'max:255'],
            'landing_quality_badge_ar' => ['nullable', 'string', 'max:255'],
            'landing_quality_title_en' => ['nullable', 'string', 'max:255'],
            'landing_quality_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_quality_desc_en' => ['nullable', 'string', 'max:2000'],
            'landing_quality_desc_ar' => ['nullable', 'string', 'max:2000'],

            'landing_testimonials_enabled' => ['nullable', 'boolean'],
            'landing_testimonials_badge_en' => ['nullable', 'string', 'max:255'],
            'landing_testimonials_badge_ar' => ['nullable', 'string', 'max:255'],
            'landing_testimonials_title_en' => ['nullable', 'string', 'max:255'],
            'landing_testimonials_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_testimonials_subtitle_en' => ['nullable', 'string', 'max:1000'],
            'landing_testimonials_subtitle_ar' => ['nullable', 'string', 'max:1000'],

            'landing_faqs_enabled' => ['nullable', 'boolean'],
            'landing_faqs_badge_en' => ['nullable', 'string', 'max:255'],
            'landing_faqs_badge_ar' => ['nullable', 'string', 'max:255'],
            'landing_faqs_title_en' => ['nullable', 'string', 'max:255'],
            'landing_faqs_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_faqs_subtitle_en' => ['nullable', 'string', 'max:1000'],
            'landing_faqs_subtitle_ar' => ['nullable', 'string', 'max:1000'],

            'landing_newsletter_enabled' => ['nullable', 'boolean'],
            'landing_newsletter_badge_en' => ['nullable', 'string', 'max:255'],
            'landing_newsletter_badge_ar' => ['nullable', 'string', 'max:255'],
            'landing_newsletter_title_en' => ['nullable', 'string', 'max:255'],
            'landing_newsletter_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_newsletter_desc_en' => ['nullable', 'string', 'max:2000'],
            'landing_newsletter_desc_ar' => ['nullable', 'string', 'max:2000'],
            'landing_newsletter_discount_badge' => ['nullable', 'string', 'max:50'],
            'landing_newsletter_btn_en' => ['nullable', 'string', 'max:100'],
            'landing_newsletter_btn_ar' => ['nullable', 'string', 'max:100'],

            'landing_meta_title_en' => ['nullable', 'string', 'max:255'],
            'landing_meta_title_ar' => ['nullable', 'string', 'max:255'],
            'landing_meta_desc_en' => ['nullable', 'string', 'max:1000'],
            'landing_meta_desc_ar' => ['nullable', 'string', 'max:1000'],
            'landing_meta_keywords' => ['nullable', 'string', 'max:500'],
        ]);

        // General settings
        if (isset($validated['site_name'])) Setting::set('site_name', $validated['site_name'], 'general');
        if (isset($validated['tagline'])) Setting::set('tagline', $validated['tagline'], 'general');
        if (isset($validated['default_language'])) Setting::set('default_language', $validated['default_language'], 'general');
        if (isset($validated['currency'])) Setting::set('currency', $validated['currency'], 'general');
        if (isset($validated['timezone'])) Setting::set('timezone', $validated['timezone'], 'general');
        if (isset($validated['contact_email'])) Setting::set('contact_email', $validated['contact_email'], 'general');
        if (isset($validated['contact_phone'])) Setting::set('contact_phone', $validated['contact_phone'], 'general');
        Setting::set('enable_whatsapp', $request->boolean('enable_whatsapp'), 'general', 'boolean');
        if (isset($validated['whatsapp_number'])) Setting::set('whatsapp_number', $validated['whatsapp_number'], 'general');
        if (isset($validated['whatsapp_default_message'])) Setting::set('whatsapp_default_message', $validated['whatsapp_default_message'], 'general');
        if (isset($validated['font_family'])) Setting::set('font_family', $validated['font_family'], 'general');
        if (isset($validated['font_heading_family'])) Setting::set('font_heading_family', $validated['font_heading_family'], 'general');
        if (isset($validated['font_size_base'])) Setting::set('font_size_base', $validated['font_size_base'], 'general');
        if (isset($validated['font_weight_headings'])) Setting::set('font_weight_headings', $validated['font_weight_headings'], 'general');
        if (isset($validated['font_weight_body'])) Setting::set('font_weight_body', $validated['font_weight_body'], 'general');

        // Store & Inventory
        if (isset($validated['low_stock_threshold'])) Setting::set('low_stock_threshold', (int) $validated['low_stock_threshold'], 'store', 'integer');
        if (isset($validated['zero_stock_behavior'])) Setting::set('zero_stock_behavior', $validated['zero_stock_behavior'], 'store');
        Setting::set('enable_reviews', $request->boolean('enable_reviews'), 'store', 'boolean');
        Setting::set('enable_coupons', $request->boolean('enable_coupons'), 'store', 'boolean');

        // Taxes & Payments
        Setting::set('tax_percentage', (float) $validated['tax_percentage'], 'tax', 'float');
        Setting::set('tax_number', $validated['tax_number'], 'tax', 'string');
        Setting::set('enable_tax', $request->boolean('enable_tax', true), 'tax', 'boolean');
        Setting::set('prices_include_tax', $request->boolean('prices_include_tax', false), 'tax', 'boolean');
        Setting::set('enable_online_payment', $request->boolean('enable_online_payment', $request->boolean('payment_stripe_enabled')), 'commerce', 'boolean');
        Setting::set('enable_cod', $request->boolean('enable_cod', $request->boolean('payment_cod_enabled')), 'commerce', 'boolean');

        // Payment Gateways & Webhooks
        Setting::set('payment_stripe_enabled', $request->boolean('payment_stripe_enabled', $request->boolean('enable_online_payment')), 'payment', 'boolean');
        if (isset($validated['payment_stripe_mode'])) Setting::set('payment_stripe_mode', $validated['payment_stripe_mode'], 'payment');
        if (isset($validated['payment_stripe_public_key'])) Setting::set('payment_stripe_public_key', $validated['payment_stripe_public_key'], 'payment');
        if (isset($validated['payment_stripe_secret_key'])) Setting::set('payment_stripe_secret_key', $validated['payment_stripe_secret_key'], 'payment');
        if (isset($validated['payment_stripe_webhook_secret'])) Setting::set('payment_stripe_webhook_secret', $validated['payment_stripe_webhook_secret'], 'payment');
        Setting::set('payment_cod_enabled', $request->boolean('payment_cod_enabled', $request->boolean('enable_cod')), 'payment', 'boolean');
        if (isset($validated['payment_cod_extra_fee'])) Setting::set('payment_cod_extra_fee', (float) $validated['payment_cod_extra_fee'], 'payment', 'float');
        if (isset($validated['payment_default_gateway'])) Setting::set('payment_default_gateway', $validated['payment_default_gateway'], 'payment');

        // Shipping
        if (isset($validated['free_shipping_threshold'])) Setting::set('free_shipping_threshold', (float) $validated['free_shipping_threshold'], 'shipping', 'float');
        if (isset($validated['flat_shipping_rate'])) Setting::set('flat_shipping_rate', (float) $validated['flat_shipping_rate'], 'shipping', 'float');

        // Notifications & Audio
        Setting::set('notify_low_stock', $request->boolean('notify_low_stock'), 'alerts', 'boolean');
        Setting::set('notify_new_order', $request->boolean('notify_new_order'), 'alerts', 'boolean');
        Setting::set('toast_sound_enabled', $request->boolean('toast_sound_enabled', true), 'alerts', 'boolean');

        // -------------------------------------------------------------
        // LANDING PAGE SECTIONS ORDER & MASTER ON/OFF SWITCHES
        // -------------------------------------------------------------
        $allSectionKeys = array_keys(SettingViewModel::landingSections());

        if ($request->filled('landing_sections_order')) {
            $rawOrder = $request->input('landing_sections_order');
            if (is_string($rawOrder)) {
                $decoded = json_decode($rawOrder, true);
                $orderList = is_array($decoded) ? $decoded : explode(',', $rawOrder);
            } elseif (is_array($rawOrder)) {
                $orderList = $rawOrder;
            } else {
                $orderList = [];
            }

            $cleanOrder = [];
            foreach ($orderList as $k) {
                $k = trim((string)$k);
                if (in_array($k, $allSectionKeys) && !in_array($k, $cleanOrder)) {
                    $cleanOrder[] = $k;
                }
            }
            foreach ($allSectionKeys as $k) {
                if (!in_array($k, $cleanOrder)) {
                    $cleanOrder[] = $k;
                }
            }
            Setting::set('landing_sections_order', $cleanOrder, 'landing', 'json');
        }

        // Master switches for all 12 sections
        foreach ($allSectionKeys as $key) {
            if ($request->has('landing_sections_builder_submitted') || $request->has("landing_{$key}_enabled")) {
                Setting::set("landing_{$key}_enabled", $request->boolean("landing_{$key}_enabled"), 'landing', 'boolean');
            }
        }

        // Landing Page Configuration (Announcements, Hero, Stats, Content)
        Setting::set('landing_announcement_enabled', $request->boolean('landing_announcement_enabled'), 'landing', 'boolean');
        if (isset($validated['landing_announcement_badge_en'])) Setting::set('landing_announcement_badge_en', $validated['landing_announcement_badge_en'], 'landing');
        if (isset($validated['landing_announcement_badge_ar'])) Setting::set('landing_announcement_badge_ar', $validated['landing_announcement_badge_ar'], 'landing');
        if (isset($validated['landing_announcement_text_en'])) Setting::set('landing_announcement_text_en', $validated['landing_announcement_text_en'], 'landing');
        if (isset($validated['landing_announcement_text_ar'])) Setting::set('landing_announcement_text_ar', $validated['landing_announcement_text_ar'], 'landing');
        if (isset($validated['landing_announcement_link'])) Setting::set('landing_announcement_link', $validated['landing_announcement_link'], 'landing');

        if (isset($validated['landing_hero_badge_en'])) Setting::set('landing_hero_badge_en', $validated['landing_hero_badge_en'], 'landing');
        if (isset($validated['landing_hero_badge_ar'])) Setting::set('landing_hero_badge_ar', $validated['landing_hero_badge_ar'], 'landing');
        if (isset($validated['landing_hero_title_en'])) Setting::set('landing_hero_title_en', $validated['landing_hero_title_en'], 'landing');
        if (isset($validated['landing_hero_title_ar'])) Setting::set('landing_hero_title_ar', $validated['landing_hero_title_ar'], 'landing');
        if (isset($validated['landing_hero_subtitle_en'])) Setting::set('landing_hero_subtitle_en', $validated['landing_hero_subtitle_en'], 'landing');
        if (isset($validated['landing_hero_subtitle_ar'])) Setting::set('landing_hero_subtitle_ar', $validated['landing_hero_subtitle_ar'], 'landing');
        if (isset($validated['landing_hero_cta_primary_text_en'])) Setting::set('landing_hero_cta_primary_text_en', $validated['landing_hero_cta_primary_text_en'], 'landing');
        if (isset($validated['landing_hero_cta_primary_text_ar'])) Setting::set('landing_hero_cta_primary_text_ar', $validated['landing_hero_cta_primary_text_ar'], 'landing');
        if (isset($validated['landing_hero_cta_primary_link'])) Setting::set('landing_hero_cta_primary_link', $validated['landing_hero_cta_primary_link'], 'landing');
        if (isset($validated['landing_hero_cta_secondary_text_en'])) Setting::set('landing_hero_cta_secondary_text_en', $validated['landing_hero_cta_secondary_text_en'], 'landing');
        if (isset($validated['landing_hero_cta_secondary_text_ar'])) Setting::set('landing_hero_cta_secondary_text_ar', $validated['landing_hero_cta_secondary_text_ar'], 'landing');
        if (isset($validated['landing_hero_cta_secondary_link'])) Setting::set('landing_hero_cta_secondary_link', $validated['landing_hero_cta_secondary_link'], 'landing');

        Setting::set('landing_stats_enabled', $request->boolean('landing_stats_enabled'), 'landing', 'boolean');
        if (isset($validated['landing_stat_1_val'])) Setting::set('landing_stat_1_val', $validated['landing_stat_1_val'], 'landing');
        if (isset($validated['landing_stat_1_label_en'])) Setting::set('landing_stat_1_label_en', $validated['landing_stat_1_label_en'], 'landing');
        if (isset($validated['landing_stat_1_label_ar'])) Setting::set('landing_stat_1_label_ar', $validated['landing_stat_1_label_ar'], 'landing');
        if (isset($validated['landing_stat_2_val'])) Setting::set('landing_stat_2_val', $validated['landing_stat_2_val'], 'landing');
        if (isset($validated['landing_stat_2_label_en'])) Setting::set('landing_stat_2_label_en', $validated['landing_stat_2_label_en'], 'landing');
        if (isset($validated['landing_stat_2_label_ar'])) Setting::set('landing_stat_2_label_ar', $validated['landing_stat_2_label_ar'], 'landing');
        if (isset($validated['landing_stat_3_val'])) Setting::set('landing_stat_3_val', $validated['landing_stat_3_val'], 'landing');
        if (isset($validated['landing_stat_3_label_en'])) Setting::set('landing_stat_3_label_en', $validated['landing_stat_3_label_en'], 'landing');
        if (isset($validated['landing_stat_3_label_ar'])) Setting::set('landing_stat_3_label_ar', $validated['landing_stat_3_label_ar'], 'landing');
        if (isset($validated['landing_stat_4_val'])) Setting::set('landing_stat_4_val', $validated['landing_stat_4_val'], 'landing');
        if (isset($validated['landing_stat_4_label_en'])) Setting::set('landing_stat_4_label_en', $validated['landing_stat_4_label_en'], 'landing');
        if (isset($validated['landing_stat_4_label_ar'])) Setting::set('landing_stat_4_label_ar', $validated['landing_stat_4_label_ar'], 'landing');

        if (isset($validated['landing_philosophy_badge_en'])) Setting::set('landing_philosophy_badge_en', $validated['landing_philosophy_badge_en'], 'landing');
        if (isset($validated['landing_philosophy_badge_ar'])) Setting::set('landing_philosophy_badge_ar', $validated['landing_philosophy_badge_ar'], 'landing');
        if (isset($validated['landing_philosophy_title_en'])) Setting::set('landing_philosophy_title_en', $validated['landing_philosophy_title_en'], 'landing');
        if (isset($validated['landing_philosophy_title_ar'])) Setting::set('landing_philosophy_title_ar', $validated['landing_philosophy_title_ar'], 'landing');
        if (isset($validated['landing_philosophy_desc_en'])) Setting::set('landing_philosophy_desc_en', $validated['landing_philosophy_desc_en'], 'landing');
        if (isset($validated['landing_philosophy_desc_ar'])) Setting::set('landing_philosophy_desc_ar', $validated['landing_philosophy_desc_ar'], 'landing');

        if (isset($validated['landing_zones_badge_en'])) Setting::set('landing_zones_badge_en', $validated['landing_zones_badge_en'], 'landing');
        if (isset($validated['landing_zones_badge_ar'])) Setting::set('landing_zones_badge_ar', $validated['landing_zones_badge_ar'], 'landing');
        if (isset($validated['landing_zones_title_en'])) Setting::set('landing_zones_title_en', $validated['landing_zones_title_en'], 'landing');
        if (isset($validated['landing_zones_title_ar'])) Setting::set('landing_zones_title_ar', $validated['landing_zones_title_ar'], 'landing');
        if (isset($validated['landing_zones_desc_en'])) Setting::set('landing_zones_desc_en', $validated['landing_zones_desc_en'], 'landing');
        if (isset($validated['landing_zones_desc_ar'])) Setting::set('landing_zones_desc_ar', $validated['landing_zones_desc_ar'], 'landing');

        if (isset($validated['landing_products_badge_en'])) Setting::set('landing_products_badge_en', $validated['landing_products_badge_en'], 'landing');
        if (isset($validated['landing_products_badge_ar'])) Setting::set('landing_products_badge_ar', $validated['landing_products_badge_ar'], 'landing');
        if (isset($validated['landing_products_title_en'])) Setting::set('landing_products_title_en', $validated['landing_products_title_en'], 'landing');
        if (isset($validated['landing_products_title_ar'])) Setting::set('landing_products_title_ar', $validated['landing_products_title_ar'], 'landing');
        if (isset($validated['landing_products_subtitle_en'])) Setting::set('landing_products_subtitle_en', $validated['landing_products_subtitle_en'], 'landing');
        if (isset($validated['landing_products_subtitle_ar'])) Setting::set('landing_products_subtitle_ar', $validated['landing_products_subtitle_ar'], 'landing');
        if (isset($validated['landing_products_limit'])) Setting::set('landing_products_limit', (int)$validated['landing_products_limit'], 'landing', 'integer');
        if (isset($validated['landing_products_cta_text_en'])) Setting::set('landing_products_cta_text_en', $validated['landing_products_cta_text_en'], 'landing');
        if (isset($validated['landing_products_cta_text_ar'])) Setting::set('landing_products_cta_text_ar', $validated['landing_products_cta_text_ar'], 'landing');

        Setting::set('landing_quality_enabled', $request->boolean('landing_quality_enabled'), 'landing', 'boolean');
        if (isset($validated['landing_quality_badge_en'])) Setting::set('landing_quality_badge_en', $validated['landing_quality_badge_en'], 'landing');
        if (isset($validated['landing_quality_badge_ar'])) Setting::set('landing_quality_badge_ar', $validated['landing_quality_badge_ar'], 'landing');
        if (isset($validated['landing_quality_title_en'])) Setting::set('landing_quality_title_en', $validated['landing_quality_title_en'], 'landing');
        if (isset($validated['landing_quality_title_ar'])) Setting::set('landing_quality_title_ar', $validated['landing_quality_title_ar'], 'landing');
        if (isset($validated['landing_quality_desc_en'])) Setting::set('landing_quality_desc_en', $validated['landing_quality_desc_en'], 'landing');
        if (isset($validated['landing_quality_desc_ar'])) Setting::set('landing_quality_desc_ar', $validated['landing_quality_desc_ar'], 'landing');

        Setting::set('landing_testimonials_enabled', $request->boolean('landing_testimonials_enabled'), 'landing', 'boolean');
        if (isset($validated['landing_testimonials_badge_en'])) Setting::set('landing_testimonials_badge_en', $validated['landing_testimonials_badge_en'], 'landing');
        if (isset($validated['landing_testimonials_badge_ar'])) Setting::set('landing_testimonials_badge_ar', $validated['landing_testimonials_badge_ar'], 'landing');
        if (isset($validated['landing_testimonials_title_en'])) Setting::set('landing_testimonials_title_en', $validated['landing_testimonials_title_en'], 'landing');
        if (isset($validated['landing_testimonials_title_ar'])) Setting::set('landing_testimonials_title_ar', $validated['landing_testimonials_title_ar'], 'landing');
        if (isset($validated['landing_testimonials_subtitle_en'])) Setting::set('landing_testimonials_subtitle_en', $validated['landing_testimonials_subtitle_en'], 'landing');
        if (isset($validated['landing_testimonials_subtitle_ar'])) Setting::set('landing_testimonials_subtitle_ar', $validated['landing_testimonials_subtitle_ar'], 'landing');

        Setting::set('landing_faqs_enabled', $request->boolean('landing_faqs_enabled'), 'landing', 'boolean');
        if (isset($validated['landing_faqs_badge_en'])) Setting::set('landing_faqs_badge_en', $validated['landing_faqs_badge_en'], 'landing');
        if (isset($validated['landing_faqs_badge_ar'])) Setting::set('landing_faqs_badge_ar', $validated['landing_faqs_badge_ar'], 'landing');
        if (isset($validated['landing_faqs_title_en'])) Setting::set('landing_faqs_title_en', $validated['landing_faqs_title_en'], 'landing');
        if (isset($validated['landing_faqs_title_ar'])) Setting::set('landing_faqs_title_ar', $validated['landing_faqs_title_ar'], 'landing');
        if (isset($validated['landing_faqs_subtitle_en'])) Setting::set('landing_faqs_subtitle_en', $validated['landing_faqs_subtitle_en'], 'landing');
        if (isset($validated['landing_faqs_subtitle_ar'])) Setting::set('landing_faqs_subtitle_ar', $validated['landing_faqs_subtitle_ar'], 'landing');

        Setting::set('landing_newsletter_enabled', $request->boolean('landing_newsletter_enabled'), 'landing', 'boolean');
        if (isset($validated['landing_newsletter_badge_en'])) Setting::set('landing_newsletter_badge_en', $validated['landing_newsletter_badge_en'], 'landing');
        if (isset($validated['landing_newsletter_badge_ar'])) Setting::set('landing_newsletter_badge_ar', $validated['landing_newsletter_badge_ar'], 'landing');
        if (isset($validated['landing_newsletter_title_en'])) Setting::set('landing_newsletter_title_en', $validated['landing_newsletter_title_en'], 'landing');
        if (isset($validated['landing_newsletter_title_ar'])) Setting::set('landing_newsletter_title_ar', $validated['landing_newsletter_title_ar'], 'landing');
        if (isset($validated['landing_newsletter_desc_en'])) Setting::set('landing_newsletter_desc_en', $validated['landing_newsletter_desc_en'], 'landing');
        if (isset($validated['landing_newsletter_desc_ar'])) Setting::set('landing_newsletter_desc_ar', $validated['landing_newsletter_desc_ar'], 'landing');
        if (isset($validated['landing_newsletter_discount_badge'])) Setting::set('landing_newsletter_discount_badge', $validated['landing_newsletter_discount_badge'], 'landing');
        if (isset($validated['landing_newsletter_btn_en'])) Setting::set('landing_newsletter_btn_en', $validated['landing_newsletter_btn_en'], 'landing');
        if (isset($validated['landing_newsletter_btn_ar'])) Setting::set('landing_newsletter_btn_ar', $validated['landing_newsletter_btn_ar'], 'landing');

        if (isset($validated['landing_meta_title_en'])) Setting::set('landing_meta_title_en', $validated['landing_meta_title_en'], 'landing');
        if (isset($validated['landing_meta_title_ar'])) Setting::set('landing_meta_title_ar', $validated['landing_meta_title_ar'], 'landing');
        if (isset($validated['landing_meta_desc_en'])) Setting::set('landing_meta_desc_en', $validated['landing_meta_desc_en'], 'landing');
        if (isset($validated['landing_meta_desc_ar'])) Setting::set('landing_meta_desc_ar', $validated['landing_meta_desc_ar'], 'landing');
        if (isset($validated['landing_meta_keywords'])) Setting::set('landing_meta_keywords', $validated['landing_meta_keywords'], 'landing');

        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('admin.settings.index')
            ->with('success', app()->getLocale() === 'ar'
                ? 'تم حفظ وتحديث إعدادات النظام ومحتوى وترتيب وتفعيل أقسام الصفحة الرئيسية بنجاح!'
                : 'Platform settings, landing page sections order, and visibility toggles updated successfully!');
    }
}
