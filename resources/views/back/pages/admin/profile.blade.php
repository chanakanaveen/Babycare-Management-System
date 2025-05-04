@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Profile')
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
                        <a href="{{ route('admin.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Profile
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
        <div class="pd-20 card-box height-100-p">
            <div class="profile-photo">
                <a href="javascript:;" onclick="event.preventDefault();document.getElementById('adminProfilePictureFile').click();" class="edit-avatar"><i class="fa fa-pencil"></i></a>
                <img src="{{ $admin->picture }}"  alt="" class="avatar-photo" id="adminProfilePicture">
                <input type="file" name="adminProfilePictureFile" id="adminProfilePictureFile" class="d-none" style="opacity:0">
            </div>
            <h5 class="text-center h5 mb-0" id="adminProfileName">{{ $admin->name }}</h5>
            <p class="text-center text-muted font-14" id="adminProfileEmail">
                {{ $admin->email }}
            </p>

        </div>
    </div>
    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
        <div class="card-box height-100-p overflow-hidden">
        {{-- @livewire('admin-profile-tabs') --}}
        <div>

            <div class="profile-tab height-100-p">
                <div class="tab height-100-p">
                    <ul class="nav nav-tabs customtab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active " data-toggle="tab" href="#personal_details" role="tab">Personal details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " data-toggle="tab" href="#update_password" role="tab">Update password</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- Timeline Tab start -->
                        <div class="tab-pane fade show active " id="personal_details" role="tabpanel">
                            <div class="pd-20">
                                <form action="{{ route('admin.update_profile') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Name</label>
                                                <input type="text" class="form-control" name='name' id="name" placeholder="Enter full name" value="{{ $admin->name }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Email</label>
                                                <input type="text" class="form-control" name='email' id="email" placeholder="Enter email" value="{{ $admin->email }}">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Username</label>
                                                <input type="text" class="form-control" name='username' id="username" placeholder="Enter username" value="{{ $admin->username }}">
                                                @error('username')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group-btn" id="" >
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <!-- Timeline Tab End -->
                        <!-- Tasks Tab start -->
                        <div class="tab-pane fade " id="update_password" role="tabpanel">
                            <div class="pd-20 profile-task-wrap">
                                <form action="{{ route('admin.change-password') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Current password</label>
                                                <input type="password" placeholder="Enter current password" name='current_password' id="current_password" class="form-control">
                                                @error('current_password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">New password</label>
                                                <input type="password" placeholder="Enter new password" name='new_password' id="new_password" class="form-control">
                                                @error('new_password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Confirm new password</label>
                                                <input type="password" placeholder="Retype new password" name='new_password_confirmation' id="new_password_confirmation" class="form-control">
                                                @error('new_password_confirmation')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update password</button>
                                </form>
                            </div>
                        </div>
                        <!-- Tasks Tab End -->

                    </div>
                </div>
            </div>

        </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
    <script>
        window.addEventListener('updateAdminInfo', function(event){
            $('#adminProfileName').html(event.detail[0].adminName);
            $('#adminProfileEmail').html(event.detail[0].adminEmail);
        });

        $('input[type="file"][name="adminProfilePictureFile"][id="adminProfilePictureFile"]').ijaboCropTool({
          preview : '#adminProfilePicture',
          setRatio:1,
          allowedExtensions: ['jpg', 'jpeg','png'],
          buttonsText:['CROP','QUIT'],
          buttonsColor:['#30bf7d','#ee5155', -15],
          processUrl:'{{ route("admin.change-profile-picture") }}',
          withCSRF:['_token','{{ csrf_token() }}'],
          onSuccess:function(message, element, status){
            // Livewire.dispatch('updateAdminSellerHeaderInfo');
             toastr.success(message);
          },
          onError:function(message, element, status){
            toastr.error(message);
          }
       });
    </script>
@endpush

@section('myscript')
<script>

//notification
function notyConfirm(type, message) {
  noty({
      title: "Stock Availability",
      text: '',
      layout: 'topRight',
      closeWith: ['click', 'hover'],
      type: type,
      template: '<div class="noty_message" id="noty_body"> <br> <b>' + message + '</b> <br ><div class="noty_close"></div></div>'
  });
}

$(document).ready(function(){
    $("#save").click(function(){
        console.log("save click")
    });

    $(document).ready(function(){
        $("#name").change(function(){
            console.log("name change")
            toastr.success('Profile updated successfully completed');
            // notyConfirm('success','Profile updated successfully completed')
        });
    });
});


</script>
@endsection
