<?php

namespace App\Services;

use App\Models\Debt;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service class for managing debt operations.
 * Provides methods for creating, updating, and deleting debt records.
 */
class DebtService
{
    /**
     * Create a new debt record.
     *
     * This method handles the creation of a new debt record, calculates the new balance,
     * and returns success or error responses based on the outcome.
     *
     * @param array $data Associative array with keys 'customer_id', 'credit', 'debit', 'debt_date', and 'details'.
     * @return array Response structure including status, message, and the created debt data.
     */
    public function createDebt($data)
    {
        try {
            // Fetch the current total balance for the customer
            $currentBalance = Debt::where('customer_id', $data['customer_id'])
                                  ->latest('created_at')
                                  ->value('total_balance') ?? 0;

            // Calculate the new balance based on credit or debit input
            if (!empty($data['credit'])) {
                $newBalance = $currentBalance + $data['credit'];
            } elseif (!empty($data['debit'])) {
                if ($data['debit'] > $currentBalance) {
                    return $this->errorResponse('المبلغ المطلوب أكبر من الرصيد المتوفر.');
                }
                $newBalance = $currentBalance - $data['debit'];
            } else {
                return $this->errorResponse('يجب تقديم قيمة صحيحة لـ credit أو debit.');
            }

            // Create the new debt record
            $debt = Debt::create([
                'customer_id' => $data['customer_id'],
                'credit' => $data['credit'] ?? null,
                'debit' => $data['debit'] ?? null,
                'debt_date' => $data['debt_date'] ?? now(),
                'total_balance' => $newBalance,
                'details' => $data['details'] ?? null,
            ]);

            return $this->successResponse($debt, 'تم تسجيل الدين بنجاح.');
        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Create debt error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء عملية التسجيل. يرجى المحاولة لاحقًا.');
        }
    }

    /**
     * Update an existing debt record.
     *
     * Updates the details and balance of a specific debt record.
     *
     * @param array $data Updated debt data including 'credit', 'debit', 'debt_date', and 'details'.
     * @param Debt $debt The debt record to be updated.
     * @return array Response structure including status, message, and the updated debt data.
     */
    public function updateDebt($data, Debt $debt)
    {
        try {
            // Fetch the latest debt record ID for the customer
            $lastDebtId = Debt::where('customer_id', $debt->customer_id)
                              ->latest('created_at')
                              ->value('id');

            if ($lastDebtId == $debt->id) {
                // Calculate new balance for the update
                $newBalance = $debt->total_balance;

                if (!empty($data['credit'])) {
                    $newBalance += $data['credit'] - ($debt->credit ?? 0);
                    $debt->debit = null; // Reset debit if credit is provided
                } elseif (!empty($data['debit'])) {
                    if ($data['debit'] > $newBalance) {
                        return $this->errorResponse('المبلغ المطلوب أكبر من الرصيد المتوفر.');
                    }
                    $newBalance -= $data['debit'] - ($debt->debit ?? 0);
                    $debt->credit = null; // Reset credit if debit is provided
                }

                // Update the debt record
                $debt->update([
                    'credit' => $data['credit'] ?? $debt->credit,
                    'debit' => $data['debit'] ?? $debt->debit,
                    'debt_date' => $data['debt_date'] ?? $debt->debt_date,
                    'total_balance' => $newBalance,
                    'details' => $data['details'] ?? $debt->details,
                ]);

                return $this->successResponse($debt, 'تم تحديث الدين بنجاح.');
            } else {
                return $this->errorResponse('لا يمكن تحديث إلا أحدث سجل.');
            }
        } catch (Exception $e) {
            Log::error('Update debt error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث الدين. يرجى المحاولة لاحقًا.');
        }
    }

    /**
     * Delete an existing debt record.
     *
     * Deletes the specified debt record only if it is the latest record for the customer.
     *
     * @param Debt $debt The debt record to be deleted.
     * @return array Response structure including status and message.
     */
    public function deleteDebt(Debt $debt)
    {
        try {
            // Fetch the latest debt record ID for the customer
            $lastDebtId = Debt::where('customer_id', $debt->customer_id)
                              ->latest('created_at')
                              ->value('id');

            if ($lastDebtId == $debt->id) {
                $debt->delete();
                return $this->successResponse(null, 'تم حذف الدين بنجاح.');
            } else {
                return $this->errorResponse('لا يمكن حذف إلا أحدث سجل.');
            }
        } catch (Exception $e) {
            Log::error('Delete debt error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف الدين. يرجى المحاولة لاحقًا.');
        }
    }

    /**
     * Generate a success response structure.
     *
     * @param mixed $data Data to be included in the response.
     * @param string $message Success message.
     * @param int $status HTTP status code (default is 200).
     * @return array Success response structure.
     */
    private function successResponse($data, string $message, int $status = 200): array
    {
        return [
            'message' => $message,
            'status' => $status,
            'data' => $data,
        ];
    }

    /**
     * Generate an error response structure.
     *
     * @param string $message Error message.
     * @param int $status HTTP status code (default is 500).
     * @return array Error response structure.
     */
    private function errorResponse(string $message, int $status = 500): array
    {
        return [
            'message' => $message,
            'status' => $status,
            'data' => null,
        ];
    }
}
