<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'company_id', 'city', 'country', 
        'billing_address', 'shipping_address', 'customer_type', 'notes', 
        'payment_terms', 'tax_exempt'
    ];

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
