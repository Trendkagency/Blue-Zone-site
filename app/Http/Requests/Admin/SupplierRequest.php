<?php

namespace App\Http\Requests\Admin;

use App\Enums\SupplierType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')?->id ?? $this->route('id');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('suppliers', 'code')->ignore($supplierId)],
            'type' => ['required', Rule::enum(SupplierType::class)],
            'name_en' => ['required', 'string', 'max:191'],
            'name_ar' => ['required', 'string', 'max:191'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'gmp_certified' => ['boolean'],
            'iso_certified' => ['boolean'],
            'audit_expiry' => ['nullable', 'date'],
            'payment_terms' => ['required', 'string', 'max:50'],
            'currency' => ['required', 'string', 'size:3'],
            'lead_time_days' => ['required', 'integer', 'min:1', 'max:365'],
            'cgmp_document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'iso_document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
