<?php

namespace App\Models\Webhook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use App\Models\User;

class BetNResult extends Model
{
    use HasFactory;
     protected $table = 'bet_n_results';

    protected $fillable = [
        'user_id',
        'operator_id',
        'request_date_time',
        'signature',
        'player_id',
        'currency',
        'tran_id',
        'game_code',
        'bet_amount',
        'win_amount',
        'net_win',
        'tran_date_time',
        'auth_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}