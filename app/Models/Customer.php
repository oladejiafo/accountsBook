<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Customer extends Model implements Searchable
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'company_id', 'city', 'country', 
        'billing_address', 'shipping_address', 'customer_type', 'notes', 
        'payment_terms', 'tax_exempt','balance'
    ];

    public function getSearchResult(): SearchResult
    {
        $title = $this->name;
        $url = route('customers.show', $this->id);
        return new SearchResult($this, $title, $url);
    }
    
    /**
     * Get the company that owns the customer.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the sales associated with the customer.
     */
    public function sales()
    {
        return $this->hasMany(SaleBill::class);
    }

    /**
     * Get the refunds associated with the customer.
     */
    public function refunds()
    {
        return $this->hasMany(RefundTransaction::class);
    }
}
