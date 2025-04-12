<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class UserPlan extends Model
{
    use HasFactory;

   protected $table = 'user_plans';

    // Definir los campos que son asignables en masa
    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
    ];

    // Definir las relaciones
    public function user()
    {
        // Relación con el modelo User
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        // Relación con el modelo Plan
        return $this->belongsTo(Plan::class);
    }
}
