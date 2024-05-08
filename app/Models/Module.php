<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // Add other fillable attributes as needed
    ];
    public function subModules()
    {
        return $this->hasMany(SubModule::class);
    }
}
