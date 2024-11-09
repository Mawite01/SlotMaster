<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Webhook\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function getReportGroupedByGameProvider()
{
    $report = Result::select(
            'game_provide_name',
            DB::raw('SUM(total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(win_amount) as total_win_amount'),
            DB::raw('SUM(net_win) as total_net_win'),
            DB::raw('COUNT(*) as total_games'),
            'users.name as user_name'
        )
        ->join('users', 'results.user_id', '=', 'users.id')  // Join with the users table
        ->groupBy('game_provide_name', 'users.name')  // Group by both game provider and user name
        ->get();

    //return $report;
    return view('admin.reports.index', compact('report'));
}

    public function getReportDetails($game_provide_name)
{
    $details = Result::where('game_provide_name', $game_provide_name)
        ->join('users', 'results.user_id', '=', 'users.id')
        ->select('results.*', 'users.name as user_name')
        ->get();

    return view('admin.reports.detail', compact('details', 'game_provide_name'));
}


    public function getTransactionDetails($operatorId, $tranId)
{
    $url = 'https://api.sm-sspi-uat.com/api/opgateway/v1/op/GetTransactionDetails'; // Replace with the actual URL

    // Generate the RequestDateTime in UTC
    $requestDateTime = Carbon::now('UTC')->format('Y-m-d H:i:s');

    // Generate the signature using MD5 hashing
    $secretKey = 's4fZpFsRfGp3VMeG'; // Replace with your actual secret key
    $functionName = 'GetTransactionDetails';
    $signatureString = $functionName . $requestDateTime . $operatorId . $secretKey;
    $signature = md5($signatureString);

    // Prepare request payload
    $payload = [
        'OperatorId' => $operatorId,
        'RequestDateTime' => $requestDateTime,
        'Signature' => $signature,
        'TranId' => $tranId
    ];

    try {
        // Make the POST request to the API endpoint
        $response = Http::post($url, $payload);

        // Check if the response is successful
        if ($response->successful()) {
            return $response->json(); // Return the response data as JSON
        } else {
            Log::error('Failed to get transaction details', ['response' => $response->body()]);
            return response()->json(['error' => 'Failed to get transaction details'], 500);
        }
    } catch (\Exception $e) {
        Log::error('API request error', ['message' => $e->getMessage()]);
        return response()->json(['error' => 'API request error'], 500);
    }
}

}

/*
use Illuminate\Support\Facades\DB;

public function getReportGroupedByGameProvider()
{
    $report = DB::select(DB::raw("
        SELECT results.*, users.name as user_name,
               SUM(total_bet_amount) as total_bet_amount,
               SUM(win_amount) as total_win_amount,
               SUM(net_win) as total_net_win,
               COUNT(*) as total_games
        FROM results
        JOIN users ON results.user_id = users.id
        GROUP BY game_provide_name, user_name
    "));

    return view('admin.reports.index', compact('report'));
}
**/