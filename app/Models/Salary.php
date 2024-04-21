<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'basic_amount', 'allowances', 'deductions', 'bonuses', 'days_absent', 'absentee_deduction', 'gross', 'net', 'salary_type', 'month', 'year', 'currency', 'created_by'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(SalaryDetail::class);
    }
}
