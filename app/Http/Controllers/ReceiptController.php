<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Services\ReceiptService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ReceiptRequest\StoreReceiptData;
use App\Http\Requests\ReceiptRequest\UpdateReceiptData;
use App\Http\Requests\ReceiptRequest\fitrtingReceiptData;

/**
 * Class ReceiptController
 * Handles receipt-related HTTP requests
 * including creating, retrieving, updating, and deleting receipts.
 */
class ReceiptController extends Controller
{
    /**
     * @var ReceiptService $ReceiptService Handles business logic for receipt operations.
     */
    protected ReceiptService $ReceiptService;

    /**
     * ReceiptController constructor
     *
     * Injects the ReceiptService to handle receipt-related operations.
     *
     * @param ReceiptService $ReceiptService The service layer for receipt management.
     */
    public function __construct(ReceiptService $ReceiptService)
    {
        $this->ReceiptService = $ReceiptService;
    }

    /**
     * Retrieve all receipts with optional pagination.
     *
     * @return JsonResponse Returns JSON response containing receipts or error message.
     */
    public function index(fitrtingReceiptData $request): JsonResponse
    {
        $result = $this->ReceiptService->getAllReceipts($request->validated());

        return $result['status'] === 200
            ? $this->successshow($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Retrieve all items associated with a specific receipt.
     *
     * @param Receipt $receipt The receipt instance for which items are retrieved.
     * @return JsonResponse Returns JSON response containing receipt items or error message.
     */
    public function getReceiptItems(Receipt $receipt): JsonResponse
    {
        $result = $this->ReceiptService->getReceiptItems($receipt);

        return $result['status'] === 200
            ? $this->successshow($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Create a new receipt along with its items.
     *
     * @param StoreReceiptData $request Validated request data for the new receipt.
     * @return JsonResponse Returns JSON response indicating success or failure.
     */
    public function store(StoreReceiptData $request): JsonResponse
    {
        $result = $this->ReceiptService->createReceipt($request->validated());

        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Update an existing receipt and its items.
     *
     * @param UpdateReceiptData $request Validated update data for the receipt.
     * @param Receipt $receipt The receipt instance to be updated.
     * @return JsonResponse Returns JSON response indicating success or failure.
     */
    public function update(UpdateReceiptData $request, Receipt $receipt): JsonResponse
    {
        $result = $this->ReceiptService->updateReceipt($request->validated(), $receipt);

        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }

    /**
     * Delete a receipt and optionally its associated items.
     *
     * @param Receipt $receipt The receipt instance to be deleted.
     * @return JsonResponse Returns JSON response indicating success or failure.
     */
    public function destroy(Receipt $receipt): JsonResponse
    {
        $result = $this->ReceiptService->deletReceipt($receipt);

        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }
}
