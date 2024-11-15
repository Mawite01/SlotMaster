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
                        <form action="{{ route('daily_summary') }}" method="POST">
                            @csrf
                            <div class="card-body mt-2">
                                <h1>Get Day Summary</h1>
                                <div class="row">
                                    <div
                                        class="col-lg-12 offset-lg-0 col-md-6 offset-md-3 col-sm-8 offset-sm-2 col-10 offset-1 ">


                                        <div class="form-group">
                                            <label for="OperatorId">OperatorId:</label>
                                            <input type="text" id="OperatorId" name="OperatorId" value="delightMMK"
                                                required><br><br>
                                        </div>


                                        <div class="form-group">
                                            <label for="RequestDateTime">RequestDateTime:</label>
                                            <input type="text" id="RequestDateTime" name="RequestDateTime"
                                                value="2024-11-11 12:12:12" required><br><br>
                                        </div>

                                        <div class="form-group">
                                            <label for="Signature">Signature:</label>
                                            <input type="text" id="Signature" name="Signature"
                                                value="b7acad4a4fb0de124cc64106ab6d0eea" required><br><br>
                                        </div>

                                        <div class="form-group">
                                            <label for="Date">Date:</label>
                                            <input type="text" id="Date" name="Date" value="2024-11-14T00:00:00Z"
                                                required><br><br>
                                        </div>

                                        <button type="submit">Submit</button>

                                    </div>


                                    @if (session('response'))
                                        <h2>Response</h2>
                                        <pre>{{ json_encode(session('response'), JSON_PRETTY_PRINT) }}</pre>
                                    @endif
                                    </body>
                                </div>

                                <div class="card-footer col-12 bg-white">
                                    <button type="submit" class="btn btn-success float-right">Submit</button>
                                </div>
                        </form>

                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@endsection
