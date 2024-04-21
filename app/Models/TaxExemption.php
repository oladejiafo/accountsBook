<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxExemption extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'valid_from', 'valid_to', 'company_id'];

    public function taxCodes()
    {
        return $this->belongsToMany(TaxCode::class, 'tax_code_tax_exemption', 'tax_exemption_id', 'tax_code_id');
    }
}
