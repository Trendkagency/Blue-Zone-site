<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Step 1: Core & Identifiers
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:50'],
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_en' => ['nullable', 'string', 'max:100'],
            'subcategory_ar' => ['nullable', 'string', 'max:100'],
            'brand' => ['required', 'string', 'max:150'],
            'target_gender' => ['nullable', 'string', 'max:50'],
            'age_group' => ['nullable', 'string', 'max:50'],
            'product_size' => ['nullable', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],

            // Step 2: Multi-Lingual Content
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'tagline_en' => ['nullable', 'string', 'max:255'],
            'tagline_ar' => ['nullable', 'string', 'max:255'],
            'short_description_en' => ['nullable', 'string', 'max:500'],
            'short_description_ar' => ['nullable', 'string', 'max:500'],
            'description_en' => ['required', 'string'],
            'description_ar' => ['required', 'string'],
            'usage_en' => ['nullable', 'string'],
            'usage_ar' => ['nullable', 'string'],

            // Step 3: Pricing & Taxes
            'price' => ['required', 'numeric', 'min:0.01'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'cost_price' => ['required', 'numeric', 'min:0'],

            // Step 4: Media & Imagery
            'image' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array'],
            'primary_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg', 'max:10240'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg', 'max:10240'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],

            // Step 5: Clinical Section
            'clinical_mechanism' => ['nullable', 'string'],
            'formula_details' => ['nullable', 'string'],
            'contraindications' => ['nullable', 'string'],
            'warnings' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'array'],

            // Step 6: Inventory & Controls
            'stock_online' => ['required', 'integer', 'min:0'],
            'stock_offline' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:1'],
            'enable_backorders' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_best_seller' => ['nullable', 'boolean'],
            'is_new' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['active', 'draft', 'inactive'])],
        ];
    }

    /**
     * Custom attributes for human-readable error messages.
     */
    public function attributes(): array
    {
        return [
            'sku' => __('admin.products.fields.sku'),
            'category_id' => __('admin.products.fields.primary_system'),
            'name_en' => __('admin.products.fields.name_en'),
            'name_ar' => __('admin.products.fields.name_ar'),
            'description_en' => __('admin.products.fields.description_en'),
            'description_ar' => __('admin.products.fields.description_ar'),
            'price' => __('admin.products.fields.retail_price'),
            'cost_price' => __('admin.products.fields.cost_price'),
            'stock_online' => __('admin.products.fields.online_stock'),
            'stock_offline' => __('admin.products.fields.offline_stock'),
            'low_stock_threshold' => __('admin.products.fields.low_stock_threshold'),
        ];
    }
}
