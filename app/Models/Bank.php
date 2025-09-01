<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'country_id'];

    protected $casts = [
        'name' => 'json',
    ];

    /**
     * Get the country that owns the bank.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
