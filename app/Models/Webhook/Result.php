<?php

namespace App\Models\Webhook;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'player_name',
        'game_provide_name',
        'game_name',
        'operator_id',
        'game_provide_name',
        'game_name',
        'request_date_time',
        'signature',
        'player_id',
        'currency',
        'round_id',
        'bet_ids',
        'result_id',
        'game_code',
        'total_bet_amount',
        'win_amount',
        'net_win',
        'tran_date_time',
    ];

    protected $casts = [
        'bet_ids' => 'array', // Cast to array for JSON
    ];

    /**
     * Get the user associated with the result.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
