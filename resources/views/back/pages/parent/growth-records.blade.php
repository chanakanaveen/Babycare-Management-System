@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Growth Records')
@section('content')

<div class="col-md-12">
    {{-- Baby Info Header --}}
    <div class="pd-20 card-box mb-30">
        <div class="clearfix mb-3">
            <div class="pull-left">
                <h4 class="h4 text-blue">
                    <i class="fa fa-child"></i>
                    Growth Records — {{ $baby->full_name }}
                </h4>
                <p class="text-muted mb-0">
                    DOB: {{ \Carbon\Carbon::parse($baby->date_of_birth)->format('d M Y') }}
                    &nbsp;|&nbsp; Gender: {{ $baby->gender }}
                    &nbsp;|&nbsp; Blood Group: {{ $baby->blood_group }}
                    &nbsp;|&nbsp; Current BMI: <strong>{{ $baby->bmi ?? 'N/A' }}</strong>
                </p>
            </div>
            <div class="pull-right">
                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addRecordModal">
                    <i class="fa fa-plus"></i> Add Growth Record
                </button>
                <a href="{{ route('parent.baby') }}" class="btn btn-secondary btn-sm ml-2">
                    <i class="fa fa-arrow-left"></i> Back to Babies
                </a>
            </div>
        </div>

        {{-- Records Table --}}
        <div class="table-responsive mt-3">
            <table class="table table-borderless table-striped">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Age (months)</th>
                        <th>Weight (kg)</th>
                        <th>Height (cm)</th>
                        <th>Head Circ. (cm)</th>
                        <th>BMI Status</th>
                        <th>Growth Status</th>
                        <th>Milestones</th>
                        <th>AI Prediction</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $i => $record)
                    @php
                        $pred = $record->ai_prediction ?? [];
                        $bmiStatus = $pred['bmi_status'] ?? null;
                        $growthStatus = $pred['growth_status'] ?? null;
                        $badgeColor = match(strtolower($bmiStatus ?? '')) {
                            'normal', 'healthy' => 'success',
                            'underweight' => 'warning',
                            'overweight', 'obese' => 'danger',
                            default => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->record_date)->format('d M Y') }}</td>
                        <td>{{ $record->age_months }}</td>
                        <td>{{ $record->weight }}</td>
                        <td>{{ $record->height }}</td>
                        <td>{{ $record->head_circumference ?? '—' }}</td>
                        <td>
                            @if($bmiStatus)
                                <span class="badge badge-{{ $badgeColor }}">{{ $bmiStatus }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($growthStatus)
                                <span class="text-{{ $badgeColor }}"><i class="fa fa-circle"></i> {{ $growthStatus }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                            title="{{ $record->milestones }}">
                            {{ $record->milestones ? Str::limit($record->milestones, 40) : '—' }}
                        </td>
                        <td>
                            <a href="{{ route('parent.growth-record.show', [$baby->baby_id, $record->record_id]) }}"
                               class="btn btn-primary btn-xs btn-sm">
                                <i class="fa fa-robot"></i> View AI
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-danger">
                            No growth records found. Add the first record using the button above.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Record Modal --}}
<div class="modal fade" id="addRecordModal" tabindex="-1" aria-labelledby="addRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addRecordModalLabel">
                    <i class="fa fa-robot"></i> Add Growth Record + AI Prediction
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addRecordForm">
                    @csrf
                    <input type="hidden" name="baby_id" value="{{ $baby->baby_id }}">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.1" max="200" class="form-control" name="weight" id="weight" placeholder="e.g. 7.5" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Height (cm) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="1" max="250" class="form-control" name="height" id="height" placeholder="e.g. 68.0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Age (months) <span class="text-danger">*</span></label>
                            <input type="number" min="0" max="216" class="form-control" name="age_months" id="age_months" placeholder="e.g. 6" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Head Circumference (cm)</label>
                            <input type="number" step="0.1" min="0" max="100" class="form-control" name="head_circumference" placeholder="e.g. 42.5">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Milestones <small class="text-muted">(optional — e.g. sitting, babbling)</small></label>
                        <textarea class="form-control" name="milestones" rows="2" placeholder="Describe any developmental milestones observed..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes <small class="text-muted">(optional)</small></label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </form>

                {{-- AI Prediction Result (shown after save) --}}
                <div id="aiPredictionResult" class="d-none mt-3">
                    <hr>
                    <h6 class="text-success"><i class="fa fa-robot"></i> AI Growth Prediction</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-0 bg-light p-3 text-center">
                                <small class="text-muted">BMI Status</small>
                                <h5 id="pred_bmi_status" class="mb-0">—</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light p-3 text-center">
                                <small class="text-muted">Growth Status</small>
                                <h5 id="pred_growth_status" class="mb-0">—</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light p-3 text-center">
                                <small class="text-muted">Next Checkup Weight</small>
                                <h5 id="pred_next_weight" class="mb-0">—</h5>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Recommendations:</strong>
                            <ul id="pred_recommendations" class="mt-1"></ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Concerns:</strong>
                            <ul id="pred_concerns" class="mt-1"></ul>
                        </div>
                    </div>
                    <div class="mt-2">
                        <strong>Milestone Expectations:</strong>
                        <p id="pred_milestones" class="text-muted mt-1"></p>
                    </div>
                    <a id="viewDetailLink" href="#" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fa fa-eye"></i> View Full Details
                    </a>
                </div>

                {{-- Error box --}}
                <div id="formError" class="alert alert-danger d-none mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="saveRecordBtn">
                    <i class="fa fa-robot"></i> <span id="saveBtnText">Save & Get AI Prediction</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('myscript')
<script>
$(document).ready(function () {

    $('#saveRecordBtn').on('click', function () {
        var btn = $(this);
        var form = $('#addRecordForm');

        // Collect form data
        var formData = {};
        form.serializeArray().forEach(function(item) {
            formData[item.name] = item.value;
        });

        // Basic validation
        if (!formData.weight || !formData.height || !formData.age_months) {
            $('#formError').removeClass('d-none').text('Weight, Height and Age are required.');
            return;
        }

        // Disable button and show loading
        btn.prop('disabled', true);
        $('#saveBtnText').text('Processing AI Prediction...');
        $('#formError').addClass('d-none');
        $('#aiPredictionResult').addClass('d-none');

        $.ajax({
            url: "{{ route('parent.growth-record.store') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            success: function (response) {
                if (response.status === 1) {
                    var pred = response.prediction || {};
                    var record = response.data || {};

                    // Populate prediction cards
                    $('#pred_bmi_status').text(pred.bmi_status || '—');
                    $('#pred_growth_status').text(pred.growth_status || '—');
                    $('#pred_next_weight').text(pred.next_checkup_weight ? pred.next_checkup_weight + ' kg' : '—');

                    // Recommendations list
                    var recHtml = '';
                    if (pred.recommendations && pred.recommendations.length) {
                        pred.recommendations.forEach(function(r) {
                            recHtml += '<li>' + r + '</li>';
                        });
                    } else {
                        recHtml = '<li class="text-muted">None</li>';
                    }
                    $('#pred_recommendations').html(recHtml);

                    // Concerns list
                    var concHtml = '';
                    if (pred.concerns && pred.concerns.length) {
                        pred.concerns.forEach(function(c) {
                            concHtml += '<li class="text-danger">' + c + '</li>';
                        });
                    } else {
                        concHtml = '<li class="text-success">No concerns</li>';
                    }
                    $('#pred_concerns').html(concHtml);

                    // Milestones
                    $('#pred_milestones').text(pred.milestone_expectations || '—');

                    // View detail link
                    if (record.record_id) {
                        $('#viewDetailLink').attr('href', '{{ url("parent/growth-records/" . $baby->baby_id) }}/' + record.record_id);
                    }

                    $('#aiPredictionResult').removeClass('d-none');
                    form[0].reset();

                    // Reload page after 4 seconds to show new record in table
                    setTimeout(function () { location.reload(); }, 4000);
                } else {
                    $('#formError').removeClass('d-none').text(response.msg || 'An error occurred.');
                }
            },
            error: function (xhr) {
                var msg = 'An unexpected error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    msg = xhr.responseJSON.msg;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errs = xhr.responseJSON.errors;
                    msg = Object.values(errs).flat().join(' ');
                }
                $('#formError').removeClass('d-none').text(msg);
            },
            complete: function () {
                btn.prop('disabled', false);
                $('#saveBtnText').text('Save & Get AI Prediction');
            }
        });
    });

    // Reset modal when closed
    $('#addRecordModal').on('hidden.bs.modal', function () {
        $('#addRecordForm')[0].reset();
        $('#aiPredictionResult').addClass('d-none');
        $('#formError').addClass('d-none');
    });
});
</script>
@endsection
