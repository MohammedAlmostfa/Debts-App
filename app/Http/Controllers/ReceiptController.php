<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiptRequest\StoreReceiptData;
use App\Models\Receipt;
use App\Services\ReceiptService;
use Illuminate\Http\JsonResponse;

/**
 * ReceiptController handles receipt-related HTTP requests
 * including creation and management of receipts.
 */
class ReceiptController extends Controller
{
    /**
     * @var ReceiptService $ReceiptService Handles business logic for receipt operations
     */
    protected ReceiptService $ReceiptService;

    /**
     * Constructor for dependency injection
     *
     * @param ReceiptService $ReceiptService Injected receipt service instance
     */
    public function __construct(ReceiptService $ReceiptService)
    {
        $this->ReceiptService = $ReceiptService;
    }
    public function index(): JsonResponse
    {
        $result = $this->ReceiptService->getAllReceipts();
        return $result['status'] === 200
                   ? $this->success($result['data'], $result['message'], $result['status'])
                   : $this->error(null, $result['message'], $result['status']);

    }
    public function getReceiptItems(Receipt $receipt): JsonResponse
    {
        $result = $this->ReceiptService->getReceiptItems($receipt);
        return $result['status'] === 200
                   ? $this->success($result['data'], $result['message'], $result['status'])
                   : $this->error(null, $result['message'], $result['status']);

    }
    /**
     * Store a new receipt record.
     *
     * @param StoreReceiptData $request Validated receipt request data
     * @return JsonResponse Returns JSON response indicating success or failure
     */
    public function store(StoreReceiptData $request): JsonResponse
    {
        // Process creation through service layer
        $result = $this->ReceiptService->createReceipt($request->validated());

        // Return appropriate response based on status code
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error(null, $result['message'], $result['status']);
    }
}
