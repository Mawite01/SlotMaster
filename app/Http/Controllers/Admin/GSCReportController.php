<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GSCReportController extends Controller
{
    public function index(Request $request)
    {
        // Call the private function to get the joined table data
        $data = $this->makeJoinTable($request);

        // Pass the data to the view
        return view('admin.gsc.report.index', compact('data'));
    }

    private function makeJoinTable(Request $request)
    {
        $query = DB::table('reports')
            ->select([
                'products.name as product_name',
                DB::raw('COUNT(reports.id) as total_record'), // Total Record
                DB::raw('SUM(reports.bet_amount) as total_bet'), // Total Bet
                DB::raw('SUM(reports.valid_bet_amount) as total_valid_bet'), // Total Valid Bet
                DB::raw('SUM(reports.jp_bet) as total_prog_jp'), // Total Progressive JP Bet
                DB::raw('SUM(reports.payout_amount) as total_payout'), // Total Payout
                DB::raw('SUM(reports.payout_amount - reports.valid_bet_amount) as total_win_lose'), // Total Win/Loss

                // Member-related columns
                DB::raw('SUM(reports.agent_commission) as member_comm'), // Member Commission
                //DB::raw('0 as member_total'), // Placeholder for member total

                // Upline-related columns
                DB::raw('SUM(reports.agent_commission) as upline_comm'), // Upline Commission
                DB::raw('SUM(reports.payout_amount - reports.valid_bet_amount) as upline_total'), // Upline Win/Loss
            ])
            ->join('products', 'reports.product_code', '=', 'products.code') // Joining reports with products
            ->where('reports.status', '101') // Filter by status '101'
            ->when(isset($request->start_date) && isset($request->end_date), function ($query) use ($request) {
                $query->whereBetween('reports.created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
            })
            ->groupBy('products.name'); // Group by product name

        return $query->get();
    }

    public function ReportDetails($productName)
    {
        // Fetch detailed information about the selected product
        $details = DB::table('reports')
            ->select([
                'reports.wager_id',
                'members.user_name as member_name', // Member alias
                DB::raw('agents.user_name as agent_name'), // Get agent's name
                'products.name as product_name',
                'game_lists.name as game_name', // Changed to use game_lists
                'reports.bet_amount',
                'reports.valid_bet_amount',
                'reports.payout_amount as payout',
                DB::raw('(reports.payout_amount - reports.valid_bet_amount) as win_loss'),
                'reports.settlement_date as settle_match_date',
            ])
            ->join('products', 'reports.product_code', '=', 'products.code')
            ->join('game_lists', 'reports.game_type_id', '=', 'game_lists.id') // Joining with game_lists
            ->join('users as members', 'reports.member_name', '=', 'members.user_name') // Join for member using alias 'members'
            ->join('users as agents', 'reports.agent_id', '=', 'agents.id') // Join for agent using alias 'agents'
            ->where('products.name', $productName)
            ->get();

        // Pass the details to the view
        return view('admin.gsc.report.details', compact('details'));
    }

    public function AgentWinLoseindex()
    {
        // Call the private function to get the joined table data
        $data = $this->AgentmakeJoinTable();

        // Pass the data to the view
        return view('admin.gsc.report.agent_index', compact('data'));
    }

    private function AgentmakeJoinTable()
    {
        $agentId = auth()->id(); // Get the authenticated agent's ID

        $query = DB::table('reports')
            ->select([
                'products.name as product_name',
                DB::raw('COUNT(reports.id) as total_record'), // Total Record
                DB::raw('SUM(reports.bet_amount) as total_bet'), // Total Bet
                DB::raw('SUM(reports.valid_bet_amount) as total_valid_bet'), // Total Valid Bet
                DB::raw('SUM(reports.jp_bet) as total_prog_jp'), // Total Progressive JP Bet
                DB::raw('SUM(reports.payout_amount) as total_payout'), // Total Payout
                DB::raw('SUM(reports.payout_amount - reports.valid_bet_amount) as total_win_lose'), // Total Win/Loss

                // Member-related columns
                DB::raw('SUM(reports.agent_commission) as member_comm'), // Member Commission

                // Upline-related columns
                DB::raw('SUM(reports.agent_commission) as upline_comm'), // Upline Commission
                DB::raw('SUM(reports.payout_amount - reports.valid_bet_amount) as upline_total'), // Upline Win/Loss
            ])
            ->join('products', 'reports.product_code', '=', 'products.code') // Joining reports with products
            ->where('reports.status', '101') // Filter by status '101'
            ->where('reports.agent_id', $agentId) // Filter by authenticated agent ID
            ->groupBy('products.name'); // Group by product name

        return $query->get();
    }

    // public function ReportDetails($productName)
    // {
    //     // Fetch detailed information about the selected product
    //     $details = DB::table('reports')
    //         ->select([
    //             'reports.wager_id',
    //             'users.user_name as member_name',
    //             DB::raw("CONCAT('AgentName: ', users.user_name) as agent_name"), // Get agent's name
    //             'products.name as product_name',
    //             'game_lists.name as game_name', // Changed to use game_lists
    //             'reports.bet_amount',
    //             'reports.valid_bet_amount',
    //             'reports.payout_amount as payout',
    //             DB::raw('(reports.payout_amount - reports.valid_bet_amount) as win_loss'),
    //             'reports.settlement_date as settle_match_date' // Corrected field name to match your schema
    //         ])
    //         ->join('products', 'reports.product_code', '=', 'products.code')
    //         ->join('game_lists', 'reports.game_type_id', '=', 'game_lists.id') // Joining with game_lists
    //       ->join('users', 'reports.member_name', '=', 'users.user_name') // Join for member
    //         ->join('users as agents', 'reports.agent_id', '=', 'agents.id') // Join for agent using reports.agent_id
    //         ->where('products.name', $productName)
    //         ->get();
    //     // Pass the details to the view
    //     return view('admin.gsc.report.details', compact('details'));
    // }
}

// The private function from your code to generate the data
// private function makeJoinTable()
// {
//     $query = User::query()->roleLimited();

//     $query->select([
//         'products.name as product_name',
//         DB::raw('COUNT(reports.id) as total_record'), // Total Record
//         DB::raw('SUM(reports.bet_amount) as total_bet'), // Total Bet
//         DB::raw('SUM(reports.valid_bet_amount) as total_valid_bet'), // Total Valid Bet
//         DB::raw('SUM(reports.jp_bet) as total_prog_jp'), // Total Prog JP
//         DB::raw('SUM(reports.payout_amount) as total_payout'), // Total Payout
//         DB::raw('SUM(reports.payout_amount - reports.valid_bet_amount) as total_win_lose'), // Total Win/Loss

//         // Member-related columns
//         DB::raw('SUM(reports.agent_commission) as member_comm'), // Member Commission
//         // DB::raw('0 as member_total'), // Total for Member (change as per your logic)

//         // Upline-related columns
//         DB::raw('SUM(reports.agent_commission) as upline_comm'), // Upline Commission
//         DB::raw('SUM(reports.payout_amount - reports.valid_bet_amount) as upline_total'), // Total for Upline (adjust if necessary)
//     ])
//     ->join('reports', 'reports.member_name', '=', 'users.user_name')
//     ->join('products', 'reports.product_code', '=', 'products.code')
//     ->where('reports.status', '101') // Status filter
//     ->groupBy('products.name');

//     return $query->get();
// }
