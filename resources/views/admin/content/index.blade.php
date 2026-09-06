<x-layouts.admin 
<<<<<<< HEAD
    :pageTitle="__('admin.menu.content')" 
    pageSubtitle="Manage storefront CMS sections, homepage hero messaging, brand narratives, and FAQs."
    :breadcrumbs="['Content' => route('admin.content.index')]"
=======
    :pageTitle="__('admin.content.title')" 
    :pageSubtitle="__('admin.content.subtitle')"
    :breadcrumbs="[__('admin.menu.content') => route('admin.content.index')]"
>>>>>>> origin/main
>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <div class="card card-hover-lift" style="padding: 2rem;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">🖼️</div>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">
<<<<<<< HEAD
                {{ __('admin.menu.banners') }}
            </h3>
            <p class="text-sm text-secondary" style="margin-bottom: 1.5rem;">
                Configure hero titles, badges, call-to-action buttons, and background gradients in Arabic and English.
            </p>
            <a href="{{ route('admin.content.banners') }}" class="btn btn-primary btn-sm">
                Edit Hero & Banners →
=======
                {{ __('admin.content.banners_title') }}
            </h3>
            <p class="text-sm text-secondary" style="margin-bottom: 1.5rem;">
                {{ __('admin.content.banners_subtitle') }}
            </p>
            <a href="{{ route('admin.content.banners') }}" class="btn btn-primary btn-sm">
                {{ __('admin.content.edit_hero_btn') }}
>>>>>>> origin/main
            </a>
        </div>

        <div class="card card-hover-lift" style="padding: 2rem;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">🌍</div>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">
<<<<<<< HEAD
                {{ __('admin.menu.story') }}
            </h3>
            <p class="text-sm text-secondary" style="margin-bottom: 1.5rem;">
                Update geographic longevity coordinates, historical centenarian research, and cultural focus areas.
            </p>
            <a href="{{ route('admin.content.story') }}" class="btn btn-primary btn-sm">
                Edit Longevity Zones →
=======
                {{ __('admin.content.story_title') }}
            </h3>
            <p class="text-sm text-secondary" style="margin-bottom: 1.5rem;">
                {{ __('admin.content.story_subtitle') }}
            </p>
            <a href="{{ route('admin.content.story') }}" class="btn btn-primary btn-sm">
                {{ __('admin.content.edit_zones_btn') }}
>>>>>>> origin/main
            </a>
        </div>

        <div class="card card-hover-lift" style="padding: 2rem;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">❓</div>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">
<<<<<<< HEAD
                {{ __('admin.menu.faqs') }}
            </h3>
            <p class="text-sm text-secondary" style="margin-bottom: 1.5rem;">
                Manage customer questions regarding clinical assays, stacking safety, and white-glove shipping.
            </p>
            <a href="{{ route('admin.content.faqs') }}" class="btn btn-primary btn-sm">
                Manage FAQs →
=======
                {{ __('admin.content.faqs_title') }}
            </h3>
            <p class="text-sm text-secondary" style="margin-bottom: 1.5rem;">
                {{ __('admin.content.faqs_subtitle') }}
            </p>
            <a href="{{ route('admin.content.faqs') }}" class="btn btn-primary btn-sm">
                {{ __('admin.content.manage_faqs_btn') }}
>>>>>>> origin/main
            </a>
        </div>
    </div>
</x-layouts.admin>
