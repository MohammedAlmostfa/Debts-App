<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\ReceiptItem;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Handles receipt creation logic
 */
class ReceiptService
{
    public function getAllReceipts()
    {
        try {
            // Fetch receipts with pagination (10 receipts per page)
            $receipts = Receipt::all();

            // Return successful response
            return [
                'status' => 200,
                'message' => 'Receipts retrieved successfully',
                'data' => $receipts,
            ];
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error fetching receipts: ' . $e->getMessage());

            // Return error response
            return [
                'status' => 500,
                'message' => 'Failed to retrieve receipts',
            ];
        }
    }



















    /**
     * Create a new receipt along with its items.
     *
     * @param array $data Contains receipt and receipt items data
     * @return array Response indicating success or failure
     */
    public function createReceipt($data)
    {
        try {
            // Create the receipt
            $receipt = Receipt::create([
                'customer_name' => $data['customer_name'],
                'total_amount' => $data['total_amount'],
                'receipt_number' => $data['receipt_number'],
                'receipt_date' => $data['receipt_date'],
            ]);

            // Create associated receipt items
            foreach ($data['items'] as $item) {
                $receipt->receiptItems()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            return [
                'status' => 200,
                'message' => 'Receipt created successfully',
                'data' => $receipt,
            ];
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error creating receipt: ' . $e->getMessage());

            return [
                'status' => 500,
                'message' => 'Failed to create receipt',
            ];
        }
    }
}
