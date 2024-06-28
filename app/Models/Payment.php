<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Payment extends Model implements Searchable
{
    use HasFactory;


    protected $fillable = [
        'company_id',
        'customer_id',
        'sales_id',
        'stock_id',
        'invoice_id',
        'invoice_number',
        'bank_id',
        'bank_reference_number',
        'payment_type',
        'payable_amount',
        'paid_amount',
        'remaining_amount',
        'payment_date',
        'payment_status',
        'payment_verified_by_cfo',
        'payment_method',
        'description',
        'recipient_type',
        'remark',
    ];

    public function getSearchResult(): SearchResult
    {
        $title = $this->invoice_number;
        $url = route('payments.edit', $this->id);
        return new SearchResult($this, $title, $url);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function verifier()
    {
        return $this->belongsTo(Employee::class, 'payment_verified_by_cfo');
    }

    public function saleBill()
    {
        return $this->belongsTo(SaleBill::class, 'sales_id');
    }    
}
