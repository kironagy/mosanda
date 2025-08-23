<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    protected $fillable = [
        'name', 'phone', 'bank_name', 'bank_iban', 'support_for',
        'total_need_amount', 'support_percentage', 'support_amount', 'amount'
    ];
    
    protected $casts = [
        'total_need_amount' => 'decimal:2',
        'support_percentage' => 'decimal:2',
        'support_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];
}
