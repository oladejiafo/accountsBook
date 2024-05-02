<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'guard_name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id');
    }
    // public function users()
    // {
    //     return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id','company_id')
    //                 ->wherePivot('company_id', auth()->user()->company_id); 
    // }
}
