@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Player Lists</li>
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
                        <a href="{{ route('admin.player.create') }}" class="btn btn-success " style="width: 100px;"><i
                                class="fas fa-plus text-white  mr-2"></i>Create</a>
                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Report</h3>
                        </div>
                        <div class="card-body">
                            <table id="mytable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Game Provider Name</th>
                                        <th>User Name</th>
                                        <th>Total Bet Amount</th>
                                        <th>Total Win Amount</th>
                                        <th>Total Net Win</th>
                                        <th>Total Games</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report as $row)
                                        <tr>
                                            <td>{{ $row->game_provide_name }}</td>
                                            <td>{{ $row->user_name }}</td>
                                            <td>{{ number_format($row->total_bet_amount, 2) }}</td>
                                            <td>{{ number_format($row->total_win_amount, 2) }}</td>
                                            <td>{{ number_format($row->total_net_win, 2) }}</td>
                                            <td>{{ $row->total_games }}</td>
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
