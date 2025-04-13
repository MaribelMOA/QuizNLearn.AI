<?php

namespace App\Models;

use Database\Seeders\XpTransactionSeeder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'customer_id',
        'payment_method_id',
        'xp',
        'current_plan_id',
    ];
    //'current_plan_id',

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



//    public function subscriptions()
//    {
//        return $this->hasMany(UserPlan::class);
//    }

    public function userPlan()
    {
        return $this->hasOne(UserPlan::class);
    }

    /**
     * Get the current plan associated with the user.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

// app/Models/User.php

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function summaries()
    {
        return $this->hasMany(Summary::class);
    }

    public function gameHistory()
    {
        return $this->hasMany(GameHistory::class);
    }

    public function featureTransactions()
    {
        return $this->hasMany(FeatureTransaction::class);
    }

    public function xpTransactions()
    {
        return $this->hasOne(XpTransaction::class);
    }

}
