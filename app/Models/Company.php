<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // Add attributes here that you want to be mass assignable
        'name',
        'email',
        'address',
        'website',
        'phone',
        'currency',
        'business_type',
        'subscription_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        // Add attributes here that you want to hide when the model is serialized
        // Example: 'password', 'api_token', etc.
    ];
}
