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
}
