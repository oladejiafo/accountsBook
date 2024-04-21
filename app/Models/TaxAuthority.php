<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxAuthority extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'contact_info', 'jurisdiction', 'company_id'];

    // Define relationships
    public function taxPayments()
    {
        return $this->hasMany(TaxPayment::class);
    }    
}
