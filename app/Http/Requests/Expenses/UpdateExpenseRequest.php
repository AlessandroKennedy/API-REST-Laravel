<?php

namespace App\Http\Requests\Expenses;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => 'string|max:191|',
            'reference_date' => 'date|before_or_equal:today',
            'value' => 'numeric|min:0',
        ];
    }
}
