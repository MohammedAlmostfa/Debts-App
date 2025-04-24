<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptItem extends Model
{
    protected $fillable = [
        'receipt_id',
        'description',
        'quantity',
        'unit_price'
    ];
    protected $casts = [
       'receipt_id' => 'string',
       'description' => 'string',
       'quantity' => 'string',
       'unit_price' => 'string',

      ];

    // Relationship: Each item belongs to a receipt
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }


    /**
     * Get the total price for the item.
     *
     * @return float Total price for the item
     */
    public function getTotalPrice(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
