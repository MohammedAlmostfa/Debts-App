<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransferCustomerDebtsToStores extends Seeder
{
    public function run()
    {
        $customers = DB::table('customers')->get();

        foreach ($customers as $customer) {
            DB::table('stores')->insert([
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'notes' => $customer->notes,
                'record_id' => $customer->record_id,
                'created_at' => $customer->created_at,
                'updated_at' => now(),
            ]);
        }

        echo "✅ تم نقل بيانات العملاء إلى جدول المتاجر بنجاح.";
    }
}
