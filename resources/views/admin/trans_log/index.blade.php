@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>TransferLog</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">TransferLog</li>
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
                    <div class="card">
                        <div class="card-body">
                            <table id="mytable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>To User</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transferLogs as $log)
                                        <tr>
                                            <td>
                                                {{ $log->created_at }}
                                            </td>
                                            <td>{{ $log->targetUser->name }}</td>
                                            <td>
                                                <div
                                                    class="d-flex align-items-center text-{{ $log->type == 'withdraw' ? 'success' : 'danger' }} text-gradient text-sm font-weight-bold ms-auto">
                                                    {{ number_format(abs($log->amountFloat)) }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($log->type == 'deposit')
                                                    <p class="text-danger font-weight-bold">Withdraw</p>
                                                @else
                                                    <p class="text-success font-weight-bold">Deposit</p>
                                                @endif
                                            </td>
                                            @if($log->note == "null")
                                            <td></td>
                                            @else
                                            <td>{{$log->note}}</td>
                                            @endif
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
