<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class ChartOfAccount extends Model implements Searchable
{
    use HasFactory;

    protected $fillable = ['company_id', 'category', 'type', 'code', 'parent_account_id','description','name'];

    public function getSearchResult(): SearchResult
    {
        $title = $this->code;
        $url = route('chartOfAccounts.edit', $this->id);
        return new SearchResult($this, $title, $url);
    }

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
