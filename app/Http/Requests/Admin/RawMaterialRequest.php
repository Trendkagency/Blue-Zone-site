<?php

namespace App\Http\Requests\Admin;

use App\Enums\RawMaterialCategory;
use App\Enums\StorageCondition;
use App\Enums\UnitOfMeasure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RawMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $materialId = $this->route('raw_material')?->id ?? $this->route('id');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('raw_materials', 'code')->ignore($materialId)],
            'name_en' => ['required', 'string', 'max:191'],
            'name_ar' => ['required', 'string', 'max:191'],
            'inci_name' => ['nullable', 'string', 'max:191'],
            'cas_number' => ['nullable', 'string', 'max:50'],
            'category' => ['required', Rule::enum(RawMaterialCategory::class)],
            'unit_of_measure' => ['required', Rule::enum(UnitOfMeasure::class)],
            'storage_condition' => ['required', Rule::enum(StorageCondition::class)],
            'standard_cost' => ['required', 'numeric', 'min:0'],
            'min_reorder_qty' => ['required', 'numeric', 'min:0'],
            'safety_stock' => ['required', 'numeric', 'min:0'],
            'shelf_life_days' => ['required', 'integer', 'min:30', 'max:3650'],
            'requires_coa' => ['boolean'],
            'default_supplier_id' => ['nullable', 'exists:suppliers,id'],
            'is_active' => ['boolean'],
        ];
    }
}
