@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Home')
@section('content')


<div class="card-box pd-20 height-100-p mb-30">
    <div class="row align-items-center">
        <div class="col-md-4">
            <img src="/back/vendors/images/moh-homepage.jpg" alt="" />
        </div>
        <div class="col-md-8">
            <h4 class="font-20 weight-500 mb-10 text-capitalize">
                Welcome back
                <div class="weight-600 font-30 text-blue">{{ $moh->name }}!</div>
            </h4>
            <p class="font-18 max-width-600">
                See your progress in here
            </p>
        </div>
    </div>
</div>

{{-- card box --}}
<div class="row pb-10">
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark">{{ $midwives }}</div>
                    <div class="font-14 text-secondary weight-500">
                        Active Midwife
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon" data-color="#00eccf" style="color: rgb(0, 236, 207);">
                        <i class="icon-copy fa fa-medkit" aria-hidden="true"></i>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark">{{ $parents }}</div>
                    <div class="font-14 text-secondary weight-500">
                        Active Parents
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon" data-color="#ff5b5b" style="color: rgb(255, 91, 91);">
                        <i class="icon-copy bi bi-person-check-fill"></i>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark">{{ $babies }}</div>
                    <div class="font-14 text-secondary weight-500">
                        Babys
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon">
                        <i class="icon-copy fi-social-myspace"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark">{{ $vaccines }}</div>
                    <div class="font-14 text-secondary weight-500">Vaccinations Completed</div>
                </div>
                <div class="widget-icon">
                    <div class="icon" data-color="#09cc06" style="color: rgb(9, 204, 6);">
                        <i class="icon-copy ion-ios-body"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
