@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Bulk Vaccination Schedules')
@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="title">
                <h4>Bulk Vaccination Scheduling</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('midwife.home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bulk Vaccinations</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card-box pd-20 mb-30">
            <h5 class="h5 mb-20 text-blue">Send Vaccination Schedule by Age Group</h5>
            <p class="text-muted">Target an entire age group of babies under your care. The system will automatically construct the schedules and dispatch alerts to the corresponding parents in real-time.</p>

            <form action="{{ route('midwife.bulk-vaccination.store') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Select Vaccine <span class="text-danger">*</span></label>
                    <select class="custom-select form-control" name="vaccine_id" required>
                        <option value="">Choose Vaccine...</option>
                        @foreach($vaccines as $vaccine)
                            <option value="{{ $vaccine->vaccine_id }}">{{ $vaccine->vaccine_name }} (Recommended @ {{ $vaccine->recommended_age_months }}m)</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold">Dose Number <span class="text-danger">*</span></label>
                    <input class="form-control" type="number" name="dose_number" value="1" min="1" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Target Min Age (Months) <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" name="min_age_months" value="0" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Target Max Age (Months) <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" name="max_age_months" value="2" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold">Clinic / Scheduled Date <span class="text-danger">*</span></label>
                    <input class="form-control" type="date" name="scheduled_date" required min="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold">Additional Notes & Instructions</label>
                    <textarea class="form-control" name="notes" rows="3" placeholder="E.g., Please bring the baby's health card and arrive 15 minutes early."></textarea>
                </div>

                <div class="text-right">
                    <button class="btn btn-primary" id="deployBtn" type="submit">
                        <i class="fa fa-paper-plane mr-2" id="deployIcon"></i>
                        <span id="deployText">Deploy Bulk Schedule</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('myscript')
<script>
    $(document).ready(function() {
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        
        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
        
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif

        // Form Submission Loader
        $('form').on('submit', function() {
            var btn = $('#deployBtn');
            btn.prop('disabled', true);
            $('#deployIcon').removeClass('fa-paper-plane').addClass('fa-spinner fa-spin');
            $('#deployText').text('Processing schedules and sending notifications...');
        });
    });
</script>
@endsection
