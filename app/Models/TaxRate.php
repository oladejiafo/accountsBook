<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = ['name','rate', 'effective_date', 'position', 'min_earnings', 'max_earnings', 'company_id'];

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class);
    }    
}
