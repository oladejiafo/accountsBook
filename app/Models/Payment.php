<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'amount', 'payment_date', 'payment_method', 'description', 'invoice_id', 'recipient_type'
    ];

    public function recipient()
    {
        return $this->morphTo();
    }
}
