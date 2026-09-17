<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCrmActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activity_type' => ['required', 'in:task,call,meeting,email,whatsapp,note,follow_up,visit,other'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lead_id' => ['nullable', 'exists:crm_leads,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'opportunity_id' => ['nullable', 'exists:crm_opportunities,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'status' => ['nullable', 'in:pending,scheduled,completed,cancelled,overdue'],
        ];
    }
}
