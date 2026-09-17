<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCrmOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lead_id' => ['nullable', 'exists:crm_leads,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'pipeline_id' => ['required', 'exists:crm_pipelines,id'],
            'stage_id' => ['required', 'exists:crm_pipeline_stages,id'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'source_id' => ['nullable', 'exists:crm_lead_sources,id'],
            'campaign_id' => ['nullable', 'exists:crm_campaigns,id'],
            'value' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'probability' => ['nullable', 'integer', 'between:0,100'],
            'expected_close_date' => ['nullable', 'date'],
        ];
    }
}
