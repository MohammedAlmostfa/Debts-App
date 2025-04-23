<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = ['customer_id', 'amount', 'type', 'debt_date', 'total_balance'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
