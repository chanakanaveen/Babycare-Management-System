@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Vaccines')
@section('content')
<div class="col-md-12">
    <div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="h4 text-blue">Vaccines Details</h4>
            </div>
            <div class="pull-right">
                {{-- <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm" type="button">
                    <i class="fa fa-plus"></i>
                    Add Vaccine
                </a> --}}
                <!-- Button to trigger modal -->
                <button class="btn btn-primary btn-sm" id="addVaccine" type="button" data-bs-toggle="modal" >
                    <i class="fa fa-plus"></i>
                    Add Vaccine
                </button>
            </div>
        </div>
        <div class="table-responsive mt-4">
            <table class="table table-borderless table-striped">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Recommended Age Months</th>
                        <th>Dosage Required</th>

                    </tr>
                </thead>
                <tbody class="table-border-bottom-0" id="sortable_categories">
                    @forelse ($vaccines as $row)
                    <tr data-index="" data-ordering="">
                        <td>
                            {{ $row->vaccine_name  }}
                        </td>
                        <td>
                            {{ $row->description }}
                        </td>
                        <td>
                            {{ $row->recommended_age_months }}
                        </td>
                        <td>
                            {{ $row->doses_required }}
                        </td>


                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for adding a new vaccine -->
<div class="modal fade" id="addVaccineModal" tabindex="-1" aria-labelledby="addVaccineModalLabel" aria-hidden="fales">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.addVaccine') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addVaccineModalLabel">Add New Vaccine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="vaccine_name" class="form-label">Vaccine Name</label>
                        <input type="text" class="form-control" id="vaccine_name" name="vaccine_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="recommended_age_months" class="form-label">Recommended Age (Months)</label>
                        <input type="number" class="form-control" id="recommended_age_months" name="recommended_age_months" required>
                    </div>
                    <div class="mb-3">
                        <label for="doses_required" class="form-label">Dosage Required</label>
                        <input type="number" class="form-control" id="doses_required" name="doses_required" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Vaccine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" tabindex=-1 role="dialog" name="docmodal" id="docmodal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.addVaccine') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addVaccineModalLabel">Add New Vaccine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="vaccine_name" class="form-label">Vaccine Name</label>
                        <input type="text" class="form-control" id="vaccine_name" name="vaccine_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="recommended_age_months" class="form-label">Recommended Age (Months)</label>
                        <input type="number" class="form-control" id="recommended_age_months" name="recommended_age_months" required>
                    </div>
                    <div class="mb-3">
                        <label for="doses_required" class="form-label">Dosage Required</label>
                        <input type="number" class="form-control" id="doses_required" name="doses_required" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Vaccine</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('myscript')
<script>
 //modal open
 $(document).on("click", "#addVaccine",function(){
    //    var about =  $(this).data("about");
    //    var url = $(this).data("url");
    //    $("#about").text(about);
    //    $("#ceimg").attr("src",url);

       $("#docmodal").modal('show');

    });

</script>

@endsection
