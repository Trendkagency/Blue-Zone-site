<?php

namespace App\Http\Requests\Admin;

use App\Enums\SupplierType;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Auto-generate supplier code if empty
        if (!$this->has('code') || empty(trim((string) $this->input('code')))) {
            $merge['code'] = Supplier::generateNextCode();
        }

        // Handle contact name aliases
        if ($this->has('contact_person') && !$this->has('contact_name')) {
            $merge['contact_name'] = $this->input('contact_person');
        }

        // Handle audit valid date aliases and default to 1-year GMP lifecycle if empty
        if ($this->has('audit_valid_until') && !$this->has('audit_expiry')) {
            $merge['audit_expiry'] = $this->input('audit_valid_until');
        }
        if (!$this->has('audit_expiry') || empty(trim((string) $this->input('audit_expiry')))) {
            $merge['audit_expiry'] = now()->addYear()->format('Y-m-d');
        }

        // Default currency to SAR if missing or empty
        if (!$this->has('currency') || empty(trim((string) $this->input('currency')))) {
            $merge['currency'] = 'SAR';
        }

        // Default lead time days to 14 if missing
        if (!$this->has('lead_time_days') || empty($this->input('lead_time_days'))) {
            $merge['lead_time_days'] = 14;
        }

        // Default payment terms if missing
        if (!$this->has('payment_terms') || empty(trim((string) $this->input('payment_terms')))) {
            $merge['payment_terms'] = 'Net 30';
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')?->id ?? $this->route('id');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('suppliers', 'code')->ignore($supplierId)],
            'type' => ['required', Rule::enum(SupplierType::class)],
            'name_en' => ['required', 'string', 'max:191'],
            'name_ar' => ['nullable', 'string', 'max:191'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'gmp_certified' => ['boolean'],
            'iso_certified' => ['boolean'],
            'audit_expiry' => ['nullable', 'date'],
            'audit_valid_until' => ['nullable', 'date'],
            'payment_terms' => ['required', 'string', 'max:50'],
            'currency' => ['required', 'string', 'size:3'],
            'lead_time_days' => ['required', 'integer', 'min:1', 'max:365'],
            'cgmp_document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'doc_cgmp' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'iso_document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'doc_iso' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'doc_halal' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'doc_allergen' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
