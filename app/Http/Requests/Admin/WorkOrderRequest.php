<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('target_quantity') && ! $this->filled('planned_qty')) {
            $merge['planned_qty'] = $this->input('target_quantity');
        }

        if ($this->has('scheduled_date') && ! $this->filled('scheduled_start')) {
            $merge['scheduled_start'] = $this->input('scheduled_date');
        }

        if ($this->has('product_batch_number') && ! $this->filled('target_finished_lot')) {
            $merge['target_finished_lot'] = $this->input('product_batch_number');
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'planned_qty' => ['required', 'integer', 'min:1'],
            'target_finished_lot' => ['nullable', 'string', 'max:100'],
            'location_id' => ['required', 'string'],
            'scheduled_start' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'auto_reserve' => ['nullable', 'boolean'],
        ];
    }
}
