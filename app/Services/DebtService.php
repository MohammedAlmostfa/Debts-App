<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Debt;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Handles customer CRUD operations
 */
class DebtService
{

    public function createDebt($data)
    {
        try {
            // Fetch the current total_balance for the customer
            $currentBalance = Debt::where('customer_id', $data['customer_id'])
                                  ->latest('created_at')
                                  ->value('total_balance') ?? 0; // Default to 0 if no balance exists

            // Calculate the new balance based on credit or debit
            if (!empty($data['credit'])) {
                $newBalance = $currentBalance + $data['credit'];
            } elseif (!empty($data['debit'])) {
                if ($data['debit'] > $currentBalance) {
                    return $this->errorResponse('المبلغ المستعاد أكبر من المبلغ المستدان في الحساب.');
                }
                $newBalance = $currentBalance - $data['debit'];
            }

            // Create the new debt record
            $debt = Debt::create([
                'customer_id' => $data['customer_id'],
                'credit' => $data['credit'] ,
                'debit' => $data['credit'] ,
                'debt_date' => $data['debt_date'] ?? now(),
                'total_balance' => $newBalance,
                "details"=>$data["details"],
            ]);

            return $this->successResponse($debt, ' تم تسجيل الدين بنجاح ');
        } catch (Exception $e) {
            // Log any errors encountered
            Log::error('Create debt error: ' . $e->getMessage());
            return $this->errorResponse('حدذ خطا يرجا اعادة المحاولة');
        }
    }

    private function successResponse($data, string $message, int $status = 200): array
    {
        return [
            'message' => $message,
            'status' => $status,
            'data' => $data
        ];
    }

    private function errorResponse(string $message, int $status = 500): array
    {
        return [
            'message' => $message,
            'status' => $status,
            'data' => null
        ];
    }
}
