<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{

    protected $fillable = ['customer_id', 'credit', 'debit', 'debt_date', 'total_balance', 'details'];


    protected $casts = [
        'customer_id' => 'string',
        'credit' => 'string',
        'debit' => 'string',
        'debt_date' => 'date',
        'total_balance' => 'string',
        'details' => 'string',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
