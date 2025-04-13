<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\FeatureTransactionFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'feature_id',
        'quantity',
        'price_xp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}
