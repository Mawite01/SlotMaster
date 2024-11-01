<?php

namespace App\Models\Webhook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Bet extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'game_provide_name',
        'game_name',
        'operator_id',
        'request_date_time',
        'signature',
        'player_id',
        'currency',
        'round_id',
        'bet_id',
        'game_code',
        'bet_amount',
        'tran_date_time',
        'auth_token',
    ];

    /**
     * Get the user that owns the bet.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}