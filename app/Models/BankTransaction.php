<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'bank_account_id',
        'bank_transaction_id',
        'bank_name',
        'type',
        'date',
        'amount',
        'description',
        // Add other fillable fields related to bank transactions
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
