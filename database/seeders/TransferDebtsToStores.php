<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransferDebtsToStores extends Seeder
{
    public function run()
    {
        $debts = DB::table('debts')->get();

        foreach ($debts as $debt) {
            DB::table('debts2')->insert([
                'store_id' => $debt->customer_id,
                'credit' => (int)$debt->credit,
                'debit' => (int)$debt->debit,
                'debt_date' => $debt->debt_date,
                'total_balance' => (int)$debt->total_balance,
                'receipt_id' => $debt->receipt_id,
                'created_at' => $debt->created_at,
                'updated_at' => now(),
            ]);
        }

        echo "✅ تم نقل بيانات الديون من العملاء إلى المتاجر بنجاح.";
    }
}
