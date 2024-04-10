<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'account_id', 'amount', 'description', 'transaction_date'
    ];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
