@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Baby Vaccinations')
@section('content')
<div class="col-md-12">
    <div class="pd-20 card-box mb-30">
        <div class="clearfix mb-3">
            <div class="pull-left">
                <h4 class="h4 text-blue">Vaccinations - {{ $baby->full_name }}</h4>
                <p class="text-muted mb-0">Current Age: <strong>{{ $babyAgeMonths }} months</strong></p>
            </div>
            <div class="pull-right mt-2 mt-sm-0">
                <a href="{{ route('midwife.baby') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back to Babys</a>
                <button class="btn btn-primary btn-sm" id="addVaccinationBtn" data-bs-toggle="modal">
                    <i class="fa fa-plus"></i> Manual Schedule
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if(session('fail'))
            <div class="alert alert-danger mt-3">{{ session('fail') }}</div>
        @endif

        {{-- Age Filter Bar --}}
        <div class="mb-4">
            <span class="mr-2 font-weight-bold">Filter by Target Age:</span>
            <div class="btn-group flex-wrap" role="group" id="ageFilterGroup">
                <button type="button" class="btn btn-outline-primary btn-sm active filter-btn" data-age="all">All</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-age="0">0m</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-age="2">2m</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-age="4">4m</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-age="6">6m</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-age="9">9m</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-age="12">12m</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-age="18">18m</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn" data-age="24">24m</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless table-striped" id="vaccineTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Vaccine</th>
                        <th>Dose</th>
                        <th>Target Age</th>
                        <th>Scheduled Date</th>
                        <th>Administered</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vaccinations as $v)
                    @php
                        $rec = $v->existing_record;
                        $bgRow = '';
                        if($v->calculated_status == 'not_yet') $bgRow = 'opacity: 0.6;';
                    @endphp
                    <tr class="vaccine-row" data-target-age="{{ $v->target_age_months }}" style="{{ $bgRow }}">
                        <td><strong>{{ $v->vaccine->vaccine_name ?? 'N/A' }}</strong></td>
                        <td>Dose {{ $v->dose_number }} of {{ $v->vaccine->doses_required }}</td>
                        <td>{{ $v->target_age_months }} months</td>
                        <td>{{ $rec && $rec->scheduled_date ? $rec->scheduled_date->format('Y-m-d') : '-' }}</td>
                        <td>{{ $rec && $rec->administered_date ? $rec->administered_date->format('Y-m-d') : '-' }}</td>
                        <td>
                            @if($v->calculated_status == 'overdue')
                                <span class="badge badge-danger" style="color: white; background-color: #ef4444;">Overdue</span>
                            @elseif($v->calculated_status == 'due_now')
                                <span class="badge badge-primary spinner-grow-sm" style="color: white; background-color: #3b82f6; animation: pulse 2s infinite;">Due Now</span>
                            @elseif($v->calculated_status == 'upcoming')
                                <span class="badge badge-info" style="color: white; background-color: #0ea5e9;">Upcoming</span>
                            @elseif($v->calculated_status == 'scheduled')
                                <span class="badge badge-warning" style="color: white; background-color: #f59e0b;">Scheduled</span>
                            @elseif($v->calculated_status == 'administered')
                                <span class="badge badge-success" style="color: white; background-color: #10b981;">Administered</span>
                            @elseif($v->calculated_status == 'not_yet')
                                <span class="badge" style="color: #6b7280; background-color: #e5e7eb;">Not Yet</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($v->calculated_status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($rec)
                                <button type="button" class="btn btn-info btn-sm update-vaccination" 
                                    data-id="{{ $rec->record_id }}"
                                    data-status="{{ $rec->vaccination_status }}"
                                    data-administered="{{ $rec->administered_date ? $rec->administered_date->format('Y-m-d') : '' }}"
                                    data-notes="{{ $rec->notes }}"
                                    data-batch="{{ $rec->batch_number }}">
                                    Update Status
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-primary btn-sm quick-schedule"
                                    data-vaccine-id="{{ $v->vaccine->vaccine_id }}"
                                    data-dose="{{ $v->dose_number }}">
                                    Schedule
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-danger">No active vaccines configured in the system.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Vaccination Modal -->
<div class="modal fade" id="addVaccinationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('midwife.baby-vaccination.schedule') }}" method="POST">
                @csrf
                <input type="hidden" name="baby_id" value="{{ $baby->baby_id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Vaccination</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Vaccine</label>
                        <select name="vaccine_id" id="s_vaccine" class="form-control" required>
                            <option value="">Select Vaccine</option>
                            @foreach($vaccines as $vaccine)
                                <option value="{{ $vaccine->vaccine_id }}">{{ $vaccine->vaccine_name }} (Doses: {{ $vaccine->doses_required }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Dose Number</label>
                        <input type="number" name="dose_number" id="s_dose" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label>Scheduled Date</label>
                        <input type="date" name="scheduled_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Vaccination Modal -->
<div class="modal fade" id="updateVaccinationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateVaccinationForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Vaccination Status</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="vaccination_status" id="u_status" class="form-control" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="administered">Administered</option>
                            <option value="missed">Missed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Administered Date</label>
                        <input type="date" name="administered_date" id="u_administered" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Batch Number</label>
                        <input type="text" name="batch_number" id="u_batch" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea name="notes" id="u_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>

@endsection

@section('myscript')
<script>
    $(document).ready(function() {
        // Manual Add
        $('#addVaccinationBtn').click(function() {
            $('#s_vaccine').val("");
            $('#s_dose').val(1);
            $('#addVaccinationModal').modal('show');
        });

        // Quick Schedule Fill
        $('.quick-schedule').click(function() {
            var vId = $(this).data('vaccine-id');
            var dose = $(this).data('dose');
            $('#s_vaccine').val(vId);
            $('#s_dose').val(dose);
            $('#addVaccinationModal').modal('show');
        });

        // Update Modal Fill
        $('.update-vaccination').click(function() {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var administered = $(this).data('administered');
            var notes = $(this).data('notes');
            var batch = $(this).data('batch');

            // Set action URL
            var url = '{{ route("midwife.baby-vaccination.update", ":id") }}';
            url = url.replace(':id', id);
            $('#updateVaccinationForm').attr('action', url);

            // Populate form
            $('#u_status').val(status);
            $('#u_administered').val(administered);
            $('#u_notes').val(notes);
            $('#u_batch').val(batch);

            $('#updateVaccinationModal').modal('show');
        });

        // JS Front-end Filter Logic
        $('.filter-btn').click(function() {
            // Manage Active Class
            $('.filter-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('active btn-primary');

            var filterAge = $(this).data('age');
            
            if (filterAge === 'all') {
                $('.vaccine-row').show();
            } else {
                var selectedAge = parseInt(filterAge);
                $('.vaccine-row').each(function() {
                    var rowAge = parseInt($(this).data('target-age'));
                    
                    if (rowAge === selectedAge) {
                        $(this).show();
                    } else if (rowAge >= selectedAge - 1 && rowAge <= selectedAge + 1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });

        // Trigger 'Add Manual' when URL contains the query param 
        @if(request('action') == 'add_manual')
            $('#addVaccinationBtn').click();
        @endif
    });
</script>
@endsection
