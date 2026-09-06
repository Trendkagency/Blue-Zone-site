<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\View\ViewModels\SettingViewModel;
=======
use App\Models\Setting;
use App\View\ViewModels\SettingViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
>>>>>>> origin/main
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
<<<<<<< HEAD
        $settings = SettingViewModel::all();
=======
        $defaults = SettingViewModel::all();
        $saved = Setting::getAll();
        $settings = array_merge($defaults, $saved);
>>>>>>> origin/main

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }
<<<<<<< HEAD
=======

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

            // Shipping
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'flat_shipping_rate' => ['nullable', 'numeric', 'min:0'],

            // Notifications & Audio
            'notify_low_stock' => ['nullable', 'boolean'],
            'notify_new_order' => ['nullable', 'boolean'],
            'toast_sound_enabled' => ['nullable', 'boolean'],
        ]);

        // General settings
        if (isset($validated['site_name'])) Setting::set('site_name', $validated['site_name'], 'general');
        if (isset($validated['tagline'])) Setting::set('tagline', $validated['tagline'], 'general');
        if (isset($validated['default_language'])) Setting::set('default_language', $validated['default_language'], 'general');
        if (isset($validated['currency'])) Setting::set('currency', $validated['currency'], 'general');
        if (isset($validated['timezone'])) Setting::set('timezone', $validated['timezone'], 'general');
        if (isset($validated['contact_email'])) Setting::set('contact_email', $validated['contact_email'], 'general');
        if (isset($validated['contact_phone'])) Setting::set('contact_phone', $validated['contact_phone'], 'general');
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
        Setting::set('enable_online_payment', $request->boolean('enable_online_payment'), 'commerce', 'boolean');
        Setting::set('enable_cod', $request->boolean('enable_cod'), 'commerce', 'boolean');

        // Shipping
        if (isset($validated['free_shipping_threshold'])) Setting::set('free_shipping_threshold', (float) $validated['free_shipping_threshold'], 'shipping', 'float');
        if (isset($validated['flat_shipping_rate'])) Setting::set('flat_shipping_rate', (float) $validated['flat_shipping_rate'], 'shipping', 'float');

        // Notifications & Audio
        Setting::set('notify_low_stock', $request->boolean('notify_low_stock'), 'alerts', 'boolean');
        Setting::set('notify_new_order', $request->boolean('notify_new_order'), 'alerts', 'boolean');
        Setting::set('toast_sound_enabled', $request->boolean('toast_sound_enabled', true), 'alerts', 'boolean');

        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('admin.settings.index')
            ->with('success', app()->getLocale() === 'ar'
                ? 'تم حفظ وتحديث إعدادات النظام ومعدلات الضرائب والمؤثرات الصوتية بنجاح!'
                : 'Platform settings, taxes, and audio alerts updated successfully!');
    }
>>>>>>> origin/main
}
