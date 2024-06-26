<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id', 
        'dept_id', 
        'office', 
        'description', 
        'serial_number', 
        'asset_code', 
        'purchase_price', 
        'current_price',
 
        'depreciation_method', 'useful_life', 'salvage_value', 
        'current_value', 'location', 'status'

    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
