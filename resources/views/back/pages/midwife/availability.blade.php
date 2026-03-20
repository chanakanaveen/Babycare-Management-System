@extends('back.layout.pages-layout')
@section('pagetitle', $pageTitle)
@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-12">
            <div class="title"><h4>{{ $pageTitle }}</h4></div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

<div class="card-box">
    <p class="text-muted mb-4">Set your weekly availability. Parents will only be able to book appointments during these time slots.</p>

    <form action="{{ route('midwife.availability.save') }}" method="POST">
        @csrf

        @php
            $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        @endphp

        @for($i = 0; $i < 7; $i++)
        @php
            $dayData = $availability->get($i);
        @endphp
        <div class="row align-items-center mb-3 p-3" style="background: {{ $dayData && $dayData->is_active ? '#f0fdf4' : '#f9fafb' }}; border-radius: 8px; border: 1px solid #e5e7eb;">
            <div class="col-md-2">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input day-toggle"
                           id="day-{{ $i }}" name="days[{{ $i }}][active]" value="1"
                           {{ $dayData && $dayData->is_active ? 'checked' : '' }}>
                    <label class="custom-control-label" for="day-{{ $i }}">
                        <strong>{{ $dayNames[$i] }}</strong>
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <label class="mb-0 text-muted" style="font-size: 13px;">From</label>
                    <input type="time" name="days[{{ $i }}][start_time]" class="form-control form-control-sm"
                           value="{{ $dayData ? substr($dayData->start_time, 0, 5) : '09:00' }}"
                           {{ !$dayData || !$dayData->is_active ? 'disabled' : '' }}>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <label class="mb-0 text-muted" style="font-size: 13px;">To</label>
                    <input type="time" name="days[{{ $i }}][end_time]" class="form-control form-control-sm"
                           value="{{ $dayData ? substr($dayData->end_time, 0, 5) : '17:00' }}"
                           {{ !$dayData || !$dayData->is_active ? 'disabled' : '' }}>
                </div>
            </div>
        </div>
        @endfor

        <button type="submit" class="btn btn-primary mt-3">
            <i class="fa fa-save"></i> Save Availability
        </button>
    </form>
</div>
@endsection

@section('myscript')
<script>
$(function() {
    $('.day-toggle').change(function() {
        var row = $(this).closest('.row');
        var inputs = row.find('input[type="time"]');
        if ($(this).is(':checked')) {
            inputs.prop('disabled', false);
            row.css('background', '#f0fdf4');
        } else {
            inputs.prop('disabled', true);
            row.css('background', '#f9fafb');
        }
    });
});
</script>
@endsection
