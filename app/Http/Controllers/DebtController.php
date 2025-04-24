<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Services\DebtService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\DebetRequest\StorDebetData;
use App\Http\Requests\DebetRequest\UpdateDebetData;

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
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }

    public function update(UpdateDebetData $request, Debt $debt): JsonResponse
    {
        // Process creation through service layer
        $result = $this->DebtService->updateDebt($request->validated(), $debt);

        // Return appropriate response based on status code
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }
    public function destroy(Debt $debt): JsonResponse
    {
        // Process creation through service layer
        $result = $this->DebtService->deleteDebt($debt);

        // Return appropriate response based on status code
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }




}
