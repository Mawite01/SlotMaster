@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                        <li class="breadcrumb-item active">create Agent</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card  col-lg-6 offset-lg-3 col-md-6 offset-md-3 col-sm-8 offset-sm-2 col-10 offset-1" style="border-radius: 15px;">
                <div class="card-header">
                    <div class="card-title col-12">
                        <h5 class="d-inline fw-bold">
                             Create Agent
                        </h5>
                         <a href="{{ route('admin.agent.index') }}" class="btn btn-primary d-inline float-right">
                             <i class="fas fa-arrow-left mr-2" ></i> Back
                         </a>
                   </div>
                </div>
                <form action="{{route('admin.agent.store')}}" method="POST">
                    @csrf
                    <div class="card-body mt-2">
                        <div class="row">
                            <div class="col-lg-12 offset-lg-0 col-md-6 offset-md-3 col-sm-8 offset-sm-2 col-10 offset-1">
                                <div class="form-group">
                                    <label>AgentId<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="user_name" value="{{$agent_name}}" readonly>
                                    @error('user_name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{old('name')}}">
                                    @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Phone<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="phone" value="{{old('phone')}}">
                                    @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12 offset-lg-0 col-md-6 offset-md-3 col-sm-8 offset-sm-2 col-10 offset-1">
                                <div class="form-group">
                                    <label>Password<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="password" value="{{old('password')}}">
                                    @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <span
                                        class="badge badge-success">Max:{{ number_format(optional(auth()->user()->wallet)->balanceFloat, 2) }}</span>
                                    <input type="text" class="form-control" name="amount" value="{{old('amount')}}">
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="card-footer col-12 bg-white">
                        <button type="submit" class="btn btn-success float-right">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </section>
@endsection
@section('script')
<script>
    var successMessage = @json(session('successMessage'));
    var userName = @json(session('username'));
    var password = @json(session('password'));
    var amount = @json(session('amount'));

    @if(session()->has('successMessage'))
    toastr.success(successMessage +
        `
    <div>
        <button class="btn btn-primary btn-sm" data-toggle="modal"
            data-username="${userName}"
            data-password="${password}"
            data-amount="${amount}"
            data-url="https://pandashan.online/login"
            onclick="copyToClipboard(this)">Copy</button>
    </div>`, {
        allowHtml: true
    });
    @endif

    function copyToClipboard(button) {
        var username = $(button).data('username');
        var password = $(button).data('password');
        var amount = $(button).data('amount');
        var url = $(button).data('url');

        var textToCopy = "Username: " + username + "\nPassword: " + password + "\nAmount: " + amount + "\nURL: " + url;

        navigator.clipboard.writeText(textToCopy).then(function() {
            toastr.success("Credentials copied to clipboard!");
        }).catch(function(err) {
            toastr.error("Failed to copy text: " + err);
        });
    }
</script>

@endsection
