<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class GeneralLedger extends Model implements Searchable
{
    use HasFactory;

    protected $fillable = ['company_id', 'account_id', 'balance'];

    public function getSearchResult(): SearchResult
    {
        $title = $this->type;
        $url = route('ledger.index', $this->id);
        return new SearchResult($this, $title, $url);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

}
