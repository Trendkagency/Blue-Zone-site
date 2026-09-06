<x-layouts.admin 
    :pageTitle="__('admin.categories.create_title')" 
    :pageSubtitle="__('admin.categories.subtitle')"
    :breadcrumbs="[__('admin.menu.categories') => route('admin.categories.index'), __('app.actions.create') => route('admin.categories.create')]"
>
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                {{ __('app.actions.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary font-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('app.actions.save') }}
            </button>
        </div>

        <div class="card" style="padding: 2rem; max-width: 800px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input 
                    name="name_en" 
                    :label="__('admin.categories.system_name')" 
                    placeholder="e.g. Cellular Longevity" 
                    :value="old('name_en')"
                    required 
                />
                <div dir="rtl">
                    <x-forms.input 
                        name="name_ar" 
                        :label="__('admin.categories.arabic_designation')" 
                        placeholder="مثال: طول العمر وتجديد الخلايا" 
                        :value="old('name_ar')"
                        required 
                    />
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input 
                    name="slug" 
                    label="URL Slug (المعرف الرابطي)" 
                    placeholder="cellular-longevity" 
                    :value="old('slug')"
                />
                <x-forms.input 
                    name="sort_order" 
                    :label="__('admin.categories.sort_order')" 
                    type="number" 
                    :value="old('sort_order', 1)" 
                    required 
                />
            </div>

            <x-forms.textarea 
                name="description_en" 
                label="Category Description (EN)" 
                rows="3" 
                placeholder="Clinical background and biological target system description..." 
                :value="old('description_en')"
            />

            <div dir="rtl">
                <x-forms.textarea 
                    name="description_ar" 
                    label="الوصف التعريفي بالعربية" 
                    rows="3" 
                    placeholder="شرح النظام الحيوي وأهدافه الفسيولوجية للعملاء..." 
                    :value="old('description_ar')"
                />
            </div>

            <x-forms.toggle 
                name="is_active" 
                :label="app()->getLocale() == 'ar' ? 'تفعيل ونشر القسم في المتجر وقائمة التصفح' : 'Publish to Storefront Menu'" 
                :description="app()->getLocale() == 'ar' ? 'عند التفعيل، سيظهر هذا القسم في القائمة الرئيسية وفلاتر التسوق.' : 'When enabled, this category will appear in the main customer navigation and shop filter sidebar.'" 
                :checked="old('is_active', true)" 
            />
        </div>
    </form>
</x-layouts.admin>
