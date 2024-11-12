<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Enums\StatusCode;
use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slot\BetWebhookRequest;
use App\Http\Requests\Slot\CancelBetNResultRequest;
use App\Http\Requests\Slot\CancelBetRequest;
use App\Models\Admin\GameList;
use App\Models\User;
use App\Models\Webhook\Bet;
use App\Models\Webhook\Result;
use App\Services\PlaceBetWebhookService;
use App\Traits\UseWebhook;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelBetController extends Controller
{
    use UseWebhook;

    // public function handleCancelBet(CancelBetRequest $request): JsonResponse
    // {
    //     $transactions = $request->getTransactions();

    //     DB::beginTransaction();
    //     try {
    //         Log::info('Starting handleCancelBet method for multiple transactions');

    //         foreach ($transactions as $transaction) {
    //             // Get the player
    //             $player = User::where('user_name', $transaction['PlayerId'])->first();
    //             if (! $player) {
    //                 Log::warning('Invalid player detected', [
    //                     'PlayerId' => $transaction['PlayerId'],
    //                 ]);

    //                 return $this->buildErrorResponse(StatusCode::InvalidPlayerPassword);
    //             }

    //             // Validate transaction signature
    //             $signature = $this->generateSignature($transaction);
    //             Log::info('CancelBet Signature', ['GeneratedCancelBetSignature' => $signature]);
    //             if ($signature !== $transaction['Signature']) {
    //                 Log::warning('Signature validation failed', [
    //                     'transaction' => $transaction,
    //                     'generated_signature' => $signature,
    //                 ]);

    //                 return $this->buildErrorResponse(StatusCode::InvalidSignature);
    //             }

    //             // Check for duplicate cancellation request
    //             $existingTransaction = Bet::where('round_id', $transaction['RoundId'])->first();

    //             // Check if the transaction has an associated result, which would prevent cancellation
    //             $associatedResult = Result::where('round_id', $transaction['RoundId'])->first();
    //             if ($associatedResult) {
    //                 Log::info('Cancellation not allowed - bet result already processed', ['RoundId' => $transaction['RoundId']]);

    //                 return $this->buildErrorResponse(StatusCode::InternalServerError); // 900500 error if result exists
    //             }

    //             if ($existingTransaction && $existingTransaction->status === 'cancelled') {
    //                 Log::warning('Duplicate cancellation request detected', ['RoundId' => $transaction['RoundId']]);

    //                 return $this->buildErrorResponse(StatusCode::DuplicateTransaction);
    //             }

    //             // Process the cancellation
    //             if (! $existingTransaction || $existingTransaction->status !== 'cancelled') {
    //                 Log::info('Cancelling Bet Transaction', ['TranId' => $transaction['RoundId']]);

    //                 // Update the existing transaction status to cancelled
    //                 if ($existingTransaction) {
    //                     $existingTransaction->status = 'cancelled';
    //                     $existingTransaction->cancelled_at = now();
    //                     $existingTransaction->save();
    //                 } else {
    //                     $request->getMember()->wallet->refreshBalance();

    //                     $newBalance = $request->getMember()->balanceFloat;

    //                     // If no transaction record exists, create a new one marked as cancelled
    //                     $game_code = GameList::where('game_code', $transaction['GameCode'])->first();
    //                     $game_name = $game_code->game_name;
    //                     $provider_name = $game_code->game_provide_name;
    //                     // Create the transaction record
    //                     Bet::create([
    //                         'user_id' => $player->id,
    //                         'game_provide_name' => $provider_name,
    //                         'game_name' => $game_name,
    //                         'operator_id' => $transaction['OperatorId'],
    //                         'request_date_time' => $transaction['RequestDateTime'],
    //                         'signature' => $transaction['Signature'],
    //                         'player_id' => $transaction['PlayerId'],
    //                         'currency' => $transaction['Currency'],
    //                         'round_id' => $transaction['RoundId'],
    //                         'bet_id' => $transaction['BetId'],
    //                         'game_code' => $transaction['GameCode'],
    //                         'bet_amount' => $transaction['BetAmount'],
    //                         'tran_date_time' => Carbon::parse($transaction['TranDateTime'])->format('Y-m-d H:i:s'),
    //                         'status' => 'cancelled',
    //                         'cancelled_at' => now(),
    //                     ]);

    //                     Log::info('Bet Transaction  processed successfully', ['BetID' => $transaction['BetId']]);
    //                 }

    //                 DB::commit();

    //                 return $this->buildSuccessResponse($newBalance);

    //             }
    //         }
    //          // Add a return response here if the loop completes without hitting a return statement inside
    //          $request->getMember()->wallet->refreshBalance();

    //         $Balance = $request->getMember()->balanceFloat;
    //     return $this->buildSuccessResponse($Balance);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Failed to handle CancelBet', [
    //             'error' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile(),
    //         ]);

    //         return response()->json(['message' => 'Failed to handle CancelBet'], 500);
    //     }
    // }

    public function handleCancelBet(CancelBetRequest $request): JsonResponse
    {
        $transactions = $request->getTransactions();

        DB::beginTransaction();
        try {
            Log::info('Starting handleCancelBet method for multiple transactions');

            foreach ($transactions as $transaction) {
                // Get the player
                $player = User::where('user_name', $transaction['PlayerId'])->first();
                if (! $player) {
                    Log::warning('Invalid player detected', [
                        'PlayerId' => $transaction['PlayerId'],
                    ]);

                    return PlaceBetWebhookService::buildResponse(
                        StatusCode::InvalidPlayerPassword,
                        0,
                        0
                    );
                }

                // Validate transaction signature
                $signature = $this->generateSignature($transaction);
                Log::info('CancelBet Signature', ['GeneratedCancelBetSignature' => $signature]);
                if ($signature !== $transaction['Signature']) {
                    Log::warning('Signature validation failed', [
                        'transaction' => $transaction,
                        'generated_signature' => $signature,
                    ]);

                    return $this->buildErrorResponse(StatusCode::InvalidSignature);
                }

                // Check if the transaction already exists
                $existingTransaction = Bet::where('bet_id', $transaction['BetId'])->first();

                // Check if there's an associated result, which prevents cancellation
                $associatedResult = Result::where('round_id', $transaction['RoundId'])->first();
                if ($associatedResult) {
                    Log::info('Cancellation not allowed - bet result already processed', ['RoundId' => $transaction['RoundId']]);

                    return $this->buildErrorResponse(StatusCode::NotEligibleCancel); // 900300 error if result exists
                }

                // // Check if the transaction has already been canceled
                // if ($existingTransaction && $existingTransaction->status === 'cancelled') {
                //     Log::warning('Duplicate cancellation request detected', ['RoundId' => $transaction['RoundId']]);

                //     return $this->buildErrorResponse(StatusCode::DuplicateTransaction);
                // }

                // Process the cancellation
                if (! $existingTransaction || $existingTransaction->status !== 'cancelled') {
                    Log::info('Cancelling Bet Transaction', ['TranId' => $transaction['RoundId']]);

                    // Update the existing transaction status to canceled
                    if ($existingTransaction) {
                        $existingTransaction->status = 'cancelled';
                        $existingTransaction->cancelled_at = now();
                        $existingTransaction->save();
                    } else {
                        // Check if the transaction has already been canceled
                        Log::warning('Duplicate cancellation request detected', ['RoundId' => $transaction['RoundId']]);

                        return $this->buildErrorResponse(StatusCode::DuplicateTransaction);
                    }

                    $PlayerBalance = $request->getMember()->balanceFloat;

                    // Check for sufficient balance
                    if ($transaction['BetAmount'] > $PlayerBalance) {
                        Log::warning('Insufficient balance detected', [
                            'BetAmount' => $transaction['BetAmount'],
                            'balance' => $PlayerBalance,
                        ]);

                        return $this->buildErrorResponse(StatusCode::InsufficientBalance, $PlayerBalance);
                    }

                    // Process the bet refund
                    $this->processTransfer(
                        User::adminUser(), // Assuming admin user as the receiving party
                        $player,
                        TransactionName::Refund,
                        $transaction['BetAmount']
                    );

                    $request->getMember()->wallet->refreshBalance();
                    $NewBalance = $request->getMember()->balanceFloat;

                    $game_code = GameList::where('game_code', $transaction['GameCode'])->first();
                    $game_name = $game_code->game_name;
                    $provider_name = $game_code->game_provide_name;

                    // Create the transaction record
                    Bet::create([
                        'user_id' => $player->id,
                        'game_provide_name' => $provider_name,
                        'game_name' => $game_name,
                        'operator_id' => $transaction['OperatorId'],
                        'request_date_time' => $transaction['RequestDateTime'],
                        'signature' => $transaction['Signature'],
                        'player_id' => $transaction['PlayerId'],
                        'currency' => $transaction['Currency'],
                        'round_id' => $transaction['RoundId'],
                        'bet_id' => $transaction['BetId'],
                        'game_code' => $transaction['GameCode'],
                        'bet_amount' => $transaction['BetAmount'],
                        'tran_date_time' => Carbon::parse($transaction['TranDateTime'])->format('Y-m-d H:i:s'),
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                    ]);

                    Log::info('Bet Transaction processed successfully', ['BetID' => $transaction['BetId']]);
                }
            }

            DB::commit();
            Log::info('All Bet transactions committed successfully');
                    $Balance = $request->getMember()->balanceFloat;

            // Build a successful response with the final balance of the last player
            return $this->buildSuccessResponse($Balance);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle BetNResult', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['message' => 'Failed to handle BetNResult'], 500);
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
        $method = 'CancelBet';
        $roundId = $transaction['RoundId'];
        $betId = $transaction['BetId'];
        $requestTime = $transaction['RequestDateTime'];
        $operatorCode = $transaction['OperatorId'];
        $secretKey = config('game.api.secret_key');
        $playerId = $transaction['PlayerId'];

        return md5($method.$roundId.$betId.$requestTime.$operatorCode.$secretKey.$playerId);
    }
}

/* The above PHP code snippet is a method named `handleCancelBet` that handles the cancellation of
multiple bet transactions based on a `BetWebhookRequest`. Here is a breakdown of what the code is
doing: */