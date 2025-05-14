<?php

namespace App\Services;

use App\Models\CustomerDebts;
use App\Models\Debt;
use Exception;
use Carbon\Carbon;
use App\Models\Receipt;
use Illuminate\Support\Facades\Log;

class FinancialReportService
{
    /**
     * Generate a financial report for the given date range.
     *
     * @param array $data Associative array containing 'start_date' and 'end_date' keys (optional).
     * @return array Structured response containing the report data or an error message.
     */
    public function getFinancialReport($data): array
    {
        try {
            // Parse and format the start and end dates (default to earliest receipt or today)
            $startDate = Carbon::parse($data['start_date'] ?? Receipt::first()?->receipt_date ?? now())->toDateString();
            $endDate = Carbon::parse($data['end_date'] ?? now())->toDateString();

            // Fetch store debt totals
            $storeDebtTotals = Debt::whereBetween('debt_date', [$startDate, $endDate])
                ->selectRaw('SUM(credit) as total_credit, SUM(debit) as total_debit')
                ->first();

            $storeCredit = $storeDebtTotals->total_credit ?? 0;
            $storeDebit = $storeDebtTotals->total_debit ?? 0;

            // Fetch customer debt totals
            $customerDebtTotals = CustomerDebts::whereBetween('debt_date', [$startDate, $endDate])
                ->selectRaw('SUM(credit) as total_credit, SUM(debit) as total_debit')
                ->first();

            $customerCredit = $customerDebtTotals->total_credit ?? 0;
            $customerDebit = $customerDebtTotals->total_debit ?? 0;

            // Organize report data
            $reportData = [
                'store_credit'   => $storeCredit,
                'store_debit'    => $storeDebit,
                'customer_credit' => $customerCredit,
                'customer_debit'  => $customerDebit,
            ];

            return $this->successResponse($reportData, 'تم استرجاع التقرير المالي بنجاح.');

        } catch (Exception $e) {
            Log::error("Unexpected error in getFinancialReport: " . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء توليد التقرير المالي، يرجى المحاولة لاحقًا.');
        }
    }

    /**
     * Generate a standardized success response.
     *
     * @param mixed $data The response data payload.
     * @param string $message Success message.
     * @param int $status HTTP status code (default: 200).
     * @return array Structured response array.
     */
    private function successResponse($data, string $message, int $status = 200): array
    {
        return [
            'message' => $message,
            'status'  => $status,
            'data'    => $data,
        ];
    }

    /**
     * Generate a standardized error response.
     *
     * @param string $message Error message.
     * @param int $status HTTP status code (default: 500).
     * @return array Structured response array.
     */
    private function errorResponse(string $message, int $status = 500): array
    {
        return [
            'message' => $message,
            'status'  => $status,
            'data'    => null,
        ];
    }
}
