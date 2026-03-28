<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XpTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\XpTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'xp_price_id',
    ];

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el modelo XpPrice.
     */
    public function xpPrice()
    {
        return $this->belongsTo(XpPrice::class);
    }
}
