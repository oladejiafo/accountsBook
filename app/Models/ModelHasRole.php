<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    use HasFactory;

    protected $table = 'model_has_roles';
    protected $fillable = [
        'model_id',
        'model_type',
        'role_id',
        'company_id',
    ];
    
    // Define any relationships here, such as with the Role model
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
