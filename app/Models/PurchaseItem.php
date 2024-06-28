<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class PurchaseItem extends Model implements Searchable
{
    use HasFactory;

    protected $fillable = [
        'billno',
        'stock_id',
        'quantity',
        'perprice',
        'totalprice',
    ];
    
    public function getSearchResult(): SearchResult
    {
        $title = $this->name;
        $url = route('stock_id.show', $this->id);
        return new SearchResult($this, $title, $url);
    }

    public function bill()
    {
        return $this->belongsTo(PurchaseBill::class, 'billno', 'id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
}
