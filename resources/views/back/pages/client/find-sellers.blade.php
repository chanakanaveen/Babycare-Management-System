@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Find Servise')
@section('content')
{{-- <div class="row-12">
    <img src="/back/vendors/images/banner.png" alt="" />
</div> --}}

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-6 mb-20">
        <div class="card-box height-100-p pd-20 min-height-200px">
            <div class="d-flex justify-content-between pb-10">
                <div class="h5 mb-0">Sellers</div>
                <div class="pull-right">
                    <a href="{{ route('client.find-servise') }}" class="btn btn-primary btn-sm">
                     <i class="ion-arrow-left-a"></i> Back to find service
                    </a>
                </div>
            </div>
            <div class="user-list">
                <ul>
                    <input type="hidden" name="serviseid" id="serviseid" value="{{ $serviseid }}">
                    @foreach ($nearestseller as $row )
                        <li class="d-flex align-items-center justify-content-between">
                            <div class="name-avatar d-flex align-items-center pr-2">
                                <div class="avatar mr-2 flex-shrink-0">
                                    <img
                                       src="{{  asset('images/users/sellers/' . $row->picture ) }}"
                                        class="border-radius-100 box-shadow"
                                        width="80"
                                        height="80"
                                        alt=""
                                    />
                                </div>
                                <div class="txt">
                                    {{-- <span
                                        class="badge badge-pill badge-sm"
                                        data-bgcolor="#e7ebf5"
                                        data-color="#265ed7"
                                        >4.9</span
                                    > --}}
                                    <div class="font-14 weight-600">{{ $row->name }}</div>
                                    <div class="font-13 weight-500" data-color="#b2b1b6">{{ $row->service1 }} | {{ $row->service2 }} | {{ $row->service3 }}</div>
                                    <div class="font-12 weight-600">{{ $row->email }} - {{ $row->phone }}</div>
                                    <div class="font-12 weight-600">{{ $row->city }}</div>
                                </div>
                            </div>
                            <div class="cta flex-shrink-0">
                                <div type="button" name="request" id="request" data-id="{{ $row->id }}" class="btn btn-sm btn-outline-primary">Request service</div>
                            </div>
                        </li>
                    @endforeach

                </ul>
            </div>
        </div>
    </div>
</div>

@endsection


@section('myscript')
<script>
$(document).ready(function(){

    //request click
    $(document).on("click", "#request", function(){
        var slid =  $(this).data("id");
        var serviseid = $("#serviseid").val();
       console.log('request click',slid);
        console.log('servise id', serviseid);
       $.ajax({
            url:'{{ route('client.servise-request') }}',
            method:"POST",
            data:{"slid":slid,"serviseid":serviseid},
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            beforeSend:function(){

            },
            success:function(response){
                console.log(response);
                if(response.status == 1){
                    toastr.success(response.msg);
                }else{
                    toastr.error(response.msg);
                }
            },
            error:function(xhr, status, error){
                console.log(xhr.responseText);
            }
        });
    });

});

</script>
@endsection
