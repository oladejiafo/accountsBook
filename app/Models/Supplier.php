<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Supplier extends Model implements Searchable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'company_id',
        'gstin',
        'contact_person',
        'is_deleted',
    ];
    
    protected $hidden = [];

    public function getSearchResult(): SearchResult
    {
        $title = $this->name;
        $url = route('supplier', $this->id);
        return new SearchResult($this, $title, $url);
    }
    
    public function purchases()
    {
        return $this->hasMany(PurchaseBill::class, 'supplier_id');
    }
}
