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