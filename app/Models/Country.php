<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'json',
    ];

    /**
     * Get the banks for the country.
     */
    public function banks()
    {
        return $this->hasMany(Bank::class);
    }
}
