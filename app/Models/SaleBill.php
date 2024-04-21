<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleBill extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'name',
        'phone',
        'address',
        'email',
        'gstin',
        'company_id',
        'customer_id',
        'paid_at',
        'payment_status',
    ];

    public function saleBillDetails()
    {
        return $this->hasOne(SaleBillDetails::class);
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class, 'billno', 'id');
    }

    public function details()
    {
        return $this->hasOne(SaleBillDetails::class, 'billno', 'id');
    }

    public function getTotalPriceAttribute()
    {
        return $this->items->sum('totalprice');
    }
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'billno', 'id');
    }

    // Define the payments relationship
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }
}
