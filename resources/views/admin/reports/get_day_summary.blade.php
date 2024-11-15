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
                            <h3>Get Day Summary</h3>
                        </div>



                        <div class="card-body">
                            <form action="{{ route('daily_summary') }}" method="POST">

                                @csrf
                                <label for="OperatorId">OperatorId:</label>
                                <input type="text" id="OperatorId" name="OperatorId" value="delightMMK" required><br><br>

                                <label for="RequestDateTime">RequestDateTime:</label>
                                <input type="text" id="RequestDateTime" name="RequestDateTime"
                                    value="2024-11-11 12:12:12" required><br><br>

                                <label for="Signature">Signature:</label>
                                <input type="text" id="Signature" name="Signature"
                                    value="b7acad4a4fb0de124cc64106ab6d0eea" required><br><br>

                                <label for="Date">Date:</label>
                                <input type="text" id="Date" name="Date" value="2024-11-14T00:00:00Z"
                                    required><br><br>

                                <button type="submit">Submit</button>
                            </form>

                            @if (session('response'))
                                <h2>Response</h2>
                                <pre>{{ json_encode(session('response'), JSON_PRETTY_PRINT) }}</pre>
                            @endif

                        </div>

                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@endsection
