<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = ['name','company_id']; // Add more fillable fields as needed

    // Define any relationships here
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
