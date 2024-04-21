<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCode extends Model
{
    use HasFactory;
    protected $fillable = ['rate', 'effective_date', 'tax_code_id', 'company_id'];

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class);
    }
}
