<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureType extends Model
{
    /** @use HasFactory<\Database\Factories\FeatureTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function features()
    {
        return $this->hasMany(Feature::class);
    }
}
