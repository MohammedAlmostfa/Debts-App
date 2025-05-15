<?php

namespace App\Http\Controllers;

use App\Models\Store;
use GuzzleHttp\Psr7\Request;
use App\Services\StoreService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\StoreResource;
use App\Http\Requests\StoreRequest\FilteringData;

use App\Http\Requests\StoreRequest\StoreStoreData;
use App\Http\Requests\StoreRequest\UpdateStoreData;

/**
 * StoreController manages store-related operations, such as:
 * - Retrieving a list of stores
 * - Showing store details (including debts)
 * - Creating, updating, and deleting store records
 */
class StoreController extends Controller
{
    /**
     * @var StoreService $storeService Handles store business logic
     */
    protected StoreService $storeService;

    /**
     * StoreController Constructor
     * Initializes the StoreService dependency for handling store-related logic.
     *
     * @param StoreService $storeService Dependency injected service for store operations
     */
    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * Retrieve and paginate a list of stores.
     *
     * @return JsonResponse Returns paginated list of stores or error response
     */
    public function index(FilteringData $request): JsonResponse
    {
        $result = $this->storeService->getAllStores($request->validated());
        return $result['status'] === 200
             ? $this->successshow($result['data'], $result['message'], $result['status'])
             : $this->error($result['data'], $result['message'], $result['status']);
    }

    /**
     * Store a new store record in the database.
     *
     * @param StoreStoreData $request Validated request data containing:
     *    - name (string, required): Name of the store
     *    - phone (string, required): Phone number of the store
     *    - notes (string, optional): Additional notes about the store
     * @return JsonResponse Returns JSON response with operation result
     */
    public function store(StoreStoreData $request): JsonResponse
    {
        $result = $this->storeService->createStore($request->validated());

        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }

    /**
     * Update an existing store record.
     *
     * @param UpdateStoreData $request Validated request data containing fields to update:
     *    - name (string, optional): Updated name of the store
     *    - phone (string, optional): Updated phone number of the store
     *    - notes (string, optional): Updated notes for the store
     * @param Store $store The store model instance to be updated
     * @return JsonResponse Returns JSON response with operation result
     */
    public function update(UpdateStoreData $request, Store $store): JsonResponse
    {
        $result = $this->storeService->updateStore(
            $request->validated(),
            $store
        );

        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }
    /**
    * Retrieve store details along with its debts.
    *
    * @param int $id Store ID.
    * @return JsonResponse Returns store debts or error response.
    */
    public function show($id): JsonResponse
    {
        $result = $this->storeService->getStoreDebts($id);

        return $result['status'] === 200
            ? $this->successshow($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }
    /**
     * Delete a store record from the database.
     *
     * @param Store $store The store model instance to be deleted
     * @return JsonResponse Returns JSON response with operation result
     */
    public function destroy(Store $store): JsonResponse
    {
        $result = $this->storeService->deleteStore($store);

        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }
}
