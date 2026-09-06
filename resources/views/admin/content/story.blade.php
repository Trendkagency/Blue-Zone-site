<x-layouts.admin 
    :pageTitle="__('admin.menu.story')" 
    pageSubtitle="Configure geographic zone coordinates, botanical focuses, and centenarian profiles."
    :breadcrumbs="['Content' => route('admin.content.index'), 'Longevity Zones' => route('admin.content.story')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
        <button type="button" class="btn btn-primary" onclick="alert('Zone content saved!')">💾 {{ __('app.actions.save') }}</button>
    </x-slot>

    <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 900px;">
        @foreach($zones as $z)
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1rem;">
                    {{ $z['name_en'] }} ({{ $z['name_ar'] }})
                </h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <x-forms.input name="zone_focus_en[]" label="Focus & Botanicals (EN)" :value="$z['focus_en']" />
                    <div dir="rtl">
                        <x-forms.input name="zone_focus_ar[]" label="التركيز الحيوي (عربي)" :value="$z['focus_ar']" />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
                    <x-forms.input name="zone_lat[]" label="Latitude" :value="$z['lat']" />
                    <x-forms.input name="zone_lng[]" label="Longitude" :value="$z['lng']" />
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.admin>
