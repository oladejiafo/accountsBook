<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Purchase extends Model implements Searchable
{
    use HasFactory;

    public function getSearchResult(): SearchResult
    {
        $title = $this->name;
        $url = route('stock_id.show', $this->id);
        return new SearchResult($this, $title, $url);
    }
}
