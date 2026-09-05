<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\View\ViewModels\ContentViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(): View
    {
        $default = ContentViewModel::all();
        $content = [
            'hero' => Setting::get('cms_hero', $default['hero']),
            'zones' => Setting::get('cms_zones', $default['zones']),
            'faqs' => Setting::get('cms_faqs', $default['faqs']),
        ];

        return view('admin.content.index', [
            'content' => $content,
        ]);
    }

    public function banners(): View
    {
        $default = ContentViewModel::all();
        $hero = Setting::get('cms_hero', $default['hero']);

        return view('admin.content.banners', ['hero' => $hero]);
    }

    public function updateBanners(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'badge_en' => 'required|string|max:255',
            'badge_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'subtitle_en' => 'required|string',
            'subtitle_ar' => 'required|string',
        ]);

        Setting::set('cms_hero', $validated, 'cms', 'json');

        return redirect()->route('admin.content.banners')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم تحديث محتوى ولافتات الواجهة الرئيسية بنجاح!"
                : "Hero banner content updated successfully!");
    }

    public function story(): View
    {
        $default = ContentViewModel::all();
        $zones = Setting::get('cms_zones', $default['zones']);

        return view('admin.content.story', ['zones' => $zones]);
    }

    public function updateStory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'zones' => 'required|array',
            'zones.*.name_en' => 'required|string|max:255',
            'zones.*.name_ar' => 'required|string|max:255',
            'zones.*.focus_en' => 'required|string|max:255',
            'zones.*.focus_ar' => 'required|string|max:255',
            'zones.*.lat' => 'required|string|max:50',
            'zones.*.lng' => 'required|string|max:50',
        ]);

        Setting::set('cms_zones', $validated['zones'], 'cms', 'json');

        return redirect()->route('admin.content.story')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم تحديث معلومات الأقاليم الزرقاء وسرد القصة بنجاح!"
                : "Blue Zones story and geographic data updated successfully!");
    }

    public function wellness(): View
    {
        return view('admin.content.wellness');
    }

    public function updateWellness(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'wellness_title_en' => 'nullable|string|max:255',
            'wellness_title_ar' => 'nullable|string|max:255',
            'wellness_body_en' => 'nullable|string',
            'wellness_body_ar' => 'nullable|string',
        ]);

        Setting::set('cms_wellness', $validated, 'cms', 'json');

        return redirect()->route('admin.content.wellness')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم حفظ وتحديث محتوى مقالات العافية بنجاح!"
                : "Wellness content updated successfully!");
    }

    public function faqs(): View
    {
        $default = ContentViewModel::all();
        $faqs = Setting::get('cms_faqs', $default['faqs']);

        return view('admin.content.faqs', ['faqs' => $faqs]);
    }

    public function storeFaq(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'q_en' => 'required|string|max:500',
            'q_ar' => 'required|string|max:500',
            'a_en' => 'required|string',
            'a_ar' => 'required|string',
        ]);

        $default = ContentViewModel::all();
        $faqs = Setting::get('cms_faqs', $default['faqs']);
        $faqs[] = $validated;

        Setting::set('cms_faqs', $faqs, 'cms', 'json');

        return redirect()->route('admin.content.faqs')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم إضافة السؤال الشائع بنجاح!"
                : "FAQ item created successfully!");
    }

    public function updateFaq(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'q_en' => 'required|string|max:500',
            'q_ar' => 'required|string|max:500',
            'a_en' => 'required|string',
            'a_ar' => 'required|string',
        ]);

        $default = ContentViewModel::all();
        $faqs = Setting::get('cms_faqs', $default['faqs']);

        if (isset($faqs[$id])) {
            $faqs[$id] = $validated;
            Setting::set('cms_faqs', $faqs, 'cms', 'json');
        }

        return redirect()->route('admin.content.faqs')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم تحديث السؤال الشائع بنجاح!"
                : "FAQ item updated successfully!");
    }

    public function destroyFaq(int $id): RedirectResponse
    {
        $default = ContentViewModel::all();
        $faqs = Setting::get('cms_faqs', $default['faqs']);

        if (isset($faqs[$id])) {
            array_splice($faqs, $id, 1);
            Setting::set('cms_faqs', $faqs, 'cms', 'json');
        }

        return redirect()->route('admin.content.faqs')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم حذف السؤال الشائع بنجاح!"
                : "FAQ item deleted successfully!");
    }
}
