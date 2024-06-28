<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Employee extends Model implements Searchable
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'active_status',
        'staff_number',
        'sur_name',
        'first_name',
        'middle_name',
        'gender',
        'designation_id',
        'last_promotion',
        'level',
        'step',
        'cadre',
        'date_of_birth',
        'height',
        'weight',
        'email',
        'phone',
        'date_employed',
        'exit_date',
        'exit_reason',
        'bank_id',
        'account_number',
        'blood_group',
        'genotype',
        'in_staff_qtrs',
        'region',
        'branch_id',
        'state_of_origin_id',
        'LGA_of_origin_id',
        'department_id',
        'home_address',
        'contact_address',
        'personal_phone',
        'personal_email',
        'office_location',
        'employment_type',
        'marital_status',
        'birth_place',
        'spouse_name',
        'marriage_date',
        'nationality_id',
        'qualifications_id',
        'profession_id',
        'confirmation_status',
        'position_id',
        'date_confirmed',
        'pension_managers_id',
        'pension_amount',
        'deformity',
        'salary',
        'days_worked',
        'tax_id',
        'pension_pin',
        'passport_number',
        'residency_status',
        'visa_type',
        'visa_expiry'
    ];

    public function getSearchResult(): SearchResult
    {
        $title = $this->staff_number;
        $url = route('employees.show', $this->id);
        return new SearchResult($this, $title, $url);
    }

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

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function stateOfOrigin()
    {
        return $this->belongsTo(State::class, 'state_of_origin_id');
    }

    public function LGAOfOrigin()
    {
        return $this->belongsTo(LGA::class, 'LGA_of_origin_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function qualifications()
    {
        return $this->belongsTo(Qualification::class, 'qualifications_id');
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function pensionManager()
    {
        return $this->belongsTo(PensionManager::class, 'pension_managers_id');
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
