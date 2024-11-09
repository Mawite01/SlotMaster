@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">W/L Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ rul('admin/slot/report') }}" class="btn btn-success " style="width: 100px;"><i
                                class="fas fa-plus text-white  mr-2"></i>Back</a>
                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>W/L Report Detail</h3>
                        </div>
                        <div class="card-body">
                            <table id="mytable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Player Name</th>
                                        <th>Game Name</th>
                                        <th>Operator ID</th>
                                        <th>ResultID</th>
                                        <th>DateTime</th>
                                        <th>Currency</th>
                                        <th>Bet</th>
                                        <th>Win</th>
                                        <th>NetWin</th>
                                        <th>RoundID</th>
                                        <th>TransactionDateTime</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($details as $detail)
                                        <tr>
                                            <td>{{ $detail->user_name }}</td>
                                            <td>{{ $detail->player_name }}</td>
                                            <td>{{ $detail->game_name }}</td>
                                            <td>{{ $detail->operator_id }}</td>
                                            <td><a href="https://prodmd.9977997.com/Report/BetDetail?agentCode=E829&WagerID={{ $detail->result_id }}"
                                                    target="_blank"
                                                    style="color: blueviolet; text-decoration: underline;">{{ $report->result_id }}</a>
                                            </td>
                                            <td>{{ $detail->request_date_time }}</td>
                                            <td>{{ $detail->currency }}</td>
                                            <td>{{ number_format($detail->total_bet_amount, 2) }}</td>
                                            <td>{{ number_format($detail->win_amount, 2) }}</td>
                                            <td>{{ number_format($detail->net_win, 2) }}</td>
                                            <td>{{ $detail->round_id }}</td>
                                            <td>{{ $detail->tran_date_time }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@endsection
