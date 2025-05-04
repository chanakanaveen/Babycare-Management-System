@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Page Title')
@section('content')
<div class="card-box  height-100-p mb-10">
    <div class="row align-items-center">
        <div class="col-md-4">
            <img src="/back/vendors/images/banner-img.png" alt="" />
        </div>
        <div class="col-md-8">
            <h4 class="font-20 weight-500 mb-10 text-capitalize">
                Welcome back
                <div class="weight-600 font-30 text-blue">{{ $client->name }}!</div>
            </h4>
            <h4 class="font-18 max-width-600">
                Choose the right person for your job and get it done.
            </h4>
        </div>
    </div>
</div>

<div class="row-12">
    <img src="/back/vendors/images/banner.png" alt="" />
</div>
<div class="row justify-content-center pd-20">
    <div class="col-lg-4">
        <a type="button" href="{{ route('client.find-servise') }}" class="btn btn-warning btn-lg btn-block">
            Find Service
        </a>
    </div>
</div>

@endsection
