<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Webhook\Result;
use Illuminate\Support\Facades\DB;

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
}