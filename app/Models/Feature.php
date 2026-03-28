<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    /** @use HasFactory<\Database\Factories\FeatureFactory> */
    use HasFactory;
    protected $fillable = [
        'feature_type_id',
        'name',
        'description',
        'xp_price',
    ];

    public function featureType()
    {
        return $this->belongsTo(FeatureType::class);
    }
}
