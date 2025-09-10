<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Percentage extends Model
{
    protected $fillable = ['number', 'status'];
    
    protected $casts = [
        'number' => 'double',
        'status' => 'boolean',
    ];
}
