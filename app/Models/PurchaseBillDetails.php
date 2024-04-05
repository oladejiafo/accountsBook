<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseBillDetails extends Model
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
    ];
    
    public function bill()
    {
        return $this->belongsTo(PurchaseBill::class, 'billno', 'id');
    }
}
