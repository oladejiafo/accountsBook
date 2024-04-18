<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionAccountMapping extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'transaction_type',
        'account_id',
        'is_credit',
        'company_id',
    ];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
