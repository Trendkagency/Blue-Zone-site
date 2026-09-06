<x-layouts.admin 
    :pageTitle="__('admin.menu.banners')" 
    pageSubtitle="Configure homepage hero banners, typography, and call-to-actions in Arabic and English."
    :breadcrumbs="['Content' => route('admin.content.index'), 'Hero & Banners' => route('admin.content.banners')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
        <button type="button" class="btn btn-primary" onclick="alert('Banner translations saved!')">💾 {{ __('app.actions.save') }}</button>
    </x-slot>

    <div class="card" style="padding: 2rem; max-width: 900px;">
        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
            Flagship Homepage Hero Section
        </h3>

        <!-- English Section -->
        <div style="background: var(--color-bg-subtle); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
            <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1rem;">English (LTR)</h4>
            <x-forms.input name="hero_badge_en" label="Pill Badge (EN)" :value="$hero['badge_en']" required />
            <x-forms.input name="hero_title_en" label="Main Heading (EN)" :value="$hero['title_en']" required />
            <x-forms.textarea name="hero_subtitle_en" label="Subtitle Description (EN)" rows="3" :value="$hero['subtitle_en']" required />
        </div>

        <!-- Arabic Section -->
        <div style="background: var(--color-bg-subtle); padding: 1.5rem; border-radius: var(--radius-md);" dir="rtl">
            <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1rem;">العربية (RTL)</h4>
            <x-forms.input name="hero_badge_ar" label="النص التعريفي العلوي (عربي)" :value="$hero['badge_ar']" required />
            <x-forms.input name="hero_title_ar" label="العنوان الرئيسي (عربي)" :value="$hero['title_ar']" required />
            <x-forms.textarea name="hero_subtitle_ar" label="الوصف التوضيحي (عربي)" rows="3" :value="$hero['subtitle_ar']" required />
        </div>
    </div>
</x-layouts.admin>
