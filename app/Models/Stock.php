<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Stock extends Model implements Searchable
{
    use HasFactory;

    protected $fillable = ['name', 'quantity', 'reorder_point', 'description', 'store_location', 'category_id'];
    /**
     * Get the category associated with the stock.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the store location associated with the stock.
     */
    public function storeLocation()
    {
        return $this->belongsTo(StockLocation::class, 'store_location');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'stock_id');
    }

    public function getSearchResult(): SearchResult
    {
        $url = route('edit-stock', $this->id);
        return new SearchResult($this, $this->name, $url);
    }
}
