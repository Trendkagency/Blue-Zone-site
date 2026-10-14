<?php

namespace App\Http\Requests\Admin;

use App\Enums\QcVerdict;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QcInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'raw_material_batch_id' => ['required', 'exists:raw_material_batches,id'],
            'sample_size' => ['required', 'numeric', 'min:0.0001'],
            'sample_uom' => ['required', 'string', 'max:10'],
            'verdict' => ['required', Rule::enum(QcVerdict::class)],
            'waiver_reason' => ['nullable', 'required_if:verdict,conditional_pass', 'string'],
            'waiver_expires_at' => ['nullable', 'required_if:verdict,conditional_pass', 'date', 'after:today'],
            'notes' => ['nullable', 'string'],
            'coa_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'parameters' => ['required', 'array', 'min:1'],
            'parameters.*.parameter_name' => ['required', 'string', 'max:100'],
            'parameters.*.specification' => ['required', 'string', 'max:150'],
            'parameters.*.result_value' => ['required', 'string', 'max:150'],
            'parameters.*.status' => ['required', 'in:pass,fail'],
        ];
    }
}
