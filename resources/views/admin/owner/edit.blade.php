@extends('layouts.master')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                    <li class="breadcrumb-item active">Edit Agent</li>
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
                    <h5 class="d-inline fw-bold">Edit Owner</h5>
                    <a href="{{ route('admin.owner.index') }}" class="btn btn-primary float-right">
                        <i class="fas fa-arrow-left" style="font-size: 20px;"></i> Back
                    </a>

                </div>
            </span>
            </h3>
            </div>
            <form method="POST" action="{{ route('admin.owner.update',$owner->id) }}" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2 col-md-8 offset-md-2 col-sm-8 offset-sm-2 col-10 offset-1">
                            <div class="form-group">
                                <label>OwnerId<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="user_name" value="{{$owner->user_name}}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $owner->name }}">
                            </div>
                            <div class="form-group">
                                <label>Phone<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" value="{{ $owner->phone }}">
                            </div>
                            <div class="form-group">
                                <label>Owner logo</label>
                                <input type="file" class="form-control" name="agent_logo">
                                @if($owner->agent_logo)
                                 <img src="{{asset('assets/img/logo').'/'. $owner->agent_logo}}" alt="" width="100px">
                                 @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer col-12 bg-white">
                    <button type="submit" class="btn btn-success float-right">Update</button>
                </div>
            </form>
        </div>

    </div>
    </div>
</section>
@endsection
