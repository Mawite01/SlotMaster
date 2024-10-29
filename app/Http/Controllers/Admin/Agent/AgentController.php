<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Enums\TransactionName;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\Http\Requests\TransferLogRequest;
use App\Models\Admin\TransferLog;
use App\Models\PaymentType;
use App\Models\User;
use App\Services\WalletService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private const AGENT_ROLE = 2;

    public function index(): View
    {
        if (! Gate::allows('agent_index')) {
            abort(403);
        }

        //kzt
        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('role_id', self::AGENT_ROLE);
            })
            ->where('agent_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();

        //kzt
        return view('admin.agent.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        if (! Gate::allows('agent_create')) {
            abort(403);
        }

        $agent_name = $this->generateRandomString();
        $paymentTypes = PaymentType::all();

        return view('admin.agent.create', compact('agent_name', 'paymentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws ValidationException
     */
    public function store(AgentRequest $request): RedirectResponse
    {
        if (! Gate::allows('agent_create')) {
            abort(403);
        }

        $master = Auth::user();
        $inputs = $request->validated();

        if (isset($inputs['amount']) && $inputs['amount'] > $master->balanceFloat) {
            return redirect()->back()->with('error', 'Balance Insufficient');
        }
        $transfer_amount = $inputs['amount'];
        $userPrepare = array_merge(
            $inputs,
            [
                'password' => Hash::make($inputs['password']),
                'agent_id' => Auth::id(),
                'type' => UserType::Admin,
            ]
        );

        $agent = User::create($userPrepare);
        $agent->roles()->sync(self::AGENT_ROLE);

        if (isset($inputs['amount'])) {
            app(WalletService::class)->transfer($master, $agent, $inputs['amount'], TransactionName::CreditTransfer);
        }

        return redirect()->route('admin.agent.index')
            ->with('successMessage', 'Agent created successfully')
            ->with('password', $request->password)
            ->with('username', $agent->user_name)
            ->with('amount', $transfer_amount);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        if (! Gate::allows('agent_edit')) {
            abort(403);
        }

        $agent = User::find($id);
        $paymentTypes = PaymentType::all();

        return view('admin.agent.edit', compact('agent', 'paymentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        if (! Gate::allows('agent_edit')) {
            abort(403);
        }

        $user = User::find($id);

        $user->update($request->all());

        return redirect()->route('admin.agent.index')
            ->with('success', 'Agent Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function getCashIn(string $id): View
    {
        if (! Gate::allows('make_transfer')) {
            abort(403);
        }
        $agent = User::find($id);

        return view('admin.agent.cash_in', compact('agent'));
    }

    public function getCashOut(string $id): View
    {
        if (! Gate::allows('make_transfer')) {
            abort(403);
        }
        // Assuming $id is the user ID
        $agent = User::findOrFail($id);

        return view('admin.agent.cash_out', compact('agent'));
    }

    public function makeCashIn(Request $request, $id): RedirectResponse
    {
        if (! Gate::allows('make_transfer')) {
            abort(403);
        }

        try {
            $agent = User::findOrFail($id);
            $admin = Auth::user();
            if ($request->amount > $admin->balanceFloat) {
                throw new \Exception('You do not have enough balance to transfer!');
            }

            app(WalletService::class)->transfer($admin, $agent, $request->amount, TransactionName::CreditTransfer, ['note' => $request->note]);

            return redirect()->route('admin.agent.index')->with('success', 'Money fill request submitted successfully!');
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function makeCashOut(TransferLogRequest $request, string $id): RedirectResponse
    {
        if (! Gate::allows('make_transfer')) {
            abort(403);
        }

        try {
            $agent = User::findOrFail($id);
            $admin = Auth::user();
            $cashOut = $request->amount;

            if ($cashOut > $agent->balanceFloat) {

                return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
            }

            // Transfer money
            app(WalletService::class)->transfer($agent, $admin, $request->amount, TransactionName::DebitTransfer, ['note' => $request->note]);

            return redirect()->back()->with('success', 'Money fill request submitted successfully!');
        } catch (Exception $e) {

            session()->flash('error', $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Money fill request submitted successfully!');
    }

    public function getTransferDetail($id)
    {
        abort_if(
            Gate::denies('make_transfer') || ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $transfer_detail = TransferLog::where('from_user_id', $id)
            ->orWhere('to_user_id', $id)
            ->get();

        return view('admin.agent.transfer_detail', compact('transfer_detail'));
    }

    private function generateRandomString()
    {
        $randomNumber = mt_rand(10000000, 99999999);

        return 'P'.$randomNumber;
    }

    public function banAgent($id): RedirectResponse
    {
        $user = User::find($id);
        $user->update(['status' => $user->status == 1 ? 0 : 1]);

        return redirect()->back()->with(
            'success',
            'User '.($user->status == 1 ? 'activate' : 'inactive').' successfully'
        );
    }

    public function getChangePassword($id)
    {
        abort_if(
            Gate::denies('agent_change_password_access') || ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        $agent = User::find($id);

        return view('admin.agent.change_password', compact('agent'));
    }

    public function makeChangePassword($id, Request $request)
    {
        abort_if(
            Gate::denies('agent_change_password_access') || ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $agent = User::find($id);
        $agent->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.agent.index')
            ->with('successMessage', 'Agent Change Password successfully')
            ->with('password', $request->password)
            ->with('username', $agent->user_name);
    }

    public function showAgentLogin($id)
    {
        $agent = User::findOrFail($id);

        return view('auth.agent_login', compact('agent'));
    }

    public function AgentToPlayerDepositLog()
    {
        $transactions = DB::table('transactions')
            ->join('users as players', 'players.id', '=', 'transactions.payable_id')
            ->join('users as agents', 'agents.id', '=', 'players.agent_id')
            ->where('transactions.type', 'deposit')
            ->where('transactions.name', 'credit_transfer')
            ->where('agents.id', '<>', 1) // Exclude agent_id 1
            ->groupBy('agents.id', 'players.id', 'agents.name', 'players.name', 'agents.commission')
            ->select(
                'agents.id as agent_id',
                'agents.name as agent_name',
                'players.id as player_id',
                'players.name as player_name',
                'agents.commission as agent_commission', // Get the commission percentage
                DB::raw('count(transactions.id) as total_deposits'),
                DB::raw('sum(transactions.amount) as total_amount')
            )
            ->get();

        return view('admin.agent.agent_to_play_dep_log', compact('transactions'));
    }

    public function AgentToPlayerDetail($agent_id, $player_id)
    {
        // Retrieve detailed information about the agent and player
        $transactionDetails = DB::table('transactions')
            ->join('users as players', 'players.id', '=', 'transactions.payable_id')
            ->join('users as agents', 'agents.id', '=', 'players.agent_id')
            ->where('agents.id', $agent_id)
            ->where('players.id', $player_id)
            ->where('transactions.type', 'deposit')
            ->where('transactions.name', 'credit_transfer')
            ->select(
                'agents.name as agent_name',
                'players.name as player_name',
                'transactions.amount',
                'transactions.created_at',
                'agents.commission as agent_commission'
            )
            ->get();

        return view('admin.agent.agent_to_player_detail', compact('transactionDetails'));
    }

    public function AgentWinLoseReport(Request $request)
    {
        $query = DB::table('reports')
            ->join('users', 'reports.agent_id', '=', 'users.id')
            ->select(
                'reports.agent_id',
                'users.name as agent_name',
                DB::raw('COUNT(DISTINCT reports.id) as qty'),
                DB::raw('SUM(reports.bet_amount) as total_bet_amount'),
                DB::raw('SUM(reports.valid_bet_amount) as total_valid_bet_amount'),
                DB::raw('SUM(reports.payout_amount) as total_payout_amount'),
                DB::raw('SUM(reports.commission_amount) as total_commission_amount'),
                DB::raw('SUM(reports.jack_pot_amount) as total_jack_pot_amount'),
                DB::raw('SUM(reports.jp_bet) as total_jp_bet'),
                DB::raw('(SUM(reports.payout_amount) - SUM(reports.valid_bet_amount)) as win_or_lose'),
                DB::raw('COUNT(*) as stake_count'),
                DB::raw('DATE_FORMAT(reports.created_at, "%Y %M") as report_month_year')
            );

        // Apply the date filter if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('reports.created_at', [$request->start_date, $request->end_date]);
        } elseif ($request->has('month_year')) {
            // Filter by month and year if provided
            $monthYear = Carbon::parse($request->month_year);
            $query->whereMonth('reports.created_at', $monthYear->month)
                ->whereYear('reports.created_at', $monthYear->year);
        }

        $agentReports = $query->groupBy('reports.agent_id', 'users.name', 'report_month_year')->get();

        return view('admin.agent.agent_report_index', compact('agentReports'));
    }

    public function AgentWinLoseDetails($agent_id, $month)
    {
        $details = DB::table('reports')
            ->join('users', 'reports.agent_id', '=', 'users.id')
            ->where('reports.agent_id', $agent_id)
            ->whereMonth('reports.created_at', Carbon::parse($month)->month)
            ->whereYear('reports.created_at', Carbon::parse($month)->year)
            ->select(
                'reports.*',
                'users.name as agent_name',
                'users.commission as agent_comm',
                DB::raw('(reports.payout_amount - reports.valid_bet_amount) as win_or_lose') // Calculating win_or_lose
            )
            ->get();

        return view('admin.agent.win_lose_details', compact('details'));
    }

    public function AuthAgentWinLoseReport(Request $request)
    {
        $agentId = Auth::user()->id;  // Get the authenticated user's agent_id

        $query = DB::table('reports')
            ->join('users', 'reports.agent_id', '=', 'users.id')
            ->select(
                'reports.agent_id',
                'users.name as agent_name',
                DB::raw('COUNT(DISTINCT reports.id) as qty'),
                DB::raw('SUM(reports.bet_amount) as total_bet_amount'),
                DB::raw('SUM(reports.valid_bet_amount) as total_valid_bet_amount'),
                DB::raw('SUM(reports.payout_amount) as total_payout_amount'),
                DB::raw('SUM(reports.commission_amount) as total_commission_amount'),
                DB::raw('SUM(reports.jack_pot_amount) as total_jack_pot_amount'),
                DB::raw('SUM(reports.jp_bet) as total_jp_bet'),
                DB::raw('(SUM(reports.payout_amount) - SUM(reports.valid_bet_amount)) as win_or_lose'),
                DB::raw('COUNT(*) as stake_count'),
                DB::raw('DATE_FORMAT(reports.created_at, "%Y %M") as report_month_year')
            )
            ->where('reports.agent_id', $agentId);  // Filter by authenticated user's agent_id

        // Apply the date filter if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('reports.created_at', [$request->start_date, $request->end_date]);
        } elseif ($request->has('month_year')) {
            // Filter by month and year if provided
            $monthYear = Carbon::parse($request->month_year);
            $query->whereMonth('reports.created_at', $monthYear->month)
                ->whereYear('reports.created_at', $monthYear->year);
        }

        $agentReports = $query->groupBy('reports.agent_id', 'users.name', 'report_month_year')->get();

        return view('admin.agent.auth_agent_report_index', compact('agentReports'));
    }

    public function AuthAgentWinLoseDetails($agent_id, $month)
    {
        $details = DB::table('reports')
            ->join('users', 'reports.agent_id', '=', 'users.id')
            ->where('reports.agent_id', $agent_id)
            ->whereMonth('reports.created_at', Carbon::parse($month)->month)
            ->whereYear('reports.created_at', Carbon::parse($month)->year)
            ->select(
                'reports.*',
                'users.name as agent_name',
                'users.commission as agent_comm',
                DB::raw('(reports.payout_amount - reports.valid_bet_amount) as win_or_lose') // Calculating win_or_lose
            )
            ->get();

        return view('admin.agent.auth_win_lose_details', compact('details'));
    }
}
