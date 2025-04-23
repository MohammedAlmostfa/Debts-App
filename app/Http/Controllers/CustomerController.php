<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\customerResource;
use App\Http\Requests\CustomerRequest\StoreCustomerData;
use App\Http\Requests\CustomerRequest\UpdateCustomerData;

/**
 * CustomerController handles all customer-related HTTP requests
 * including creation, updating, and deletion of customer records.
 */
class CustomerController extends Controller
{
    /**
     * @var CustomerService $customerService Handles business logic for customer operations
     */
    protected CustomerService $customerService;

    /**
     * Constructor for dependency injection
     *
     * @param CustomerService $customerService Injected customer service instance
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        $result = $this->customerService->getAllCustomers();
        return $result['status'] === 200
                ? $this->paginated($result['data'], customerResource::class, $result['message'], $result['status'])
                : $this->error($result['data'], $result['message'], $result['status']);
    }
    public function show($id)
    {
        $result = $this->customerService->getCustomerDebts($id);
        return $result['status'] === 200
             ? $this->success($result['data'], $result['message'], $result['status'])
             : $this->error($result['data'], $result['message'], $result['status']);
    }
    /**
     * Store a new customer record
     *
     * @param StoreCustomerData $request Validated request data containing:
     *    - name: string (required)
     *    - phone: string (required)
     *    - notes: string (optional)
     * @return JsonResponse Returns JSON response with:
     *    - success: boolean
     *    - message: string
     *    - data: Customer|null
     *    - status: integer (HTTP status code)
     */
    public function store(StoreCustomerData $request): JsonResponse
    {
        // Process creation through service layer
        $result = $this->customerService->createCustomer($request->validated());

        // Return appropriate response based on status code
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }

    /**
     * Update an existing customer record
     *
     * @param UpdateCustomerData $request Validated request data containing update fields
     * @param Customer $customer Customer model to be updated
     * @return JsonResponse Returns JSON response with operation result
     */
    public function update(UpdateCustomerData $request, Customer $customer): JsonResponse
    {
        // Process update through service layer
        $result = $this->customerService->updateCustomer(
            $request->validated(),
            $customer
        );

        // Return appropriate response based on status code
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }

    /**
     * Delete a customer record
     *
     * @param Customer $customer Customer model to be deleted
     * @return JsonResponse Returns JSON response with operation result
     */
    public function destroy(Customer $customer): JsonResponse
    {
        // Process deletion through service layer
        $result = $this->customerService->deleteCustomer($customer);

        // Return appropriate response based on status code
        return $result['status'] === 200
            ? $this->success($result['data'], $result['message'], $result['status'])
            : $this->error($result['data'], $result['message'], $result['status']);
    }
}
