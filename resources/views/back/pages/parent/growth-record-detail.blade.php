@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Growth Record Detail')
@section('content')

@php
    $bmiStatus    = $prediction['bmi_status'] ?? null;
    $growthStatus = $prediction['growth_status'] ?? null;
    $recommendations = $prediction['recommendations'] ?? [];
    $nextWeight   = $prediction['next_checkup_weight'] ?? null;
    $concerns     = $prediction['concerns'] ?? [];
    $milestoneExp = $prediction['milestone_expectations'] ?? null;

    $badgeColor = match(strtolower($bmiStatus ?? '')) {
        'normal', 'healthy' => 'success',
        'underweight'       => 'warning',
        'overweight', 'obese' => 'danger',
        default             => 'secondary',
    };

    $heightM = $record->height / 100;
    $bmi     = ($heightM > 0) ? round($record->weight / ($heightM * $heightM), 2) : null;
@endphp

<div class="col-md-12">

    {{-- Navigation --}}
    <div class="mb-3">
        <a href="{{ route('parent.growth-record.index', $baby->baby_id) }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left"></i> Back to Growth Records
        </a>
    </div>

    {{-- Baby Info --}}
    <div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="h4 text-blue">
                    <i class="fa fa-child"></i>
                    {{ $baby->full_name }} — Growth Record Detail
                </h4>
                <p class="text-muted mb-0">
                    DOB: {{ \Carbon\Carbon::parse($baby->date_of_birth)->format('d M Y') }}
                    &nbsp;|&nbsp; Gender: {{ $baby->gender }}
                    &nbsp;|&nbsp; Blood Group: {{ $baby->blood_group }}
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Record Details Card --}}
        <div class="col-md-5">
            <div class="pd-20 card-box mb-30">
                <h5 class="text-blue mb-4"><i class="fa fa-clipboard-list"></i> Measurement Details</h5>
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th class="text-muted" style="width:50%">Record Date</th>
                            <td><strong>{{ \Carbon\Carbon::parse($record->record_date)->format('d M Y') }}</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Age</th>
                            <td><strong>{{ $record->age_months }} months</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Weight</th>
                            <td><strong>{{ $record->weight }} kg</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Height</th>
                            <td><strong>{{ $record->height }} cm</strong></td>
                        </tr>
                        @if($record->head_circumference)
                        <tr>
                            <th class="text-muted">Head Circumference</th>
                            <td><strong>{{ $record->head_circumference }} cm</strong></td>
                        </tr>
                        @endif
                        @if($bmi)
                        <tr>
                            <th class="text-muted">Calculated BMI</th>
                            <td><strong>{{ $bmi }}</strong></td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                @if($record->milestones)
                <div class="mt-3">
                    <h6 class="text-muted">Milestones Observed</h6>
                    <p class="alert alert-light border">{{ $record->milestones }}</p>
                </div>
                @endif

                @if($record->notes)
                <div class="mt-2">
                    <h6 class="text-muted">Notes</h6>
                    <p class="alert alert-light border">{{ $record->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- AI Prediction Card --}}
        <div class="col-md-7">
            <div class="pd-20 card-box mb-30">
                <h5 class="text-blue mb-4">
                    <i class="fa fa-robot"></i> AI Growth Prediction
                    <small class="text-muted ml-2" style="font-size:12px;">Powered by Google Gemini</small>
                </h5>

                @if(empty($prediction))
                    <div id="noPredictionBox">
                        <div class="alert alert-warning mb-3">
                            <i class="fa fa-exclamation-triangle"></i>
                            No AI prediction available for this record.
                            This record was added before AI analysis was enabled.
                        </div>
                        <button id="generatePredBtn" class="btn btn-success">
                            <i class="fa fa-robot"></i> Generate AI Prediction Now
                        </button>
                        <div id="generateError" class="alert alert-danger mt-3 d-none"></div>
                    </div>
                    <div id="predictionDisplay" class="d-none"></div>
                @else

                {{-- Status Cards Row --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-center border-{{ $badgeColor }} p-3">
                            <small class="text-muted">BMI Status</small>
                            <h4 class="text-{{ $badgeColor }} mb-0 mt-1">
                                {{ $bmiStatus ?? '—' }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center border-primary p-3">
                            <small class="text-muted">Growth Status</small>
                            <h5 class="text-primary mb-0 mt-1">
                                {{ $growthStatus ?? '—' }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center border-info p-3">
                            <small class="text-muted">Next Checkup Weight</small>
                            <h5 class="text-info mb-0 mt-1">
                                {{ $nextWeight ? $nextWeight . ' kg' : '—' }}
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Recommendations --}}
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 bg-light p-3 h-100">
                            <h6 class="text-success mb-3">
                                <i class="fa fa-check-circle"></i> Recommendations
                            </h6>
                            @if(count($recommendations))
                                <ul class="list-unstyled mb-0">
                                    @foreach($recommendations as $rec)
                                    <li class="mb-2">
                                        <i class="fa fa-angle-right text-success"></i>
                                        {{ $rec }}
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">No specific recommendations.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Concerns --}}
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 bg-light p-3 h-100">
                            <h6 class="text-danger mb-3">
                                <i class="fa fa-exclamation-circle"></i> Concerns
                            </h6>
                            @if(count($concerns))
                                <ul class="list-unstyled mb-0">
                                    @foreach($concerns as $concern)
                                    <li class="mb-2">
                                        <i class="fa fa-angle-right text-danger"></i>
                                        {{ $concern }}
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-success mb-0">
                                    <i class="fa fa-check"></i> No concerns identified.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Milestone Expectations --}}
                @if($milestoneExp)
                <div class="mt-3">
                    <div class="card border-0 bg-light p-3">
                        <h6 class="text-primary mb-2">
                            <i class="fa fa-star"></i> Milestone Expectations
                        </h6>
                        <p class="mb-0 text-muted">{{ $milestoneExp }}</p>
                    </div>
                </div>
                @endif

                @endif {{-- end if prediction not empty --}}
            </div>
        </div>
    </div>

</div>

@endsection

@section('myscript')
<script>
$(document).ready(function () {

    $('#generatePredBtn').on('click', function () {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');
        $('#generateError').addClass('d-none');

        $.ajax({
            url: "{{ route('parent.growth-record.generate-prediction', $record->record_id) }}",
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.status === 1) {
                    var pred = response.prediction;

                    var badgeColor = 'secondary';
                    var bmiLower = (pred.bmi_status || '').toLowerCase();
                    if (bmiLower === 'normal' || bmiLower === 'healthy') badgeColor = 'success';
                    else if (bmiLower === 'underweight') badgeColor = 'warning';
                    else if (bmiLower === 'overweight' || bmiLower === 'obese') badgeColor = 'danger';

                    var recHtml = '';
                    if (pred.recommendations && pred.recommendations.length) {
                        pred.recommendations.forEach(function(r) { recHtml += '<li class="mb-2"><i class="fa fa-angle-right text-success"></i> ' + r + '</li>'; });
                    } else { recHtml = '<li class="text-muted">None</li>'; }

                    var concHtml = '';
                    if (pred.concerns && pred.concerns.length) {
                        pred.concerns.forEach(function(c) { concHtml += '<li class="mb-2"><i class="fa fa-angle-right text-danger"></i> ' + c + '</li>'; });
                    } else { concHtml = '<li class="text-success"><i class="fa fa-check"></i> No concerns identified.</li>'; }

                    var milestoneHtml = pred.milestone_expectations
                        ? '<div class="mt-3"><div class="card border-0 bg-light p-3"><h6 class="text-primary mb-2"><i class="fa fa-star"></i> Milestone Expectations</h6><p class="mb-0 text-muted">' + pred.milestone_expectations + '</p></div></div>'
                        : '';

                    var html = '<div class="row mb-4">'
                        + '<div class="col-md-4"><div class="card text-center border-' + badgeColor + ' p-3"><small class="text-muted">BMI Status</small><h4 class="text-' + badgeColor + ' mb-0 mt-1">' + (pred.bmi_status || '—') + '</h4></div></div>'
                        + '<div class="col-md-4"><div class="card text-center border-primary p-3"><small class="text-muted">Growth Status</small><h5 class="text-primary mb-0 mt-1">' + (pred.growth_status || '—') + '</h5></div></div>'
                        + '<div class="col-md-4"><div class="card text-center border-info p-3"><small class="text-muted">Next Checkup Weight</small><h5 class="text-info mb-0 mt-1">' + (pred.next_checkup_weight ? pred.next_checkup_weight + ' kg' : '—') + '</h5></div></div>'
                        + '</div>'
                        + '<div class="row">'
                        + '<div class="col-md-6 mb-3"><div class="card border-0 bg-light p-3 h-100"><h6 class="text-success mb-3"><i class="fa fa-check-circle"></i> Recommendations</h6><ul class="list-unstyled mb-0">' + recHtml + '</ul></div></div>'
                        + '<div class="col-md-6 mb-3"><div class="card border-0 bg-light p-3 h-100"><h6 class="text-danger mb-3"><i class="fa fa-exclamation-circle"></i> Concerns</h6><ul class="list-unstyled mb-0">' + concHtml + '</ul></div></div>'
                        + '</div>'
                        + milestoneHtml;

                    $('#noPredictionBox').addClass('d-none');
                    $('#predictionDisplay').removeClass('d-none').html(html);
                } else {
                    $('#generateError').removeClass('d-none').text(response.msg || 'Failed to generate prediction.');
                    btn.prop('disabled', false).html('<i class="fa fa-robot"></i> Generate AI Prediction Now');
                }
            },
            error: function (xhr) {
                var msg = 'An unexpected error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.msg) msg = xhr.responseJSON.msg;
                $('#generateError').removeClass('d-none').text(msg);
                btn.prop('disabled', false).html('<i class="fa fa-robot"></i> Generate AI Prediction Now');
            }
        });
    });
});
</script>
@endsection
