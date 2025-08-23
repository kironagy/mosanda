<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faqs extends Model
{
    use HasTranslations;
    
    protected $fillable = ['question', 'answer', 'status'];
    
    public $translatable = ['question', 'answer'];
    
    protected $casts = [
        'status' => 'boolean',
    ];
}
