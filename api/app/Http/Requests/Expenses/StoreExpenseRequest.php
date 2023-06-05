<?php

namespace App\Http\Requests\Expenses;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => 'required|string|max:191|',
            'reference_date' => 'required|date|before_or_equal:today',
            'value' => 'required|numeric|min:0',
        ];
    }

}
