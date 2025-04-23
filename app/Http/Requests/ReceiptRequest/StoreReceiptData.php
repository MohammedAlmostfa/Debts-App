<?php

namespace App\Http\Requests\ReceiptRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceiptData extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Always allow the request to proceed.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Validation rules for creating a receipt:
     * - customer_name (required, string)
     * - total_amount (required, numeric)
     * - receipt_number (required, string, unique)
     * - receipt_date (required, date)
     * - items (array containing receipt item details)
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'receipt_number' => 'required|string|unique:receipts,receipt_number|max:50',
            'receipt_date' => 'required|date',
            'items' => 'required|array',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}
