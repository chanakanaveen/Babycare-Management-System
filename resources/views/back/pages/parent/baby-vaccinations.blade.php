@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Baby Vaccinations')
@section('content')
<div class="col-md-12">
    <div class="pd-20 card-box mb-30">
        <div class="clearfix mb-3">
            <div class="pull-left">
                <h4 class="h4 text-blue">Vaccination Schedule - {{ $baby->full_name }}</h4>
                <p class="text-muted mb-0">Current Age: <strong>{{ $babyAgeMonths }} months</strong></p>
            </div>
            <div class="pull-right mt-2 mt-sm-0">
                <a href="{{ route('parent.baby') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back to Babies</a>
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
                        <th>Administered Date</th>
                        <th>Status</th>
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
                                <span class="badge badge-primary" style="color: white; background-color: #3b82f6; animation: pulse 2s infinite;">Due Now</span>
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
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-danger">No active vaccines configured in the system.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
        // Age filter
        $('.filter-btn').click(function() {
            $('.filter-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('active btn-primary');

            var filterAge = $(this).data('age');

            if (filterAge === 'all') {
                $('.vaccine-row').show();
            } else {
                $('.vaccine-row').each(function() {
                    if ($(this).data('target-age') == filterAge) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
    });
</script>
@endsection
