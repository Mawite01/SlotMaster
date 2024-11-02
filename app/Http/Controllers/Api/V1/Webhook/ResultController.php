<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Enums\StatusCode;
use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slot\ResultWebhookRequest;
use App\Models\Admin\GameList;
use App\Models\User;
use App\Models\Webhook\Result;
use App\Services\PlaceBetWebhookService;
use App\Traits\UseWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResultController extends Controller
{
    use UseWebhook;

    public function handleResult(ResultWebhookRequest $request): JsonResponse
    {
        $transactions = $request->getTransactions();

        DB::beginTransaction();
        try {
            Log::info('Starting handleResult method for multiple transactions');

            $player = User::where('user_name', $transactions['PlayerId'])->first();
            if (! $player) {
                Log::warning('Invalid player detected', [
                    'PlayerId' => $transactions['PlayerId'],
                ]);

                return PlaceBetWebhookService::buildResponse(StatusCode::InvalidPlayerPassword, 0, 0);
            }

            // Validate signature
            $signature = $this->generateSignature($transactions);
            if ($signature !== $transactions['Signature']) {
                Log::warning('Signature validation failed');

                return $this->buildErrorResponse(StatusCode::InvalidSignature);
            }

            // Process refund if WinAmount > 0
            if ($transactions['WinAmount'] > 0) {
                $this->processTransfer(
                    User::adminUser(),
                    $player,
                    TransactionName::Payout,
                    $transactions['WinAmount']
                );
            }

            //$newBalance = $player->wallet->refreshBalance()->balanceFloat;
            $request->getMember()->wallet->refreshBalance();

            $newBalance = $request->getMember()->balanceFloat;
            $game_code = GameList::where('game_code', $transactions['GameCode'])->first();
            $game_name = $game_code->game_name;
            $provider_name = $game_code->game_provide_name;

            // Create result record
            Result::create([
                'user_id' => $player->id,
                'player_name' => $player->name,
                'game_provide_name' => $provider_name,
                'game_name' => $game_name,
                'operator_id' => $transactions['OperatorId'],
                'request_date_time' => $transactions['RequestDateTime'],
                'signature' => $transactions['Signature'],
                'player_id' => $transactions['PlayerId'],
                'currency' => $transactions['Currency'],
                'round_id' => $transactions['RoundId'],
                'bet_ids' => $transactions['BetIds'],
                'result_id' => $transactions['ResultId'],
                'game_code' => $transactions['GameCode'],
                'total_bet_amount' => $transactions['TotalBetAmount'],
                'win_amount' => $transactions['WinAmount'],
                'net_win' => $transactions['NetWin'],
                'tran_date_time' => $transactions['TranDateTime'],
            ]);

            Log::info('Result transaction processed successfully', ['ResultId' => $transactions['ResultId']]);
            DB::commit();

            return $this->buildSuccessResponse($newBalance);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle Result', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to handle Result'], 500);
        }
    }

    private function buildSuccessResponse(float $newBalance): JsonResponse
    {
        return response()->json([
            'Status' => StatusCode::OK->value,
            'Description' => 'Success',
            'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
            'Balance' => round($newBalance, 4),
        ]);
    }

    private function buildErrorResponse(StatusCode $statusCode, float $balance = 0): JsonResponse
    {
        return response()->json([
            'Status' => $statusCode->value,
            'Description' => $statusCode->name,
            'Balance' => round($balance, 4),
        ]);
    }

    private function generateSignature(array $transaction): string
    {
        $method = 'Result';

        return md5($method.$transaction['RoundId'].$transaction['ResultId'].$transaction['RequestDateTime'].
                    $transaction['OperatorId'].config('game.api.secret_key').$transaction['PlayerId']);
    }
}
