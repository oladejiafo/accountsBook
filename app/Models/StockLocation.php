<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLocation extends Model
{
    use HasFactory;

    protected $table = 'stock_locations';

    protected $fillable = ['name','company_id'];

        /**
     * Get the stocks associated with the category.
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'store_location');
    }
}
