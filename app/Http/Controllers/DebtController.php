<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Services\DebtService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\DebetRequest\StorDebetData;
use App\Http\Requests\DebetRequest\UpdateDebetData;

/**
 * Class DebtController
 * Handles debt-related HTTP requests, including creating,
 * updating, and deleting debt records.
 */
class DebtController extends Controller
{
    /**
     * @var DebtService $DebtService Service layer for handling debt business logic.
     */
    protected DebtService $DebtService;

    /**
     * DebtController constructor
     * Injects the DebtService to handle debt-related operations.
     *
     * @param DebtService $DebtService The service instance for managing debts.
     */
    public function __construct(DebtService $DebtService)
    {
        $this->DebtService = $DebtService;
    }

    /**
     * Create a new debt record.
     *
     * This method handles the creation of a new debt record by
     * validating the incoming request through `StorDebetData`.
     * The business logic is delegated to the DebtService.
     *
     * @param StorDebetData $request Validated data for the new debt record.
     * @return JsonResponse Returns a JSON response with the created debt or an error message.
     */
    public function store(StorDebetData $request): JsonResponse
    {
        // Process the request using the DebtService
        $result = $this->DebtService->createDebt($request->validated());

        // Return an appropriate response based on the result status
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }

    /**
     * Update an existing debt record.
     *
     * This method updates a debt record identified by its ID.
     * The `UpdateDebetData` validates the update request, and
     * the DebtService handles the update logic.
     *
     * @param UpdateDebetData $request Validated update data for the debt record.
     * @param Debt $debt The debt record to update.
     * @return JsonResponse Returns a JSON response with the updated debt or an error message.
     */
    public function update(UpdateDebetData $request, Debt $debt): JsonResponse
    {
        // Process the update using the DebtService
        $result = $this->DebtService->updateDebt($request->validated(), $debt);

        // Return an appropriate response based on the result status
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }


    /**
     * Delete a debt record.
     *
     * This method deletes a debt record identified by its ID.
     * The DebtService handles the logic for deletion.
     *
     * @param Debt $debt The debt record to delete.
     * @return JsonResponse Returns a JSON response confirming the deletion or an error message.
     */
    public function destroy(Debt $debt): JsonResponse
    {
        // Process the deletion using the DebtService
        $result = $this->DebtService->deleteDebt($debt);

        // Return an appropriate response based on the result status
        return $result['status'] === 200
            ? $this->success(new $result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }
}
