@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Babies')
@section('content')
<div class="col-md-12">
    <div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="h4 text-blue">My Baby Details</h4>
            </div>
            {{-- <div class="pull-right">
                <button class="btn btn-primary btn-sm" id="addbaby" name="addbaby" type="button" data-bs-toggle="modal" >
                    <i class="fa fa-plus"></i>
                    Add Baby
                </button>
            </div> --}}
        </div>
        <div class="table-responsive mt-4">
            <table class="table table-borderless table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Blood Group</th>
                        <th>Birth Hospital</th>
                        <th>Birth Weight</th>
                        <th>Parent's Name</th>
                        <th>BMI</th>
                        <th>Growth Records</th>

                    </tr>
                </thead>
                <tbody class="table-border-bottom-0" id="sortable_categories">
                    @forelse ($babys as $row)
                    <tr data-index="" data-ordering="">
                        <td>
                            {{ $row->full_name }}
                        </td>
                        <td>
                            {{ $row->date_of_birth }}
                        </td>
                        <td>
                            {{ $row->gender }}
                        </td>
                        <td>
                            {{ $row->blood_group }}
                        </td>
                        <td>
                            {{ $row->birth_hospital }}
                        </td>
                        <td>
                            {{ $row->birth_weight }}
                        </td>
                        <td>
                            {{ $row->parentname }}
                        </td>
                        <td>
                            {{ $row->bmi }}
                        </td>
                        <td>
                            <a href="{{ route('parent.growth-record.index', $row->baby_id) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-robot"></i> Growth Records
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <span class="text-danger">No users found!</span>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        <div class="d-block mt-2">
            {{-- {{ $categories->links('livewire::simple-bootstrap') }} --}}
        </div>
    </div>
</div>

{{-- baby new modal --}}
<div class="modal fade" id="addBabyModal" tabindex="-1" aria-labelledby="addBabyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addBabyForm" method="POST" action="{{ route('midwife.baby-store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addBabyModalLabel">Add Baby Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="blood_group" class="form-label">Blood Group</label>
                        <input type="text" class="form-control" id="blood_group" name="blood_group" required>
                    </div>
                    <div class="mb-3">
                        <label for="birth_hospital" class="form-label">Birth Hospital</label>
                        <input type="text" class="form-control" id="birth_hospital" name="birth_hospital" required>
                    </div>
                    <div class="mb-3">
                        <label for="birth_weight" class="form-label">Birth Weight</label>
                        <input type="text" class="form-control" id="birth_weight" name="birth_weight" required>
                    </div>
                    <div class="mb-3">
                        <label for="parentname" class="form-label">Midwife's Name</label>
                        {{-- <input type="text" class="form-control" id="parentname" name="parentname" required> --}}
                        <select class="custom-select2 form-control select2-hidden-accessible" name="midwife" id="midwife" style="width: 100%; height: 38px" data-select2-id="1" tabindex="-1" aria-hidden="true">
                            <option value="" selected disabled>Select a Midwife</option>
                            @foreach ($midwife as $row )
                                <option value="{{ $row->id }}" >{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="parentname" class="form-label">Parent's Name</label>
                        {{-- <input type="text" class="form-control" id="parentname" name="parentname" required> --}}
                        <select class="custom-select2 form-control select2-hidden-accessible" name="parentname" id="parentname" style="width: 100%; height: 38px" data-select2-id="1" tabindex="-1" aria-hidden="true">
                            <option value="" selected disabled>Select a Parents</option>
                            @foreach ($parents as $row )
                                <option value="{{ $row->id }}" >{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="birth_complications" class="form-label">Birth Complications</label>
                        <textarea class="form-control" id="birth_complications" name="birth_complications"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Baby</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateBabyModal" tabindex="-1" aria-labelledby="updateBabyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateBabyForm" method="POST" action="{{ route('midwife.weight-record-store') }}">
                @csrf
                <input type="hidden" id="baby_id" name="baby_id"> <!-- Hidden field for baby ID -->
                {{-- <input type='hidden' id='midwife_id' name='midwife_id' value='{{ $midwifedetails->id }}'> --}}
                <div class="modal-header">
                    <h5 class="modal-title" id="updateBabyModalLabel">Update Baby Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="weight" class="form-label">Weight (KG)</label>
                        <input type="text" class="form-control" id="weight" name="weight" required>
                    </div>
                    <div class="mb-3">
                        <label for="height" class="form-label">Height (cm)</label>
                        <input type="text" class="form-control" id="height" name="height" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('myscript')
<script>
    $(document).ready(function() {
        $('#addbaby').click(function() {
            console.log('Add Baby button clicked');
            // Show the modal
            $('#addBabyModal').modal('show');
        });

        // Show the Update Baby Modal
        $('.update-baby').click(function() {
            const babyId = $(this).data('id');
            $('#baby_id').val(babyId); // Set the baby ID in the hidden input
            $('#updateBabyModal').modal('show');
        });
    });
</script>

@endsection
