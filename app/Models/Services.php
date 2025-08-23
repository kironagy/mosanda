<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Services extends Model
{
    use HasTranslations;
    
    protected $fillable = ['title', 'description', 'image', 'button_text', 'button_link'];
    
    public $translatable = ['title', 'description'];
}
