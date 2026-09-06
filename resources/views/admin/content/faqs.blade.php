<x-layouts.admin 
    :pageTitle="__('admin.menu.faqs')" 
    pageSubtitle="Manage clinical FAQs, answers, and category associations in Arabic and English."
    :breadcrumbs="['Content' => route('admin.content.index'), 'FAQs' => route('admin.content.faqs')]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-primary">+ Add New FAQ</button>
    </x-slot>

    <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 900px;">
        @foreach($faqs as $f)
            <div class="card" style="padding: 1.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--color-primary); margin: 0;">
                        {{ $f['q_en'] }}
                    </h4>
                    <button type="button" class="btn btn-ghost btn-sm text-danger">🗑️</button>
                </div>

                <div class="text-sm text-secondary" style="margin-bottom: 1rem; line-height: 1.7;">
                    {{ $f['a_en'] }}
                </div>

                <div style="background: var(--color-bg-subtle); padding: 1rem; border-radius: var(--radius-md);" dir="rtl">
                    <div class="font-bold text-sm" style="margin-bottom: 0.5rem; color: var(--color-primary);">{{ $f['q_ar'] }}</div>
                    <div class="text-xs text-secondary">{{ $f['a_ar'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.admin>
