<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxForm extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'filing_deadline', 'company_id'];

    public function taxTransactions()
    {
        return $this->hasMany(TaxTransaction::class);
    }    
}
