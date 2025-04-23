<?php

namespace App\Http\Controllers;

use App\Http\Requests\DebetRequest\StorDebetData;
use App\Services\DebtService;
use Illuminate\Http\JsonResponse;

class DebtController extends Controller
{
    /**
     * @var DebtService $DebtService Handles business logic for debt operations
     */
    protected DebtService $DebtService;

    /**
     * Constructor for dependency injection
     *
     * @param DebtService $DebtService Injected debt service instance
     */
    public function __construct(DebtService $DebtService)
    {
        $this->DebtService = $DebtService;
    }

    /**
     * Store a new debt record.
     *
     * @param StorDebetData $request Validated debt request data
     * @return JsonResponse
     */
    public function store(StorDebetData $request): JsonResponse
    {
        // Process creation through service layer
        $result = $this->DebtService->createDebt($request->validated());

        // Return appropriate response based on status code
        return $result['status'] === 201
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }
}
