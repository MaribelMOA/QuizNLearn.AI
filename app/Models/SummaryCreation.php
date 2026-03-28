<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryCreation extends Model
{
    protected $table = 'summary_creations';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'created_at',
    ];
}
