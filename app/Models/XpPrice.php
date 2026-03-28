<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XpPrice extends Model
{
    /** @use HasFactory<\Database\Factories\XpPriceFactory> */
    use HasFactory;
    protected $fillable = ['xp_amount', 'price'];
}
