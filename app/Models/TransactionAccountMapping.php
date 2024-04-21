<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionAccountMapping extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_type',
        'debit_account_id',
        'credit_account_id',
        'is_credit',
        'company_id',
    ];

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id');
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type');
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
