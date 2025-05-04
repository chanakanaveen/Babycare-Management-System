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
                        <a href="{{ route('client.home') }}">Home</a>
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
                <a href="javascript:;" onclick="event.preventDefault();document.getElementById('clientProfilePictureFile').click();" class="edit-avatar"><i class="fa fa-pencil"></i></a>
                <img src="{{ $client->picture }}"  alt="" class="avatar-photo" id="clientProfilePicture">
                <input type="file" name="clientProfilePictureFile" id="clientProfilePictureFile" class="d-none" style="opacity:0">
            </div>
            <h5 class="text-center h5 mb-0" id="adminProfileName">{{ $client->name }}</h5>
            <p class="text-center text-muted font-14" id="adminProfileEmail">
                {{ $client->email }}
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
                                <form action="{{ route('client.update_profile') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Name</label>
                                                <input type="text" class="form-control" name='name' id="name" placeholder="Enter full name" value="{{ $client->name }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Email</label>
                                                <input type="text" class="form-control" name='email' id="email" placeholder="Enter email" value="{{ $client->email }}">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Username</label>
                                                <input type="text" class="form-control" name='username' id="username" placeholder="Enter username" value="{{ $client->username }}">
                                                @error('username')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="">Address</label>
                                                <input type="text" class="form-control" name='address' id="address" placeholder="Enter address" value="{{ $client->address}}">
                                                @error('address')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Phone</label>
                                                <input type="text" class="form-control" name='phone' id="phone" placeholder="Enter phone" value="{{ $client->phone }}">
                                                @error('phone')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4" data-select2-id="8">
                                            <div class="form-group" data-select2-id="7">
                                                <label>Provinces</label>
                                                <select class="custom-select2 form-control select2-hidden-accessible" name="provinces" id="provinces" style="width: 100%; height: 38px" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                                    <option value="" selected disabled>Select a province</option>
                                                    @foreach ($provinces as $row )
                                                        <option value="{{ $row->id }}" {{ $row->id == $client->provinces ? 'selected' : '' }} >{{ $row->name_en }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4" data-select2-id="8">
                                            <div class="form-group" data-select2-id="7">
                                                <label>Provinces</label>
                                                <select class="custom-select2 form-control select2-hidden-accessible" name="districts" id="districts" style="width: 100%; height: 38px" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                                    <option value="" selected disabled>Select a district</option>
                                                    @foreach ($districts as $row )
                                                        <option value="{{ $row->id }}" {{ $row->id == $client->districts ? 'selected' : '' }} >{{ $row->name_en }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4" data-select2-id="8">
                                            <div class="form-group" data-select2-id="7">
                                                <label>Cities</label>
                                                <select class="custom-select2 form-control select2-hidden-accessible" name="cities" id="cities" style="width: 100%; height: 38px" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                                    <option value="" selected disabled>Select a citie</option>
                                                    @foreach ($cities as $row )
                                                        <option value="{{ $row->id }}" {{ $row->id == $client->cities ? 'selected' : '' }} >{{ $row->name_en }}</option>
                                                    @endforeach
                                                </select>
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
                                <form action="{{ route('client.change-password') }}" method="POST">
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
                                    <button type="submit" class="btn btn-primary" disabled>Update password</button>
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

$('input[type="file"][name="clientProfilePictureFile"][id="clientProfilePictureFile"]').ijaboCropTool({
    preview : '#clientProfilePicture',
    setRatio:1,
    allowedExtensions: ['jpg', 'jpeg','png'],
    buttonsText:['CROP','QUIT'],
    buttonsColor:['#30bf7d','#ee5155', -15],
    processUrl:'{{ route("client.change-profile-picture") }}',
    withCSRF:['_token','{{ csrf_token() }}'],
    onSuccess:function(message, element, status){

        toastr.success(message);
    },
    onError:function(message, element, status){
        console.log(message);
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
