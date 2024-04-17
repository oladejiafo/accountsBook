<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDetail extends Model
{
    use HasFactory;
    protected $fillable = ['salary_id', 'salary_item_id', 'amount', 'category'];

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function item()
    {
        return $this->belongsTo(SalaryItem::class, 'salary_item_id');
    }
}
