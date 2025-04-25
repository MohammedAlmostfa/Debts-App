<?php

namespace App\Services;

use Exception;
use App\Models\Debt;
use App\Events\DebtProcessed;
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
     * @param array $data Associative array with keys:
     *                    - 'customer_id': int
     *                    - 'credit': float|null
     *                    - 'debit': float|null
     *                    - 'debt_date': string|DateTime|null
     *                    - 'details': string|null
     * @return array
     */
    public function createDebt($data)
    {
        try {
            // Get the last total balance for the customer (or 0 if no previous debt)
            $currentBalance = Debt::where('customer_id', $data['customer_id'])
                                  ->latest('created_at')
                                  ->value('total_balance') ?? 0;

            // Calculate the new balance based on credit or debit
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

            // Create the debt record
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
            Log::error('Create debt error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء عملية التسجيل. يرجى المحاولة لاحقًا.');
        }
    }

    /**
     * Update an existing debt record.
     *
     * @param array $data Updated values for credit, debit, etc.
     * @param Debt $debt The debt instance to update.
     * @return array
     */
    public function updateDebt($data, Debt $debt)
    {
        try {
            $newBalance = $debt->total_balance;

            // Handle credit update
            if (!empty($data['credit'])) {
                $newBalance += $data['credit'] - ($debt->credit ?? 0);

                $differenceAmount = $newBalance - $debt->total_balance;
                event(new DebtProcessed($debt->id, $debt->customer_id, $differenceAmount));
                $debt->debit = null; // Reset debit if credit is provided
                // Handle debit update
            } elseif (!empty($data['debit'])) {
                if ($data['debit'] > $newBalance) {
                    return $this->errorResponse('المبلغ المطلوب أكبر من الرصيد المتوفر.');
                }

                $newBalance -= $data['debit'] - ($debt->debit ?? 0);

                $differenceAmount = $newBalance - $debt->total_balance;
                event(new DebtProcessed($debt->id, $debt->customer_id, $differenceAmount));



                $debt->credit = null; // Reset credit if debit is provided
            }

            // Apply the updates
            $debt->update([
                'credit' => $data['credit'] ?? $debt->credit,
                'debit' => $data['debit'] ?? $debt->debit,
                'debt_date' => $data['debt_date'] ?? $debt->debt_date,
                'total_balance' => $newBalance,
                'details' => $data['details'] ?? $debt->details,
            ]);

            return $this->successResponse($debt, 'تم تحديث الدين بنجاح.');
        } catch (Exception $e) {
            Log::error('Update debt error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث الدين. يرجى المحاولة لاحقًا.');
        }
    }







    /**
     * Delete an existing debt record.
     *
     * @param Debt $debt The debt to delete.
     * @return array
     */
    public function deleteDebt(Debt $debt)
    {
        try {
            // Handle credit removal
            if (!empty($debt->credit)) {
                $data = -$debt->credit;
                event(new DebtProcessed($debt->id, $debt->customer_id, $data));


                $debt->debit = null;
                $debt->save();

                // Handle debit removal
            } elseif (!empty($debt->debit)) {
                $data = $debt->debit;

                event(new DebtProcessed($debt->id, $debt->customer_id, $data));


            }

            // Delete the debt
            $debt->delete();

            return $this->successResponse(null, 'تم حذف الدين بنجاح.');
        } catch (Exception $e) {
            Log::error('Delete debt error: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف الدين. يرجى المحاولة لاحقًا.');
        }
    }

    /**
     * Generate a success response structure.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return array
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
     * @param string $message
     * @param int $status
     * @return array
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
