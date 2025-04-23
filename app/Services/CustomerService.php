<?php

namespace App\Services;

use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Handles customer CRUD operations
 */
class CustomerService
{

    public function getAllCustomers()
    {
        try {
            // Fetch all customers with pagination (10 customers per page)
            $customers = Customer::paginate(10);

            // Return successful response
            return $this->successResponse($customers, 'Customers retrieved successfully', 200);
        } catch (Exception $e) {
            // Log any error that occurs
            Log::error('Get customers error: ' . $e->getMessage());

            // Return error response
            return $this->errorResponse('Failed to retrieve customers');
        }
    }
    public function getCustomerDebts($id)
    {
        try {
            // Fetch the customer with their debts using eager loading
            $customer = Customer::with('debts')->findOrFail($id);

            // Return successful response with the debts
            return $this->successResponse($customer->debts, 'Customer debts retrieved successfully', 200);
        } catch (Exception $e) {
            // Log any error that occurs
            Log::error('Error fetching customer debts: ' . $e->getMessage());

            // Return error response
            return $this->errorResponse('Failed to retrieve customer debts');
        }
    }



    /**
     * Create new customer
     * @param array $data ['name', 'phone', 'notes']
     * @return array ['message', 'status', 'data']
     */
    public function createCustomer(array $data): array
    {
        try {
            $customer = Customer::create($data);
            return $this->successResponse($customer, 'Customer created', 201);
        } catch (Exception $e) {
            Log::error('Create customer error: '.$e->getMessage());
            return $this->errorResponse('Failed to create customer');
        }
    }

    /**
     * Update existing customer
     * @param array $data ['name', 'phone', 'notes']
     * @param Customer $customer
     * @return array ['message', 'status', 'data']
     */
    public function updateCustomer(array $data, Customer $customer): array
    {
        try {
            $customer->update($data);
            return $this->successResponse($customer, 'Customer updated');
        } catch (Exception $e) {
            Log::error('Update customer error: '.$e->getMessage());
            return $this->errorResponse('Failed to update customer');
        }
    }

    /**
     * Delete customer
     * @param Customer $customer
     * @return array ['message', 'status']
     */
    public function deleteCustomer(Customer $customer): array
    {
        try {
            $customer->delete();
            return $this->successResponse(null, 'Customer deleted');
        } catch (Exception $e) {
            Log::error('Delete customer error: '.$e->getMessage());
            return $this->errorResponse('Failed to delete customer');
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
