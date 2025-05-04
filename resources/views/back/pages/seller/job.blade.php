@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Page Title')
@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="title">
                <h4>Profile</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('seller.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Job
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="pd-20 bg-white border-radius-4 box-shadow mb-30">

    {{-- <div class="colomn">
        <h3 class="text-left " style="color: teal;"><b>Welcome {{ $seller->name }}</b>
            @if ($seller->status == 1)
                <div  class="btn btn-success" disabled>Verified</div>
            @elseif($seller->status == 2)
                <div  class="btn btn-info" disabled>You are pending</dic>
            @else
                <div  class="btn btn-warning" disabled>You are not verified</dic>
            @endif
        </h3>
    </div>
    <h5 style="font-family: monospace; color: rgb(78, 99, 139);">If you want to Change your servise Go to Profile Selection and save it.</h5> --}}
    <div class="card-box pd-20 height-100-p mb-30">
        <div class="row align-items-center">
            <div class="col-md-4">
                <img src="/back/vendors/images/img20.jpg" alt="" />
            </div>
            <div class="col-md-8">
                <h4 class="font-20 weight-500 mb-10 text-capitalize">
                    Welcome back
                    <div class="weight-600 font-30 text-blue">{{ $seller->name }}!
                        @if ($seller->status == 1)
                        <div  class="btn btn-success" disabled>Verified</div>
                    @elseif($seller->status == 2)
                        <div  class="btn btn-info" disabled>You are pending</dic>
                    @else
                        <div  class="btn btn-warning" disabled>You are not verified</dic>
                    @endif
                    </div>
                </h4>
                {{-- <p class="font-18 max-width-600">
                    See your progress in here
                </p> --}}
                <h5 style="font-family: monospace; color: rgb(78, 99, 139);">If you want to Change your servise Go to Profile Selection and save it.</h5>
            </div>
        </div>
    </div>

    @if ($seller->status == 1)
                {{-- <div  class="btn btn-success" disabled>Verified</div> --}}
            @elseif($seller->status == 2)
            <div class="body">
                <br>
                <p class="text-success"><b>Your verification request successfully sent</b></p>
                <hr class="display-4">
                <p class="text-info">you will receive a mail that account is verify or not</p>
            </div>
            @else
                <div class="row pd-10">
                    <h3>Important</h3>

                    <p>Technician must do the verify process for continue repairing services. If the account is not verified, then the technician cannot get customer requests for repairing service, and that account will be deleted after 24 hours.</p>
                </div>
                <hr class="my-4">
                <div class="row justify-content-right">
                    <div class="col-lg-4">
                        <div type="button" class="btn btn-success btn-lg btn-block" name="verify" id="verify" data-toggle="modal" data-target="#VerifyTechnician">
                        Verify
                        </div>
                    </div>
                </div>
            @endif


</div>

<div class="modal" tabindex=-1 role="dialog" id="VerifyTechnician">
    <div class="modal-dialog" role="form">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Verify</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form method="POST" action="{{ route('seller.document-upload') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="sellerid" id="sellerid" value="{{ $seller->id }}">
            <div class="modal-body">
                <div class="container">
                    <div class="form-row">
                        <div class="form group col-md-12">
                            <label for="comment">About Your field</label>
                            <textarea class="form-control" rows="5"  id="comment" name="comment" value="{{ old ('comment') }}"></textarea>
                            <span class="text-danger">@error('comment'){{$message}}@enderror</span>
                        </div>
                    </div>
                    <hr class="my-4">

                    <label>Certificates <p><small>(if you have for your technician field, attach a clear  photo of it)</small></p></label>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <div class="custom-file">
                                <input type="file" name="Certificate" class="custom-file-input" value="{{ old ('Certificate') }}">
                                <label class="custom-file-label" >Choose file</label>
                                <span class="text-danger">@error('Certificate'){{$message}}@enderror</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>

        </form>

      </div>

    </div>

</div>
@endsection


@section('myscript')
<script>



$(document).ready(function(){
    $("#verify").click(function(){
        console.log("verify")
    });


});


</script>
@endsection
