<x-layouts.admin 
    :pageTitle="'Edit: ' . $category['name_en']" 
    pageSubtitle="Update biological classification, subcategories, and storefront hierarchy."
    :breadcrumbs="['Categories' => route('admin.categories.index'), $category['name_en'] => route('admin.categories.edit', $category['id'])]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            {{ __('app.actions.cancel') }}
        </a>
        <button type="button" class="btn btn-primary" onclick="alert('Category changes saved!')">
            💾 {{ __('app.actions.save') }}
        </button>
    </x-slot>

    <div class="card" style="padding: 2rem; max-width: 800px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="name_en" label="Category Name (EN)" :value="$category['name_en']" required />
            <div dir="rtl">
                <x-forms.input name="name_ar" label="اسم القسم بالعربية" :value="$category['name_ar']" required />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-forms.input name="slug" label="URL Slug" :value="$category['slug']" required />
            <x-forms.input name="sort_order" label="Sort Priority" type="number" :value="$category['sort_order']" required />
        </div>

        <x-forms.textarea name="description_en" label="Category Description (EN)" rows="3" :value="$category['description_en']" />

        <div dir="rtl">
            <x-forms.textarea name="description_ar" label="الوصف بالعربية" rows="3" :value="$category['description_ar']" />
        </div>

        <x-forms.toggle name="is_active" label="Publish to Storefront Menu" description="When enabled, this category will appear in the main customer navigation and shop filter sidebar." :checked="$category['status'] === 'active'" />
    </div>
</x-layouts.admin>
