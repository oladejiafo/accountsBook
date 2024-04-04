<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleBillDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'billno',
        'eway',
        'veh',
        'destination',
        'po',
        'cgst',
        'sgst',
        'igst',
        'cess',
        'tcs',
        'total',
        'company_id',
    ];

    public function bill()
    {
        return $this->belongsTo(SaleBill::class, 'billno', 'billno');
    }
 
}
