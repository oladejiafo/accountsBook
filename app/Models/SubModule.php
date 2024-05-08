<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubModule extends Model
{
    use HasFactory;
    protected $fillable = [
        'module_id',
        'name',
        // Add other fillable attributes as needed
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
