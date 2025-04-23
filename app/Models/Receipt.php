<?php

namespace App\Models;

use App\Models\ReceiptItem;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{


    protected $fillable = ['customer_name','total_amount','receipt_number','receipt_date'];


    public function receiptitems()
    {
        return $this->hasMany(ReceiptItem::class, 'receipt_id');

    }

}
