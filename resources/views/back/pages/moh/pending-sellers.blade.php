@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Midwifes')
@section('content')
<div class="card-box pb-10">
    <div class="h5 pd-20 mb-0">Midwifes</div>
    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
        <div class="row">
            <div class="col-sm-12 col-md-6"></div>
            <div class="col-sm-12 col-md-6"></div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="data-table table nowrap dataTable no-footer dtr-inline" id="DataTables_Table_0" role="grid">
        <thead>
            <tr role="row">
                <th  tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1"  >Name</th>
                <th  tabindex="0" rowspan="1" colspan="1" >Devision</th>
                <th  tabindex="0"  rowspan="1" colspan="1" >Location</th>
                <th  tabindex="0"  rowspan="1" colspan="1" >Register Date</th>
                <th  tabindex="0"  rowspan="1" colspan="1" >Baby Count</th>
                <th  tabindex="0"  rowspan="1" colspan="1">Status</th>
                <th class="datatable-nosort sorting_disabled" rowspan="1" colspan="1" >Actions</th></tr>
        </thead>
        <tbody>
            @foreach ($sellers as $row)
            <tr role="row" class="odd">
                <td class="table-plus sorting_1" tabindex="0">
                    <div class="name-avatar d-flex align-items-center">
                        <div class="avatar mr-2 flex-shrink-0">
                            <img src="{{  asset('images/users/sellers/' . $row->picture ) }}" class="border-radius-100 shadow" width="40" height="40" alt="">
                        </div>
                        <div class="txt">
                            <div class="weight-400">{{ $row->name }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $row->division_name }}</td>
                <td>{{ $row->cities }}</td>
                <td>{{ $row->date }}</td>
                <td>
                    {{ $row->baby_count }}
                </td>
                <td>
                    <span class="badge badge-pill" data-bgcolor="#e7ebf5" data-color="#265ed7" style="color: rgb(38, 94, 215); background-color: rgb(231, 235, 245);">{{ $row->approval_status }}</span>
                </td>
                <td>
                        <input
                            type="checkbox"
                            {{ $row->is_approved ? 'checked' : '' }}
                            class="switch-btn "
                            data-color="#28a745"
                            {{-- data-secondary-color="#f56767" --}}
                            name="verify"
                            id="verify"
                            data-id="{{ $row->id }}"
                            data-status="{{ $row->is_approved ? 'Approved' : 'Not Approved' }}"
                        />
                </td>
            </tr>
            @endforeach


            {{-- <tr role="row" class="even">
                <td class="table-plus sorting_1" tabindex="0">
                    <div class="name-avatar d-flex align-items-center">
                        <div class="avatar mr-2 flex-shrink-0">
                            <img src="vendors/images/photo5.jpg" class="border-radius-100 shadow" width="40" height="40" alt="">
                        </div>
                        <div class="txt">
                            <div class="weight-600">Doris L. Larson</div>
                        </div>
                    </div>
                </td>
                <td>Male</td>
                <td>76 kg</td>
                <td>Dr. Ren Delan</td>
                <td>22 Jul 2020</td>
                <td>
                    <span class="badge badge-pill" data-bgcolor="#e7ebf5" data-color="#265ed7" style="color: rgb(38, 94, 215); background-color: rgb(231, 235, 245);">Dengue</span>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="#" data-color="#265ed7" style="color: rgb(38, 94, 215);"><i class="icon-copy dw dw-edit2"></i></a>
                        <a href="#" data-color="#e95959" style="color: rgb(233, 89, 89);"><i class="icon-copy dw dw-delete-3"></i></a>
                    </div>
                </td>
            </tr>
            <tr role="row" class="odd">
                <td class="table-plus sorting_1" tabindex="0">
                    <div class="name-avatar d-flex align-items-center">
                        <div class="avatar mr-2 flex-shrink-0">
                            <img src="vendors/images/photo1.jpg" class="border-radius-100 shadow" width="40" height="40" alt="">
                        </div>
                        <div class="txt">
                            <div class="weight-600">Doris L. Larson</div>
                        </div>
                    </div>
                </td>
                <td>Male</td>
                <td>76 kg</td>
                <td>Dr. Ren Delan</td>
                <td>22 Jul 2020</td>
                <td>
                    <span class="badge badge-pill" data-bgcolor="#e7ebf5" data-color="#265ed7" style="color: rgb(38, 94, 215); background-color: rgb(231, 235, 245);">Dengue</span>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="#" data-color="#265ed7" style="color: rgb(38, 94, 215);"><i class="icon-copy dw dw-edit2"></i></a>
                        <a href="#" data-color="#e95959" style="color: rgb(233, 89, 89);"><i class="icon-copy dw dw-delete-3"></i></a>
                    </div>
                </td>
            </tr>
            <tr role="row" class="even">
                <td class="table-plus sorting_1" tabindex="0">
                    <div class="name-avatar d-flex align-items-center">
                        <div class="avatar mr-2 flex-shrink-0">
                            <img src="vendors/images/photo9.jpg" class="border-radius-100 shadow" width="40" height="40" alt="">
                        </div>
                        <div class="txt">
                            <div class="weight-600">Jake Springer</div>
                        </div>
                    </div>
                </td>
                <td>Female</td>
                <td>45 kg</td>
                <td>Dr. Garrett Kincy</td>
                <td>08 Oct 2020</td>
                <td>
                    <span class="badge badge-pill" data-bgcolor="#e7ebf5" data-color="#265ed7" style="color: rgb(38, 94, 215); background-color: rgb(231, 235, 245);">Covid 19</span>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="#" data-color="#265ed7" style="color: rgb(38, 94, 215);"><i class="icon-copy dw dw-edit2"></i></a>
                        <a href="#" data-color="#e95959" style="color: rgb(233, 89, 89);"><i class="icon-copy dw dw-delete-3"></i></a>
                    </div>
                </td>
            </tr>
            <tr role="row" class="odd">
                <td class="table-plus sorting_1" tabindex="0">
                    <div class="name-avatar d-flex align-items-center">
                        <div class="avatar mr-2 flex-shrink-0">
                            <img src="vendors/images/photo4.jpg" class="border-radius-100 shadow" width="40" height="40" alt="">
                        </div>
                        <div class="txt">
                            <div class="weight-600">Jennifer O. Oster</div>
                        </div>
                    </div>
                </td>
                <td>Female</td>
                <td>45 kg</td>
                <td>Dr. Callie Reed</td>
                <td>19 Oct 2020</td>
                <td>
                    <span class="badge badge-pill" data-bgcolor="#e7ebf5" data-color="#265ed7" style="color: rgb(38, 94, 215); background-color: rgb(231, 235, 245);">Typhoid</span>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="#" data-color="#265ed7" style="color: rgb(38, 94, 215);"><i class="icon-copy dw dw-edit2"></i></a>
                        <a href="#" data-color="#e95959" style="color: rgb(233, 89, 89);"><i class="icon-copy dw dw-delete-3"></i></a>
                    </div>
                </td>
            </tr> --}}
        </tbody>
    </table>
</div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-5"></div>
    <div class="col-sm-12 col-md-7">
        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
            <ul class="pagination"><li class="paginate_button page-item previous disabled" id="DataTables_Table_0_previous"><a href="#" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" class="page-link"><i class="ion-chevron-left"></i></a></li><li class="paginate_button page-item active"><a href="#" aria-controls="DataTables_Table_0" data-dt-idx="1" tabindex="0" class="page-link">1</a></li><li class="paginate_button page-item "><a href="#" aria-controls="DataTables_Table_0" data-dt-idx="2" tabindex="0" class="page-link">2</a></li><li class="paginate_button page-item next" id="DataTables_Table_0_next"><a href="#" aria-controls="DataTables_Table_0" data-dt-idx="3" tabindex="0" class="page-link"><i class="ion-chevron-right"></i></a></li>
            </ul>
        </div>
    </div>
</div>
</div>
</div>

{{-- model --}}
<div class="modal" tabindex=-1 role="dialog" name="docmodal" id="docmodal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">
                    Seller Expereince
                </h4>
                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-hidden="true"
                >
                    ×
                </button>
            </div>
            <div class="modal-body">
                <h1 class="lead" style="color: darkblue;"><b>About Field</b></h1>
                <div class="" name="about" id="about"></div>
                <hr class="my-4">
                <h1 class="lead" style="color: darkblue;"><b>Certificate</b></h1>
                <img class="img-fluid" id="ceimg" src="" alt="Certificate Photo" />
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal"
                >
                    Close
                </button>

            </div>
        </div>
    </div>

</div>

{{-- confirm modal --}}
<div
    class="modal fade"
    id="verifymodalconfirm"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div
        class="modal-dialog modal-dialog-centered"
        role="document"
    >
        <div class="modal-content">
            <div class="modal-body text-center font-18">
                <h4 class="padding-top-30 mb-30 weight-500">
                    Are you sure you want to Verify This Midwife?
                </h4>
                <div
                    class="padding-bottom-30 row"
                    style="max-width: 170px; margin: 0 auto"
                >
                    <div class="col-6">
                        <button
                            type="button"
                            class="btn btn-secondary border-radius-100 btn-block confirmation-btn"
                            data-dismiss="modal"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                        NO
                    </div>
                    <div class="col-6">
                        <button
                            type="button"
                            class="btn btn-primary border-radius-100 btn-block confirmation-btn"
                            data-dismiss="modal"
                            id="verifyconfirm"
                        >
                            <i class="fa fa-check"></i>
                        </button>
                        YES
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- seller unverify modal --}}
<div
    class="modal fade"
    id="unverfiymodal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="myLargeModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-warning">
            <div class="modal-body text-center">
                <h3 class="mb-15">
                    <i class="fa fa-exclamation-triangle"></i> Warning
                </h3>
                <p>
                    Are you sure you want to Unverify This Midwife?
                </p>
                <button
                    type="button"
                    class="btn btn-dark"
                    id="unverifyconfirm"
                >
                    Yes
                </button>

                <button
                    type="button"
                    class="btn btn-dark"
                    data-dismiss="modal"
                >
                   No
                </button>
            </div>
        </div>
    </div>
</div>

@endsection


@section('myscript')
<script>
$(document).ready(function(){

    var selectid = '';

    //modal open
    $(document).on("click", "#view",function(){
       var about =  $(this).data("about");
       var url = $(this).data("url");
       $("#about").text(about);
       $("#ceimg").attr("src",url);

       $("#docmodal").modal('show');

    });

    $(document).on("change", "#verify",function(value){
        selectid = $(this).data("id");
        var status = $(this).data("status");
        console.log(selectid,status);
        var isChecked = $(this).prop("checked");
        console.log(isChecked);
        if (isChecked) {
            $("#verifymodalconfirm").modal('show');
        } else {
            // Checkbox is unchecked
            $("#unverfiymodal").modal('show');
        }
    });

    //verify confirm yes
    $("#verifyconfirm").click(function(){
        console.log("verify confirm");
        console.log(selectid);
        $.ajax({
            url:'{{ route('moh.approve-midwife') }}',
            method:"POST",
            data:{"seid":selectid},
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            beforeSend:function(){
                $("#verifymodalconfirm").modal('toggle');
            },
            success:function(response){
                console.log(response);
                if(response.status == 1){
                    location.reload();
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

    //unverify confirm yes
    $("#unverifyconfirm").click(function(){
        console.log("unverify confirm");
        $.ajax({
            url:'{{ route('moh.revoke-midwife') }}',
            method:"POST",
            data:{"seid":selectid},
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            beforeSend:function(){
                $("#unverfiymodal").modal('toggle');
            },
            success:function(response){
                console.log(response);
                if(response.status == 1){
                    location.reload();
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
