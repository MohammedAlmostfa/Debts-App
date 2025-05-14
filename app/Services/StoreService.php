<?php

namespace App\Services;

use App\Models\Store;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Handles store CRUD (Create, Read, Update, Delete) operations.
 * Provides methods for managing store data and interacting with the database.
 */
class StoreService
{
    /**
     * Retrieve all stores.
     *
     * Fetches all stores from the database.
     *
     * @return array Response containing status, message, and list of stores.
     */
    public function getAllStores($filteringData)
    {
        try {
            $stores = Store::query()
                ->when(!empty($filteringData), function ($query) use ($filteringData) {
                    $query->filterBy($filteringData);
                })
                ->get();

            return $this->successResponse($stores, 'تم استرجاع المتاجر بنجاح', 200);
        } catch (Exception $e) {
            Log::error('خطأ أثناء استرجاع المتاجر: ' . $e->getMessage());

            return $this->errorResponse('فشل في استرجاع المتاجر');
        }
    }

    /**
     * Retrieve store debts.
     *
     * Fetches the store and its associated debts using eager loading.
     *
     * @param int $id ID of the store.
     * @return array Response containing status, message, and store debts.
     */
    public function getStoreDebts($id)
    {
        try {

            $store = Store::with(['debts'])->findOrFail($id);

            return $this->successResponse($store, 'تم استرجاع ديون المتجر بنجاح', 200);
        } catch (Exception $e) {
            Log::error('خطأ أثناء استرجاع ديون المتجر: ' . $e->getMessage());

            return $this->errorResponse('فشل في استرجاع ديون المتجر');
        }
    }

    /**
     * Create a new store.
     *
     * @param array $data Array containing store details ['name', 'phone', 'notes'].
     * @return array Response containing status, message, and created store data.
     */
    public function createStore(array $data): array
    {
        try {
            $store = Store::create($data);

            return $this->successResponse($store, 'تم إنشاء المتجر بنجاح', 200);
        } catch (Exception $e) {
            Log::error('خطأ أثناء إنشاء المتجر: ' . $e->getMessage());

            return $this->errorResponse('فشل في إنشاء المتجر');
        }
    }

    /**
     * Update an existing store.
     *
     * Updates the details of an existing store.
     *
     * @param array $data Array containing updated store details ['name', 'phone', 'notes'].
     * @param Store $store The store to be updated.
     * @return array Response containing status, message, and updated store data.
     */
    public function updateStore(array $data, Store $store): array
    {
        try {
            $store->update($data);

            return $this->successResponse($store, 'تم تحديث المتجر بنجاح');
        } catch (Exception $e) {
            Log::error('خطأ أثناء تحديث المتجر: ' . $e->getMessage());

            return $this->errorResponse('فشل في تحديث المتجر');
        }
    }

    /**
     * Delete a store.
     *
     * Removes a store from the database.
     *
     * @param Store $store The store to be deleted.
     * @return array Response containing status and message.
     */
    public function deleteStore(Store $store): array
    {
        try {
            $store->delete();

            return $this->successResponse(null, 'تم حذف المتجر بنجاح');
        } catch (Exception $e) {
            Log::error('خطأ أثناء حذف المتجر: ' . $e->getMessage());

            return $this->errorResponse('فشل في حذف المتجر');
        }
    }

    /**
     * Helper method for success responses.
     *
     * @param mixed $data Data to be included in the response.
     * @param string $message Success message.
     * @param int $status HTTP status code (default 200).
     * @return array Response structure for successful operations.
     */
    private function successResponse($data, string $message, int $status = 200): array
    {
        return [
            'message' => $message,
            'status' => $status,
            'data' => $data,
        ];
    }

    /**
     * Helper method for error responses.
     *
     * @param string $message Error message.
     * @param int $status HTTP status code (default 500).
     * @return array Response structure for failed operations.
     */
    private function errorResponse(string $message, int $status = 500): array
    {
        return [
            'message' => $message,
            'status' => $status,
            'data' => null,
        ];
    }
}
