<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Enums\StatusCode;
use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slot\CancelBetNResultRequest;
use App\Models\User;
use App\Models\Webhook\BetNResult;
use App\Services\PlaceBetWebhookService;
use App\Traits\UseWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelBetNResultController extends Controller
{
    use UseWebhook;

    public function handleCancelBetNResult(CancelBetNResultRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            Log::info('Starting handleCancelBetNResult method');

            // Validate player
            $player = $request->getMember();
            if (!$player) {
                Log::warning('Invalid player detected', [
                    'PlayerId' => $request->getPlayerId(),
                ]);

                return PlaceBetWebhookService::buildResponse(StatusCode::InvalidPlayerPassword, 0, 0);
            }

            // Perform validation using the validator class
            $validator = $request->check();
            Log::info('Validator check passed');
            if (isset($validator['Status']) && $validator['Status'] !== StatusCode::OK->value) {
                Log::warning('Validation failed', ['validation_response' => $validator]);
                return response()->json($validator);
            }

            // Check for existing transaction with the provided TranId
            $existingTransaction = BetNResult::where('tran_id', $request->getTranId())->first();
            if (!$existingTransaction) {
                Log::warning('Transaction not found', [
                    'tran_id' => $request->getTranId(),
                ]);

                return $this->buildErrorResponse(StatusCode::BetTransactionNotFound);
            }

            // Ensure idempotency
            if ($existingTransaction->status === 'cancelled') {
                Log::info('Transaction already cancelled', [
                    'tran_id' => $request->getTranId(),
                ]);

                $balance = $player->wallet->balanceFloat;
                return $this->buildSuccessResponse($balance);
            }

            // Process the refund
            $this->processTransfer(User::adminUser(), $player, TransactionName::Refund, $existingTransaction->bet_amount);

            // Update transaction status to "cancelled"
            $existingTransaction->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $newBalance = $player->wallet->refreshBalance()->balance;
            Log::info('Transaction cancelled successfully', ['new_balance' => $newBalance]);

            DB::commit();
            return $this->buildSuccessResponse($newBalance);
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

    private function buildSuccessResponse(float $newBalance): JsonResponse
    {
        return response()->json([
            'Status' => StatusCode::OK->value,
            'Description' => 'Transaction cancelled successfully',
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
}