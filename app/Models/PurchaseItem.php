<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'billno',
        'stock_id',
        'quantity',
        'perprice',
        'totalprice',
    ];
    
    public function bill()
    {
        return $this->belongsTo(PurchaseBill::class, 'billno', 'id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
}
