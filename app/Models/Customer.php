<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'notes','record_id'];

    protected $casts = [

       'name' => 'string',
'phone' => 'integer',
       'notes' => 'string',
       'record_id' => 'integer',

      ];
    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
