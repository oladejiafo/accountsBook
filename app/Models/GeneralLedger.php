<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedger extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'account_id', 'balance'];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

}
