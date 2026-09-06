<x-layouts.admin 
    pageTitle="Create Longevity Category" 
    pageSubtitle="Add a new biological target system for catalog navigation and product classification."
    :breadcrumbs="['Categories' => route('admin.categories.index'), 'Create' => route('admin.categories.create')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            {{ __('app.actions.cancel') }}
        </a>
        <button type="button" class="btn btn-primary" onclick="alert('Category created successfully!')">
            💾 {{ __('app.actions.save') }}
        </button>
    </x-slot>

    <div class="card" style="padding: 2rem; max-width: 800px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="name_en" label="Category Name (EN)" placeholder="e.g. Cellular Longevity" required />
            <div dir="rtl">
                <x-forms.input name="name_ar" label="اسم القسم بالعربية" placeholder="مثال: طول العمر وتجديد الخلايا" required />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="slug" label="URL Slug" placeholder="cellular-longevity" required />
            <x-forms.input name="sort_order" label="Sort Priority" type="number" value="1" required />
        </div>

        <x-forms.textarea name="description_en" label="Category Description (EN)" rows="3" />

        <div dir="rtl">
            <x-forms.textarea name="description_ar" label="الوصف بالعربية" rows="3" />
        </div>

        <x-forms.toggle name="is_active" label="Publish to Storefront Menu" description="When enabled, this category will appear in the main customer navigation and shop filter sidebar." checked />
    </div>
</x-layouts.admin>
