<?php

namespace App\Http\Requests\Admin;

use App\Enums\StorageCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GoodsReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_order_id' => ['required', 'exists:purchase_orders,id'],
            'location_id' => ['required', 'string', 'exists:locations,id'],
            'received_date' => ['required', 'date'],
            'carrier_name' => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'temperature_logger_reading' => ['nullable', 'numeric', 'between:-50,60'],
            'temperature_within_range' => ['boolean'],
            'seal_verified' => ['boolean'],
            'container_count' => ['required', 'integer', 'min:1'],
            'discrepancy_notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.item_type' => ['nullable', 'string', 'in:raw_material,product'],
            'items.*.raw_material_id' => ['nullable', 'required_without:items.*.product_id', 'exists:raw_materials,id'],
            'items.*.product_id' => ['nullable', 'required_without:items.*.raw_material_id', 'exists:products,id'],
            'items.*.received_qty' => ['required', 'numeric', 'min:0.0001'],
            'items.*.rejected_qty' => ['nullable', 'numeric', 'min:0'],
            'items.*.supplier_lot' => ['required', 'string', 'max:100'],
            'items.*.manufacture_date' => ['required', 'date'],
            'items.*.expiry_date' => ['required', 'date', 'after:items.*.manufacture_date'],
            'items.*.storage_condition' => ['nullable', Rule::enum(StorageCondition::class)],
            'items.*.storage_bin' => ['nullable', 'string', 'max:50'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
