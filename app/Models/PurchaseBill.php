<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseBill extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'supplier_id', 'company_id'
    ];
    
    // public function supplier()
    // {
    //     return $this->belongsTo(Supplier::class);
    // }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'billno', 'id');
    }

    public function details()
    {
        return $this->hasOne(PurchaseBillDetails::class, 'billno', 'id');
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'billno', 'id');
    }
}
