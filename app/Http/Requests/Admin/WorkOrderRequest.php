<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'planned_qty' => ['required', 'integer', 'min:1'],
            'location_id' => ['required', 'string', 'exists:locations,id'],
            'scheduled_start' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
