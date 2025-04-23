<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'notes'];

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
