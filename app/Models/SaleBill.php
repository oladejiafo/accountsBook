<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class SaleBill extends Model implements Searchable
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

    public function getSearchResult(): SearchResult
    {
        $title = $this->name;
        $url = route('sales.show', $this->id);
        return new SearchResult($this, $title, $url);
    }

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
