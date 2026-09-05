@php
    $cId = $category['id'] ?? 1;
    $cName = app()->getLocale() == 'ar' ? ($category['name_ar'] ?? $category['name_en']) : $category['name_en'];
@endphp

<x-layouts.admin 
    :pageTitle="__('admin.categories.edit_title', ['name' => $cName])" 
    :pageSubtitle="__('admin.categories.subtitle')"
    :breadcrumbs="[__('admin.menu.categories') => route('admin.categories.index'), $cName => route('admin.categories.edit', $cId)]"
>
    <form method="POST" action="{{ route('admin.categories.update', $cId) }}">
        @csrf
        @method('PUT')

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
                    :value="old('name_en', $category['name_en'] ?? '')" 
                    required 
                />
                <div dir="rtl">
                    <x-forms.input 
                        name="name_ar" 
                        :label="__('admin.categories.arabic_designation')" 
                        :value="old('name_ar', $category['name_ar'] ?? '')" 
                        required 
                    />
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input 
                    name="slug" 
                    label="URL Slug (المعرف الرابطي)" 
                    :value="old('slug', $category['slug'] ?? '')" 
                />
                <x-forms.input 
                    name="sort_order" 
                    :label="__('admin.categories.sort_order')" 
                    type="number" 
                    :value="old('sort_order', $category['sort_order'] ?? 1)" 
                    required 
                />
            </div>

            <x-forms.textarea 
                name="description_en" 
                label="Category Description (EN)" 
                rows="3" 
                :value="old('description_en', $category['description_en'] ?? '')" 
            />

            <div dir="rtl">
                <x-forms.textarea 
                    name="description_ar" 
                    label="الوصف التعريفي بالعربية" 
                    rows="3" 
                    :value="old('description_ar', $category['description_ar'] ?? '')" 
                />
            </div>

            <x-forms.toggle 
                name="is_active" 
                :label="app()->getLocale() == 'ar' ? 'تفعيل ونشر القسم في المتجر وقائمة التصفح' : 'Publish to Storefront Menu'" 
                :description="app()->getLocale() == 'ar' ? 'عند التفعيل، سيظهر هذا القسم في القائمة الرئيسية وفلاتر التسوق.' : 'When enabled, this category will appear in the main customer navigation and shop filter sidebar.'" 
                :checked="old('is_active', ($category['status'] ?? 'active') === 'active' || ($category['is_active'] ?? true))" 
            />
        </div>
    </form>
</x-layouts.admin>
