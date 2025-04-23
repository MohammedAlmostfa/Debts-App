<?php

namespace App\Http\Requests\DebetRequest;

use Illuminate\Foundation\Http\FormRequest;

class StorDebetData extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Allow all users to execute this request.
     */
    public function authorize(): bool
    {
        return true; // تسمح للجميع بتنفيذ الطلب
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Define rules for validating the incoming data.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer|exists:customers,id',
            'credit' => 'nullable|numeric|min:0|required_without:debit',
            'debit'  => 'nullable|numeric|min:0|required_without:credit',
            'debt_date' => 'nullable|date',
'details'=>'nullable|string'
        ];
    }
}
