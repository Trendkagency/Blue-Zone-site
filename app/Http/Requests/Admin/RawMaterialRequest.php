<?php

namespace App\Http\Requests\Admin;

use App\Enums\RawMaterialCategory;
use App\Enums\StorageCondition;
use App\Enums\UnitOfMeasure;
use App\Models\RawMaterial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RawMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Map sku or code, or auto-generate if empty
        $code = $this->input('code') ?: $this->input('sku');
        if (empty(trim((string) $code))) {
            $merge['code'] = RawMaterial::generateNextCode();
        } else {
            $merge['code'] = trim((string) $code);
        }

        // Map uom alias to unit_of_measure
        if ($this->has('uom') && (!$this->has('unit_of_measure') || empty($this->input('unit_of_measure')))) {
            $merge['unit_of_measure'] = $this->input('uom');
        }

        // Normalize storage condition aliases if provided
        if ($this->has('storage_condition')) {
            $sc = (string) $this->input('storage_condition');
            $scMap = [
                'refrigerated_2_8c' => 'cold_2_8c',
                'cold' => 'cold_2_8c',
                'refrigerated' => 'cold_2_8c',
                'frozen' => 'frozen_minus_20c',
                'ambient' => 'ambient_15_25c',
            ];
            if (isset($scMap[$sc])) {
                $merge['storage_condition'] = $scMap[$sc];
            }
        }

        // Map reorder_level alias to min_reorder_qty
        if ($this->has('reorder_level') && (!$this->has('min_reorder_qty') || $this->input('min_reorder_qty') === null)) {
            $merge['min_reorder_qty'] = $this->input('reorder_level');
        }

        // Handle shelf_life_days vs shelf_life_months
        if ($this->has('shelf_life_months') && (!$this->has('shelf_life_days') || empty($this->input('shelf_life_days')))) {
            $months = max(1, (int) $this->input('shelf_life_months'));
            $merge['shelf_life_days'] = $months * 30;
        } elseif (!$this->has('shelf_life_days') || empty($this->input('shelf_life_days'))) {
            $merge['shelf_life_days'] = 730; // 2 years standard default
        }

        // Fallback name_ar to name_en if empty
        if (!$this->has('name_ar') || empty(trim((string) $this->input('name_ar')))) {
            $merge['name_ar'] = $this->input('name_en');
        }

        // Default standard_cost to 0.0000 if empty
        if (!$this->has('standard_cost') || $this->input('standard_cost') === null || $this->input('standard_cost') === '') {
            $merge['standard_cost'] = 0.0000;
        }

        // Default safety_stock to 0.0000 if empty
        if (!$this->has('safety_stock') || $this->input('safety_stock') === null || $this->input('safety_stock') === '') {
            $merge['safety_stock'] = 0.0000;
        }

        // Default min_reorder_qty to 0.0000 if still empty
        if (!isset($merge['min_reorder_qty']) && (!$this->has('min_reorder_qty') || $this->input('min_reorder_qty') === null || $this->input('min_reorder_qty') === '')) {
            $merge['min_reorder_qty'] = 0.0000;
        }

        // Default requires_coa to true for raw materials
        if (!$this->has('requires_coa')) {
            $merge['requires_coa'] = true;
        }

        // Default is_active to true
        if (!$this->has('is_active')) {
            $merge['is_active'] = true;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $materialId = $this->route('raw_material')?->id ?? $this->route('id');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('raw_materials', 'code')->ignore($materialId)],
            'sku' => ['nullable', 'string', 'max:50'],
            'name_en' => ['required', 'string', 'max:191'],
            'name_ar' => ['nullable', 'string', 'max:191'],
            'inci_name' => ['nullable', 'string', 'max:191'],
            'cas_number' => ['nullable', 'string', 'max:50'],
            'category' => ['required', Rule::enum(RawMaterialCategory::class)],
            'unit_of_measure' => ['required', Rule::enum(UnitOfMeasure::class)],
            'uom' => ['nullable', Rule::enum(UnitOfMeasure::class)],
            'storage_condition' => ['required', Rule::enum(StorageCondition::class)],
            'standard_cost' => ['required', 'numeric', 'min:0'],
            'min_reorder_qty' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['nullable', 'numeric', 'min:0'],
            'safety_stock' => ['required', 'numeric', 'min:0'],
            'shelf_life_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'shelf_life_months' => ['nullable', 'integer', 'min:1'],
            'requires_coa' => ['boolean'],
            'default_supplier_id' => ['nullable', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
