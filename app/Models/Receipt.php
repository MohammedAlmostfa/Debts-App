<?php

namespace App\Models;

use App\Models\ReceiptItem;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{


    protected $fillable = ['customer_name','total_price','receipt_number','receipt_date'];
    protected $casts = [
          'customer_name' => 'string',
          'total_price' => 'integer',
          'receipt_number' => 'integer',
          'receipt_date' => 'date',

      ];

    public function receiptitems()
    {
        return $this->hasMany(ReceiptItem::class, 'receipt_id');

    }

}
