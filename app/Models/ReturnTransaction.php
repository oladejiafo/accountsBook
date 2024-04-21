<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'condition',
        'reason',
        'status',
        'refund_amount',
        'payment_method',
        'transaction_id',
        'carrier',
        'tracking_number',
        'shipping_cost',
        'notes',
        'company_id',
        'customer_id', // Added field for customer ID
        'approval_required', // Added field for approval requirement
        'exchange', // Added field for refund/exchange option
    ];
    
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
        return $this->hasMany(ReturnedProduct::class);
    }
}
