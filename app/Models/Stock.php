<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
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
}
