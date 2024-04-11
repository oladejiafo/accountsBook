<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'start_date',
        'end_date',

        'total_deposits',
        'total_withdrawals',
        'ending_balance',
        'notes',
        'attachments',
        'method',
        // Add other fillable fields as needed
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
