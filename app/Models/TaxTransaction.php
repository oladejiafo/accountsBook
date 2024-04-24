<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'transaction_type', 'tax_code_id', 'tax_category_id', 'company_id'];

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class);
    }

    public function taxCategory()
    {
        return $this->belongsTo(TaxCategory::class);
    }

    public function taxForm()
    {
        return $this->belongsTo(TaxForm::class);
    }

    public function taxReturn()
    {
        return $this->belongsTo(TaxReturn::class);
    }

    public function taxDeduction()
    {
        return $this->belongsTo(TaxDeduction::class);
    }

    public function taxPenalty()
    {
        return $this->belongsTo(TaxPenalty::class);
    }

    public function taxPayment()
    {
        return $this->hasOne(TaxPayment::class);
    }    
    // public function transactionType()
    // {
    //     return $this->belongsTo(TransactionType::class);
    // }

}
