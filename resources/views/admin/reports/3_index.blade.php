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
                        <a href="{{ route('home') }}" class="btn btn-success " style="width: 100px;"><i
                                class="fas fa-plus text-white  mr-2"></i>Back</a>
                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Report</h3>
                        </div>
                        <div class="card-body">
                            <table id="mytable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>UserName</th>
                                        <th>ProviderName</th>
                                        <th>TotalStake</th>
                                        <th>TotalBet</th>
                                        <th>TotalWin</th>
                                        <th>TotalNetWin</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report as $row)
                                        <tr>
                                            <td>{{ $row->user_name }}</td>
                                            <td>{{ $row->game_provide_name }}</td>
                                            <td>{{ $row->total_games }}</td>
                                            <td>{{ number_format($row->total_bet_amount, 2) }}</td>
                                            <td>{{ number_format($row->total_win_amount, 2) }}</td>
                                            <td>{{ number_format($row->total_net_win, 2) }}</td>
                                            <td><a
                                                    href="{{ route('admin.reports.details', ['game_provide_name' => $row->game_provide_name]) }}">Detail</a>
                                            </td>
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
