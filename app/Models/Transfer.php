<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Transfer extends Model implements Searchable
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'from_account_id', 'to_account_id', 'amount', 'description', 'date'
    ];

    public function getSearchResult(): SearchResult
    {
        $title = $this->description;
        $url = route('transfers.edit', $this->id);
        return new SearchResult($this, $title, $url);
    }

    public function fromAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_account_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_account_id');
    }

}
