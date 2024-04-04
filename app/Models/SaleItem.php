<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'billno',
        'stock_id',
        'quantity',
        'perprice',
        'totalprice',
        'company_id',
    ];

    public function bill()
    {
        return $this->belongsTo(SaleBill::class, 'billno', 'id');
    }
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
    // public function saleBill()
    // {
    //     return $this->belongsTo(SaleBill::class, 'billno', 'billno'); // Assuming 'billno' is the foreign key
    // }
}
