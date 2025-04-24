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
    /**
     * Retrieve all receipts with pagination.
     *
     * @return array Response indicating success or failure
     */
    public function getAllReceipts()
    {
        try {
            // Fetch receipts with pagination (10 receipts per page)
            $receipts = Receipt::all();

            return $this->successResponse($receipts, 'تم استرجاع الإيصالات بنجاح.');
        } catch (Exception $e) {
            Log::error('خطأ أثناء استرجاع الإيصالات: ' . $e->getMessage());

            return $this->errorResponse('فشل في استرجاع الإيصالات.');
        }
    }

    /**
     * Retrieve all receipt items for a specific receipt.
     *
     * @param Receipt $receipt The receipt to retrieve items for
     * @return array Response indicating success or failure
     */
    public function getReceiptItems(Receipt $receipt)
    {
        try {
            // Fetch items associated with the receipt
            $receiptItems = $receipt->receiptItems;

            return $this->successResponse($receiptItems, 'تم استرجاع عناصر الإيصال بنجاح.');
        } catch (Exception $e) {
            Log::error('خطأ أثناء استرجاع عناصر الإيصال: ' . $e->getMessage());

            return $this->errorResponse('فشل في استرجاع عناصر الإيصال.');
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
                'total_price' => $data['total_price'],
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

            return $this->successResponse($receipt, 'تم إنشاء الإيصال بنجاح.');
        } catch (Exception $e) {
            Log::error('خطأ أثناء إنشاء الإيصال: ' . $e->getMessage());

            return $this->errorResponse('فشل في إنشاء الإيصال.');
        }
    }

    /**
     * Success response structure.
     */
    private function successResponse($data, string $message, int $status = 200): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Error response structure.
     */
    private function errorResponse(string $message, int $status = 500): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => null,
        ];
    }
}
