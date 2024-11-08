<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slot\CancelBetNResultRequest;
use App\Models\User;
use App\Models\Webhook\BetNResult;
use App\Traits\UseWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelBetNResultController extends Controller
{
    use UseWebhook;

    public function handleCancelBetNResult(CancelBetNResultRequest $request): JsonResponse
    {
        $transactions = $request->getTransactions();

        DB::beginTransaction();
        try {
            Log::info('Starting handleCancelBetNResult method for multiple transactions');

            foreach ($transactions as $transaction) {
                // Check if the transaction has already been processed
                $existingTransaction = BetNResult::where('tran_id', $transaction['TranId'])->first();

                if ($existingTransaction && $existingTransaction->status === 'processed') {
                    Log::info('BetNResult already processed', ['TranId' => $transaction['TranId']]);
                    return $this->buildErrorResponse(StatusCode::NotEligible); // 900300 status for already processed
                }

                // If unprocessed, mark as processed and return 200 status without adjusting balance
                if (!$existingTransaction || $existingTransaction->status !== 'processed') {
                    Log::info('BetNResult unprocessed, setting status to processed', ['TranId' => $transaction['TranId']]);

                    // Mark the transaction as processed
                    if ($existingTransaction) {
                        $existingTransaction->status = 'processed';
                        $existingTransaction->save();
                    } else {
                        // If no transaction record exists, create a new record with status 'processed'
                        BetNResult::create([
                            'user_id' => User::where('user_name', $transaction['PlayerId'])->first()->id,
                            'operator_id' => $transaction['OperatorId'],
                            'request_date_time' => $transaction['RequestDateTime'],
                            'signature' => $transaction['Signature'],
                            'player_id' => $transaction['PlayerId'],
                            'currency' => $transaction['Currency'],
                            'tran_id' => $transaction['TranId'],
                            'game_code' => $transaction['GameCode'],
                            'bet_amount' => $transaction['BetAmount'],
                            'win_amount' => $transaction['WinAmount'],
                            'net_win' => $transaction['WinAmount'] - $transaction['BetAmount'],
                            'tran_date_time' => $transaction['TranDateTime'],
                            'status' => 'processed',
                        ]);
                    }

                    DB::commit();
                    return $this->buildSuccessResponse();
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle CancelBetNResult', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['message' => 'Failed to handle CancelBetNResult'], 500);
        }
    }

    private function buildSuccessResponse(): JsonResponse
    {
        return response()->json([
            'Status' => StatusCode::OK->value,
            'Description' => 'Success',
            'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    private function buildErrorResponse(StatusCode $statusCode): JsonResponse
    {
        return response()->json([
            'Status' => $statusCode->value,
            'Description' => $statusCode->name,
            'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}