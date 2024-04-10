<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountsCategory extends Model
{
    use HasFactory;
    protected $table = 'accounts_category';

    protected $fillable = [
        'category',
    ];

}
