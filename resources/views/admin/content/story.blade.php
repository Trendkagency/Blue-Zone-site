<x-layouts.admin 
<<<<<<< HEAD
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
=======
    :pageTitle="__('admin.content.story_title')" 
    :pageSubtitle="__('admin.content.story_subtitle')"
    :breadcrumbs="[__('admin.menu.content') => route('admin.content.index'), __('admin.content.story_title') => route('admin.content.story')]"
>
    <form method="POST" action="{{ route('admin.content.story.update') }}">
        @csrf

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">{{ __('app.actions.cancel') }}</a>
            <button type="submit" class="btn btn-primary font-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('app.actions.save') }}
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 900px;">
            @foreach($zones as $index => $z)
                <div class="card shadow-sm border border-gray-100 dark:border-gray-800" style="padding: 1.5rem;">
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1rem;">
                        {{ $z['name_en'] }} ({{ $z['name_ar'] }})
                    </h4>

                    <input type="hidden" name="zones[{{ $index }}][name_en]" value="{{ $z['name_en'] }}">
                    <input type="hidden" name="zones[{{ $index }}][name_ar]" value="{{ $z['name_ar'] }}">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-forms.input name="zones[{{ $index }}][focus_en]" label="Focus & Botanicals (EN)" :value="$z['focus_en']" required />
                        <div dir="rtl">
                            <x-forms.input name="zones[{{ $index }}][focus_ar]" label="التركيز الحيوي (عربي)" :value="$z['focus_ar']" required />
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
                        <x-forms.input name="zones[{{ $index }}][lat]" label="Latitude / خط العرض" :value="$z['lat']" required />
                        <x-forms.input name="zones[{{ $index }}][lng]" label="Longitude / خط الطول" :value="$z['lng']" required />
                    </div>
                </div>
            @endforeach
        </div>
    </form>
>>>>>>> origin/main
</x-layouts.admin>
