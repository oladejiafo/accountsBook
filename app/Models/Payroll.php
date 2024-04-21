<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'employee_id', 'basic_salary', 'allowances', 'deductions', 'total_pay', 'payment_date', 'payment_method', 'period'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
