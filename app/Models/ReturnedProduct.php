<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_transaction_id',
        'product_id',
        'name',
        'quantity',
        'condition',
    ];

    public function returnTransaction()
    {
        return $this->belongsTo(ReturnTransaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
