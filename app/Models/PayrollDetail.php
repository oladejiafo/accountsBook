<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    use HasFactory;


    protected $fillable = [
        'company_id',
        'payroll_id',
        'component',
        'amount',
        'type', // 'earning' or 'deduction'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
