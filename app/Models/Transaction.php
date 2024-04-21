<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'type', 'date', 'amount', 'description', 'transaction_name', 'recipient_name', 'recipient_account_no', 'status', 'source', 'bank_id', 'from_account_id', 'to_account_id', 'account_id', 'approved_by', 'approved_at'];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function account_type()
    {
        return $this->belongsTo(AccountsCategory::class, 'type');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function fromAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_account_id');
    }
}
