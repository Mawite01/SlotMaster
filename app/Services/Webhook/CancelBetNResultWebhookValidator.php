<?php

namespace App\Services\Webhook;

use App\Enums\StatusCode;
use App\Http\Requests\Slot\CancelBetNResultRequest;
use Illuminate\Support\Facades\Log;

class CancelBetNResultWebhookValidator
{
    protected function __construct(protected CancelBetNResultRequest $request) {}

    public function validate(): array
    {
        if (!$this->isValidSignature()) {
            return $this->response(StatusCode::InvalidSignature);
        }

        if (!$this->request->getMember()) {
            return $this->response(StatusCode::InvalidPlayer);
        }

        return $this->response(StatusCode::OK);
    }

    protected function isValidSignature(): bool
    {
        $method = "CancelBetNResult";
        $tranId = $this->request->getTranId();
        $requestTime = $this->request->getRequestDateTime();
        $operatorCode = $this->request->getOperatorId();
        $secretKey = $this->getSecretKey();
        $playerId = $this->request->getPlayerId();

        Log::info('Generating signature', [
            'method' => $method,
            'tranId' => $tranId,
            'requestTime' => $requestTime,
            'operatorCode' => $operatorCode,
            'secretKey' => $secretKey,
            'playerId' => $playerId,
        ]);

        $signature = md5($method . $tranId . $requestTime . $operatorCode . $secretKey . $playerId);
        Log::info('Generated signature', ['signature' => $signature]);

        return $this->request->getSignature() === $signature;
    }

    protected function getSecretKey(): string
    {
        $secretKey = config('game.api.secret_key');
        Log::info('Fetched secret key');
        return $secretKey;
    }

    protected function response(StatusCode $responseCode): array
    {
        return [
            'Status' => $responseCode->value,
            'Description' => $responseCode->name,
        ];
    }

    public static function make(CancelBetNResultRequest $request): self
    {
        return new self($request);
    }
}