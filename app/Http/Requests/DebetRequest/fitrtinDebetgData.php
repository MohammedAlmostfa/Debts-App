<?php

namespace App\Http\Requests\DebetRequest;

use Illuminate\Foundation\Http\FormRequest;

class fitrtinDebetgData extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'credit' => 'nullable',
            'debit'  => 'nullable',
            'debt_date' => 'nullable|date|before_or_equal:now',
                    'receipt_id' => 'nullable|numeric'
        ];
    }
}
