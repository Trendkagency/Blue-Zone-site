<x-layouts.admin 
    :pageTitle="__('admin.content.faqs_title')" 
    :pageSubtitle="__('admin.content.faqs_subtitle')"
    :breadcrumbs="[__('admin.menu.content') => route('admin.content.index'), __('admin.content.faqs_title') => route('admin.content.faqs')]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-primary font-bold shadow-sm" onclick="openModal('createFaqModal')">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('admin.content.add_faq_btn') }}
        </button>
    </x-slot>

    <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 900px;">
        @forelse($faqs as $index => $f)
            <div class="card shadow-sm border border-gray-100 dark:border-gray-800" style="padding: 1.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--color-primary); margin: 0;">
                        {{ $f['q_en'] }}
                    </h4>
                    <button type="button" class="btn btn-ghost btn-sm text-danger cursor-pointer" 
                            onclick="confirmDelete('{{ route('admin.content.faqs.destroy', $index) }}', '{{ addslashes($f['q_en']) }}', false)" 
                            title="{{ __('app.actions.delete') }}">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>

                <div class="text-sm text-secondary" style="margin-bottom: 1rem; line-height: 1.7;">
                    {{ $f['a_en'] }}
                </div>

                <div style="background: var(--color-bg-subtle); padding: 1rem; border-radius: var(--radius-md);" dir="rtl">
                    <div class="font-bold text-sm" style="margin-bottom: 0.5rem; color: var(--color-primary);">{{ $f['q_ar'] }}</div>
                    <div class="text-xs text-secondary leading-relaxed">{{ $f['a_ar'] }}</div>
                </div>
            </div>
        @empty
            <div class="card p-8 text-center text-muted">
                <i class="fa-solid fa-circle-question text-3xl mb-2 text-gray-400"></i>
                <p class="font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد أسئلة شائعة مسجلة حالياً.' : 'No FAQs recorded yet.' }}</p>
            </div>
        @endforelse
    </div>

    <!-- Create FAQ Modal -->
    <div id="createFaqModal" class="modal-backdrop" style="display: none; align-items: center; justify-content: center;">
        <div class="modal-dialog" style="max-width: 650px; width: 90%; background: var(--color-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); border: 1px solid var(--color-border); overflow: hidden;">
            <form method="POST" action="{{ route('admin.content.faqs.store') }}">
                @csrf

                <div class="modal-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between;">
                    <h4 class="modal-title font-bold text-base" style="margin: 0; color: var(--color-primary);">
                        <i class="fa-solid fa-plus-circle mr-1.5 ml-1.5"></i> {{ __('admin.content.add_faq_btn') }}
                    </h4>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('createFaqModal')" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal-body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                    <x-forms.input name="q_en" label="Question (English)" placeholder="e.g. How to store Blue Zone bottles?" required />
                    <x-forms.textarea name="a_en" label="Answer (English)" rows="3" placeholder="Store in a cool, dry environment..." required />

                    <div dir="rtl">
                        <x-forms.input name="q_ar" label="السؤال بالعربية" placeholder="مثال: كيف يتم تخزين عبوات بلو زون؟" required />
                        <x-forms.textarea name="a_ar" label="الإجابة بالعربية" rows="3" placeholder="يحفظ في مكان بارد وجاف..." required />
                    </div>
                </div>

                <div class="modal-footer" style="padding: 1rem 1.5rem; background: var(--color-bg-subtle); border-top: 1px solid var(--color-border); display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-secondary text-sm" onclick="closeModal('createFaqModal')">
                        {{ __('app.actions.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary text-sm font-bold">
                        <i class="fa-solid fa-floppy-disk mr-1 ml-1"></i> {{ __('app.actions.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
