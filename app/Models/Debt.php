<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{

    protected $fillable = ['customer_id', 'credit', 'debit', 'debt_date', 'total_balance', 'details'];


    protected $casts = [
        'customer_id' => 'integer',
        'credit' => 'integer',
        'debit' => 'integer',
        'debt_date' => 'date',
        'total_balance' => 'integer',
        'details' => 'string',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
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

        return $query;
    }

}
