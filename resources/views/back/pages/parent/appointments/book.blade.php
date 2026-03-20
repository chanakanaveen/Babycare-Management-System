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

@if(session('fail'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('fail') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

<div class="card-box">
    <form action="{{ route('parent.appointment.store') }}" method="POST" id="booking-form">
        @csrf

        {{-- Step 1: Select Baby & Reason --}}
        <div id="step-1">
            <h5 class="mb-3"><span class="badge badge-primary mr-2">1</span> Select Baby & Reason</h5>

            <div class="form-group">
                <label for="baby_id">Select Baby <span class="text-danger">*</span></label>
                <select name="baby_id" id="baby_id" class="form-control @error('baby_id') is-invalid @enderror" required>
                    <option value="">-- Select a Baby --</option>
                    @foreach($babies as $baby)
                        <option value="{{ $baby->baby_id }}" {{ old('baby_id') == $baby->baby_id ? 'selected' : '' }}>
                            {{ $baby->full_name }} ({{ $baby->date_of_birth }})
                        </option>
                    @endforeach
                </select>
                @error('baby_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="reason">Reason for Visit <span class="text-danger">*</span></label>
                <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror"
                          rows="3" minlength="10" maxlength="500" required
                          placeholder="Describe the reason (min 10 characters)...">{{ old('reason') }}</textarea>
                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="button" class="btn btn-primary" id="to-step-2">
                Next: Select Date <i class="fa fa-arrow-right"></i>
            </button>
        </div>

        {{-- Step 2: Select Date --}}
        <div id="step-2" style="display:none;">
            <h5 class="mb-3"><span class="badge badge-primary mr-2">2</span> Select Date</h5>

            <div id="availability-info" class="mb-3 p-3" style="background: #f0fdf4; border-radius: 8px; display:none;">
                <strong>Midwife:</strong> <span id="midwife-name-display"></span>
                <div class="mt-2" id="available-days-display"></div>
            </div>

            <div class="form-group">
                <label for="appointment_date">Appointment Date <span class="text-danger">*</span></label>
                <input type="date" name="appointment_date" id="appointment_date"
                       class="form-control @error('appointment_date') is-invalid @enderror"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       max="{{ date('Y-m-d', strtotime('+60 days')) }}"
                       value="{{ old('appointment_date') }}" required>
                @error('appointment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex" style="gap: 8px;">
                <button type="button" class="btn btn-secondary" id="back-to-step-1">
                    <i class="fa fa-arrow-left"></i> Back
                </button>
                <button type="button" class="btn btn-primary" id="to-step-3">
                    Next: Select Time <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- Step 3: Select Time --}}
        <div id="step-3" style="display:none;">
            <h5 class="mb-3"><span class="badge badge-primary mr-2">3</span> Select Time Slot</h5>

            <input type="hidden" name="appointment_time" id="appointment_time" value="{{ old('appointment_time') }}">

            <div id="time-slots" class="mb-3" style="display: flex; flex-wrap: wrap; gap: 8px;">
                <p class="text-muted">Select a date first to see available time slots</p>
            </div>

            <div class="d-flex" style="gap: 8px;">
                <button type="button" class="btn btn-secondary" id="back-to-step-2">
                    <i class="fa fa-arrow-left"></i> Back
                </button>
                <button type="button" class="btn btn-primary" id="to-step-4">
                    Next: Review <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- Step 4: Review & Submit --}}
        <div id="step-4" style="display:none;">
            <h5 class="mb-3"><span class="badge badge-primary mr-2">4</span> Review & Confirm</h5>

            <div class="card-box" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <table class="table table-borderless mb-0">
                    <tr><td style="width:120px; font-weight:600;">Baby</td><td id="review-baby"></td></tr>
                    <tr><td style="font-weight:600;">Midwife</td><td id="review-midwife"></td></tr>
                    <tr><td style="font-weight:600;">Date</td><td id="review-date"></td></tr>
                    <tr><td style="font-weight:600;">Time</td><td id="review-time"></td></tr>
                    <tr><td style="font-weight:600;">Reason</td><td id="review-reason"></td></tr>
                </table>
            </div>

            <div class="d-flex mt-3" style="gap: 8px;">
                <button type="button" class="btn btn-secondary" id="back-to-step-3">
                    <i class="fa fa-arrow-left"></i> Back
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-check"></i> Confirm Booking
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('myscript')
<script>
$(function() {
    var availabilityData = null;

    // Step navigation
    $('#to-step-2').click(function() {
        if (!$('#baby_id').val()) { toastr.error('Please select a baby'); return; }
        if ($('#reason').val().length < 10) { toastr.error('Reason must be at least 10 characters'); return; }
        $('#step-1').hide(); $('#step-2').show();
        // Fetch availability
        $.get('{{ route("parent.appointment.availability") }}', { baby_id: $('#baby_id').val() }, function(res) {
            if (res.status === 1) {
                availabilityData = res.data;
                $('#midwife-name-display').text(res.data.midwife_name);
                var dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                var html = '<strong>Available Days:</strong> ';
                res.data.availability.forEach(function(a) {
                    html += '<span class="badge badge-success mr-1">' + dayNames[a.day_of_week] + ' (' + a.start_time + ' - ' + a.end_time + ')</span>';
                });
                $('#available-days-display').html(html);
                $('#availability-info').show();
            }
        });
    });
    $('#back-to-step-1').click(function() { $('#step-2').hide(); $('#step-1').show(); });
    $('#to-step-3').click(function() {
        if (!$('#appointment_date').val()) { toastr.error('Please select a date'); return; }
        $('#step-2').hide(); $('#step-3').show();
        generateTimeSlots();
    });
    $('#back-to-step-2').click(function() { $('#step-3').hide(); $('#step-2').show(); });
    $('#to-step-4').click(function() {
        if (!$('#appointment_time').val()) { toastr.error('Please select a time slot'); return; }
        $('#step-3').hide(); $('#step-4').show();
        // Populate review
        $('#review-baby').text($('#baby_id option:selected').text());
        $('#review-midwife').text($('#midwife-name-display').text());
        $('#review-date').text($('#appointment_date').val());
        $('#review-time').text($('#appointment_time').val());
        $('#review-reason').text($('#reason').val());
    });
    $('#back-to-step-3').click(function() { $('#step-4').hide(); $('#step-3').show(); });

    function generateTimeSlots() {
        var slotsDiv = $('#time-slots');
        slotsDiv.html('<p class="text-muted">Loading...</p>');

        if (!availabilityData) {
            slotsDiv.html('<p class="text-danger">No availability data.</p>');
            return;
        }

        var date = new Date($('#appointment_date').val());
        var dayOfWeek = date.getDay();
        var dayAvail = availabilityData.availability.filter(function(a) { return parseInt(a.day_of_week) === dayOfWeek; });

        if (dayAvail.length === 0) {
            slotsDiv.html('<p class="text-danger">Midwife is not available on this day. Please select another date.</p>');
            return;
        }

        var bookedSlots = (availabilityData.booked_slots || [])
            .filter(function(s) { return s.appointment_date === $('#appointment_date').val(); })
            .map(function(s) { return s.appointment_time; });

        var html = '';
        dayAvail.forEach(function(avail) {
            var start = avail.start_time.split(':');
            var end = avail.end_time.split(':');
            var startMin = parseInt(start[0]) * 60 + parseInt(start[1]);
            var endMin = parseInt(end[0]) * 60 + parseInt(end[1]);

            for (var m = startMin; m < endMin; m += 30) {
                var h = Math.floor(m / 60);
                var mi = m % 60;
                var timeStr = (h < 10 ? '0' + h : h) + ':' + (mi < 10 ? '0' + mi : mi) + ':00';
                var displayTime = (h > 12 ? h - 12 : h) + ':' + (mi < 10 ? '0' + mi : mi) + (h >= 12 ? ' PM' : ' AM');
                if (h === 0) displayTime = '12:' + (mi < 10 ? '0' + mi : mi) + ' AM';

                var isBooked = bookedSlots.some(function(b) {
                    return b.substring(0, 5) === timeStr.substring(0, 5);
                });

                if (!isBooked) {
                    html += '<button type="button" class="btn btn-outline-primary time-slot-btn" data-time="' + timeStr + '" style="min-width:100px;">' + displayTime + '</button>';
                }
            }
        });

        if (!html) {
            html = '<p class="text-danger">No available time slots for this date.</p>';
        }
        slotsDiv.html(html);

        // Time slot click
        $(document).off('click', '.time-slot-btn').on('click', '.time-slot-btn', function() {
            $('.time-slot-btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $('#appointment_time').val($(this).data('time'));
        });
    }

    // Re-generate on date change
    $('#appointment_date').change(function() { if ($('#step-3').is(':visible')) generateTimeSlots(); });
});
</script>
@endsection
