<x-layouts.admin 
    :pageTitle="__('admin.content.title')" 
    :pageSubtitle="__('admin.content.subtitle')"
    :breadcrumbs="[__('admin.menu.content') => route('admin.content.index')]"
>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <div class="card card-hover-lift" style="padding: 2rem;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">🖼️</div>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">
                {{ __('admin.content.banners_title') }}
            </h3>
            <p class="text-sm text-secondary" style="margin-bottom: 1.5rem;">
                {{ __('admin.content.banners_subtitle') }}
            </p>
            <a href="{{ route('admin.content.banners') }}" class="btn btn-primary btn-sm">
                {{ __('admin.content.edit_hero_btn') }}
            </a>
        </div>

        <div class="card card-hover-lift" style="padding: 2rem;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">🌍</div>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">
                {{ __('admin.content.story_title') }}
            </h3>
            <p class="text-sm text-secondary" style="margin-bottom: 1.5rem;">
                {{ __('admin.content.story_subtitle') }}
            </p>
            <a href="{{ route('admin.content.story') }}" class="btn btn-primary btn-sm">
                {{ __('admin.content.edit_zones_btn') }}
            </a>
        </div>

        <div class="card card-hover-lift" style="padding: 2rem;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">❓</div>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">
                {{ __('admin.content.faqs_title') }}
            </h3>
            <p class="text-sm text-secondary" style="margin-bottom: 1.5rem;">
                {{ __('admin.content.faqs_subtitle') }}
            </p>
            <a href="{{ route('admin.content.faqs') }}" class="btn btn-primary btn-sm">
                {{ __('admin.content.manage_faqs_btn') }}
            </a>
        </div>
    </div>
</x-layouts.admin>
