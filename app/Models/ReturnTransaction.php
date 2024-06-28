<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class ReturnTransaction extends Model implements Searchable
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'condition',
        'reason',
        'return_status',
        'return_date',
        'reason_for_return',
        'refund_amount',
        'payment_method',
        'transaction_id',
        'carrier',
        'tracking_number',
        'shipping_cost',
        'notes',
        'company_id',
        'customer_id', 
        'approval_required', 
        'exchange', 
    ];
    
    // public function getSearchResult(): SearchResult
    // {
    //     $title = $this->product_id;
    //     $url = route('returns.edit', $this->id);
    //     return new SearchResult($this, $title, $url);
    // }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Define the company relationship
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Define relationship with returned products (assuming you have a returned_products table)
    public function returnedProducts()
    {
        // return $this->hasMany(ReturnedProduct::class);
        return $this->hasMany(ReturnedProduct::class, 'return_id');
    }
}
