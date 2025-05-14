<?php

namespace App\Models;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{

    protected $fillable = ['store_id', 'credit', 'debit', 'debt_date', 'total_balance', 'receipt_id'];

    protected $table = 'debts2';
    protected $casts = [
        'store_id' => 'integer',
        'credit' => 'integer',
        'debit' => 'integer',
        'debt_date' => 'date',
        'total_balance' => 'integer',
        'receipt_id' => 'integer',
    ];
    public function stroe()
    {
        return $this->belongsTo(Store::class);
    }
    public function scopeFilterBy($query, array $filteringData)
    {
        // Filter where 'credit' is null
        if (isset($filteringData['debit'])) {
            $query->whereNull('credit');

        }

        // Filter where 'debit' is null
        if (isset($filteringData['credit'])) {
            $query->whereNull('debit');

        }

        // Filter by debt_date if provided
        if (isset($filteringData['debt_date'])) {
            $query->whereDate('debt_date', '=', $filteringData['debt_date']);
        }
        if (isset($filteringData['receipt_id'])) {
            $query->whereDate('receipt_id', '=', $filteringData['receipt_id']);
        }


        return $query;
    }

}
