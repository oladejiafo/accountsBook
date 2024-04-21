<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'department_id',
        'designation_id',
        'name',
        'surname',
        'email',
        'personal_email',
        'phone_number',
        'branch_id',
        'citizenship',
        'passport_number',
        'date_of_birth',
        'gender',
        'marital_status',
        'date_employed',
        'status',
        'date_of_resignation',
        'resignation_reason',
        'bank_name',
        'bank_title',
        'bank_account_number',
        'iban',
        'swift_code',
        'bank_country',
        'created_by',
        'company_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
