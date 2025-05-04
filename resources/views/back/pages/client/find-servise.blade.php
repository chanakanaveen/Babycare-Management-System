@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Find Servise')
@section('content')
<div class="row-12">
    <img src="/back/vendors/images/banner.png" alt="" />
</div>

<div class="card-box  height-100-p mb-10">
    <div class="row align-items-center ">
        <div class="col-md-12 pd-10">
            <div class="col-md-12">
                <h3 class="font-28 weight-500 mb-10 text-capitalize text-blue text-center">
                    Get any tasks done by professionals
                </h3>
                <h5 class="font-28 weight-500 mb-10 text-capitalize ttext-secondary text-center">
                    We have professionals ready to help
                </h5>
            </div>
        </div>
        <div class="col-md-12">
            <h4 class="font-18 text-center">
                Your nearest town is <span style="color: red">{{ $client->city }}</span> and your location cordinate is <span style="color: rgb(248, 128, 30)">{{ $client->location }}</span>
            </h4>
        </div>
    </div>

    <form action="{{ route('client.find-sellers') }}" method="POST">
        @csrf

        <div class="row justify-content-center pd-5">
            <div class="col-md-4" data-select2-id="8">
                <div class="form-group" data-select2-id="7">
                    <select class="custom-select2 form-control select2-hidden-accessible" name="service" id="service" style="width: 100%; height: 38px" data-select2-id="1" tabindex="-1" aria-hidden="true">
                        <option value="" selected disabled>Select a service</option>
                        @foreach ($services as $row)
                            <option value="{{ $row->id }}">{{ $row->title }}</option>
                        @endforeach
                    </select>
                    @error('service')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row justify-content-center pd-10">
            <div class="col-md-4">
                <button type="submit" class="btn btn-warning btn-lg btn-block">
                    Search
                </button>
            </div>
        </div>

</form>

</div>

@endsection
