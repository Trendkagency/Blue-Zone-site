<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BatchAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_quantity' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'min:10', 'max:255'],
            'second_approver_id' => ['nullable', 'exists:users,id', 'different:' . auth()->id()],
        ];
    }
}
