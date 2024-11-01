<?php

namespace App\Http\Requests\Slot;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use App\Services\Webhook\CancelBetNResultWebhookValidator;


class CancelBetNResultRequest extends FormRequest
{
    private ?User $member;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'OperatorId' => 'required|string|max:20',
            'RequestDateTime' => 'required|string|max:50',
            'Signature' => 'required|string|max:50',
            'PlayerId' => 'required|string|max:50',
            'Currency' => 'required|string|max:5',
            'TranId' => 'required|string|max:30',
            'GameCode' => 'required|string|max:50',
            'BetAmount' => 'required|numeric',
            'WinAmount' => 'nullable|numeric',
            'TranDateTime' => 'required|date',
        ];
    }

    public function check()
    {
        $validator = CancelBetNResultWebhookValidator::make($this)->validate();

        return $validator;
    }




    public function getOperatorId() { return $this->get('OperatorId'); }
    public function getRequestDateTime() { return $this->get('RequestDateTime'); }
    public function getSignature() { return $this->get('Signature'); }
    public function getPlayerId() { return $this->get('PlayerId'); }
    public function getCurrency() { return $this->get('Currency'); }
    public function getTranId() { return $this->get('TranId'); }
    public function getGameCode() { return $this->get('GameCode'); }
    public function getBetAmount() { return $this->get('BetAmount'); }
    public function getWinAmount() { return $this->get('WinAmount'); }
    public function getTranDateTime() { return $this->get('TranDateTime'); }

    public function getMember()
    {
        $playerId = $this->getPlayerId();
        return User::where('user_name', $playerId)->first();
    }
}