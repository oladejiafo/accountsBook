<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Deposit extends Model implements Searchable
{
    use HasFactory;


    protected $fillable = ['company_id', 'type', 'date', 'amount', 'description', 'account_id'];

    public function getSearchResult(): SearchResult
    {
        $title = $this->type;
        $url = route('deposits.edit', $this->id);
        return new SearchResult($this, $title, $url);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function account_type()
    {
        return $this->belongsTo(AccountsCategory::class, 'type');
    }
}
