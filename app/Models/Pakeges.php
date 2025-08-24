<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Pakeges extends Model
{
    use HasTranslations;
    
    protected $guarded = [];
    
    public $translatable = ['title', 'description'];
    
    protected $casts = [
        'amount_from' => 'decimal:2',
        'amount_to' => 'decimal:2',
        'support_percentage' => 'decimal:2',
        'price' => 'decimal:2',
    ];
}
