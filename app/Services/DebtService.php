<?php

namespace App\Services;

use Exception;
use App\Models\Debt;
use App\Events\DebtProcessed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service class for managing debt operations including creation,
 * modification, and deletion of debt records with proper balance tracking.
 */
class DebtService
{
    /**
     * Create a new debt record with proper balance calculation.
     *
     * @param array $data {
     *     @var int      $customer_id  Required. The customer ID
     *     @var float    $credit       Optional. The credit amount (positive value)
     *     @var float    $debit        Optional. The debit amount (positive value)
     *     @var string   $debt_date    Optional. The date of the transaction
     *     @var string   $details      Optional. Additional details
     * }
     * @return array Response array with status, message and data
     */
    public function createDebt($data)
    {
        try {
            // Get the current balance from the most recent debt record
            $currentBalance = Debt::where('customer_id', $data['customer_id'])
                                  ->latest('created_at')
                                  ->value('total_balance') ?? 0;

            // Validate and calculate new balance
            if (!empty($data['credit'])) {
                $newBalance = $currentBalance + $data['credit'];
            } elseif (!empty($data['debit'])) {
                // Prevent over-withdrawal
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
                'credit' => $data['credit'] ?? 0,
                'debit' => $data['debit'] ?? 0,
                'debt_date' => $data['debt_date'] ?? now(),
                'total_balance' => $newBalance,
                'receipt_id' => $data['receipt_id'] ?? null,
            ]);

            return $this->successResponse($debt, 'تم تسجيل الدين بنجاح.');

        } catch (Exception $e) {
            Log::error('Create debt error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء عملية التسجيل. يرجى المحاولة لاحقًا.');
        }
    }

    /**
     * Update an existing debt record with proper balance recalculation.
     * Handles special case of converting between credit and debit types.
     *
     * @param array $data Updated values (same structure as createDebt)
     * @param Debt $debt The debt record to update
     * @return array Response array with status, message and data
     */
    public function updateDebt($data, Debt $debt)
    {
        DB::beginTransaction();
        try {


            $debt->update([
                'credit' => $data['credit'] ?? 0,
                'debit' => $data['debit'] ?? 0,
                'debt_date' => $data['debt_date'] ?? $debt->debt_date,
                'receipt_id' => $data['receipt_id'] ?? $debt->receipt_id,
            ]);

            $debts = Debt::where('customer_id', $debt->customer_id)
                ->orderBy('id')
                ->get();

            $total = 0;
            foreach ($debts as $d) {
                $total += $d->credit - $d->debit;
                $d->update(['total_balance' => $total]);
            }

            DB::commit();

            return $this->successResponse($debt, 'تم تحديث الدين بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update debt error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث الدين. يرجى المحاولة لاحقًا.');
        }
    }
    /**
     * Delete a debt record and adjust subsequent balances.
     *
     * @param Debt $debt The debt record to delete
     * @return array Response array with status and message
     */
    public function deleteDebt(Debt $debt)
    {
        try {
            // Handle balance adjustment before deletion
            if (!empty($debt->credit)) {
                // Reverse the credit impact
                $adjustment = -$debt->credit;
                event(new DebtProcessed($debt->id, $debt->customer_id, $adjustment));
            } elseif (!empty($debt->debit)) {
                // Reverse the debit impact
                $adjustment = $debt->debit;
                event(new DebtProcessed($debt->id, $debt->customer_id, $adjustment));
            }

            // Delete the record
            $debt->delete();

            return $this->successResponse(null, 'تم حذف الدين بنجاح.');

        } catch (Exception $e) {
            Log::error('Delete debt error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف الدين. يرجى المحاولة لاحقًا.');
        }
    }

    /**
     * Generate a standardized success response.
     *
     * @param mixed $data The response data payload
     * @param string $message Success message
     * @param int $status HTTP status code (default: 200)
     * @return array Structured response array
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
     * Generate a standardized error response.
     *
     * @param string $message Error message
     * @param int $status HTTP status code (default: 500)
     * @return array Structured response array
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
