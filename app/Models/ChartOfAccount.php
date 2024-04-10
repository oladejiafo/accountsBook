<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'category', 'type', 'code', 'name', 'parent_account_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function parentAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_account_id');
    }

    public function subAccounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_account_id');
    }

    public function types()
    {
        return $this->belongsTo(AccountsCategory::class, 'type');
    }
}
