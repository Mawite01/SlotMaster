<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WithdrawResource;
use App\Models\WithDrawRequest;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithDrawRequestController extends Controller
{
    use HttpResponses;

    public function withdraw(Request $request)
    {
        try {
            $player = Auth::user();
            if ($request->amount > $player->balanceFloat) {
                return $this->error('', 'Insufficient Balance', 401);
            }
            $withdraw = WithDrawRequest::create([
                'user_id' => $player->id,
                'agent_id' => $player->agent_id,
                'amount' => $request->amount,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'payment_type_id' => $request->payment_type_id,
            ]);

            return $this->success($withdraw, 'Withdraw Request Success');

        } catch (Exception $e) {
            $this->error('', $e->getMessage(), 401);
        }
    }

    public function log()
    {
        $withdraw = WithDrawRequest::where('user_id', Auth::id())->get();

        return $this->success(WithdrawResource::collection($withdraw));
    }
}
