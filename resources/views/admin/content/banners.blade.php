<x-layouts.admin 
    :pageTitle="__('admin.content.banners_title')" 
    :pageSubtitle="__('admin.content.banners_subtitle')"
    :breadcrumbs="[__('admin.menu.content') => route('admin.content.index'), __('admin.content.banners_title') => route('admin.content.banners')]"
>
    <form method="POST" action="{{ route('admin.content.banners.update') }}">
        @csrf

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
            <button type="submit" class="btn btn-primary font-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('app.actions.save') }}
            </button>
        </div>

        <div class="card shadow-sm border border-gray-100 dark:border-gray-800" style="padding: 2rem; max-width: 900px;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                {{ __('admin.content.banners_title') }}
            </h3>

            <!-- English Section -->
            <div style="background: var(--color-bg-subtle); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1rem;">English (LTR)</h4>
                <x-forms.input name="badge_en" label="Pill Badge (EN)" :value="old('badge_en', $hero['badge_en'] ?? '')" required />
                <x-forms.input name="title_en" label="Main Heading (EN)" :value="old('title_en', $hero['title_en'] ?? '')" required />
                <x-forms.textarea name="subtitle_en" label="Subtitle Description (EN)" rows="3" :value="old('subtitle_en', $hero['subtitle_en'] ?? '')" required />
            </div>

            <!-- Arabic Section -->
            <div style="background: var(--color-bg-subtle); padding: 1.5rem; border-radius: var(--radius-md);" dir="rtl">
                <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1rem;">العربية (RTL)</h4>
                <x-forms.input name="badge_ar" label="النص التعريفي العلوي (عربي)" :value="old('badge_ar', $hero['badge_ar'] ?? '')" required />
                <x-forms.input name="title_ar" label="العنوان الرئيسي (عربي)" :value="old('title_ar', $hero['title_ar'] ?? '')" required />
                <x-forms.textarea name="subtitle_ar" label="الوصف التوضيحي (عربي)" rows="3" :value="old('subtitle_ar', $hero['subtitle_ar'] ?? '')" required />
            </div>
        </div>
    </form>
</x-layouts.admin>
