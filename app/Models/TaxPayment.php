<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxPayment extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'date', 'tax_type', 'reference', 'company_id'];

    public function taxTransaction()
    {
        return $this->belongsTo(TaxTransaction::class);
    }    
}
