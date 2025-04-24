<?php

namespace App\Models;

use App\Models\ReceiptItem;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{


    protected $fillable = ['customer_name', 'total_price', 'receipt_number', 'receipt_date', 'phone'];
    protected $casts = [
        'customer_name' => 'string',
        'phone' => 'integer',
        'total_price' => 'integer',
        'receipt_number' => 'integer',
        'receipt_date' => 'date',

    ];

    public function receiptitems()
    {
        return $this->hasMany(ReceiptItem::class, 'receipt_id');
    }
    public function scopeFilterBy($query, array $filteringData)
    {
        if (isset($filteringData['name'])) {
            $query->where('customer_name', 'LIKE', "%{$filteringData['customer_name']}%");
        }

        if (isset($filteringData['phone'])) {
            $query->where('phone', '=', $filteringData['phone']);
        }
        if (isset($filteringData['receipt_number'])) {
            $query->where('receipt_number', '=', $filteringData['receipt_number']);
        }
        if (isset($filteringData['receipt_date'])) {
            $query->where('receipt_date', '=', $filteringData['receipt_date']);
        }

        return $query;
    }
}
