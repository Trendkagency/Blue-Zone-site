<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConvertCrmLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'existing_customer_id' => ['nullable', 'exists:customers,id'],
            'create_company' => ['nullable', 'boolean'],
            'create_opportunity' => ['nullable', 'boolean'],
            'opportunity_name' => ['nullable', 'string', 'max:255'],
            'pipeline_id' => ['nullable', 'exists:crm_pipelines,id'],
            'stage_id' => ['nullable', 'exists:crm_pipeline_stages,id'],
            'opportunity_value' => ['nullable', 'numeric', 'min:0'],
            'expected_close_date' => ['nullable', 'date'],
        ];
    }
}
