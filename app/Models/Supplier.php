<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
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

    public function purchases()
    {
        return $this->hasMany(PurchaseBill::class, 'supplier_id');
    }
}
