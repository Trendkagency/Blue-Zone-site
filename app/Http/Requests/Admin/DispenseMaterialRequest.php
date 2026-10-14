<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DispenseMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'raw_material_batch_id' => ['required', 'exists:raw_material_batches,id'],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'scanned_barcode' => ['required', 'string', 'max:100'],
        ];
    }
}
