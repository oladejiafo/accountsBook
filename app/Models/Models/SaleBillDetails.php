<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleBillDetails extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
