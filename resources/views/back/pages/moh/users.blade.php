@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Babies')
@section('content')
<div class="col-md-12">
    <div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="h4 text-blue">Baby Details</h4>
            </div>
            {{-- <div class="pull-right">
                <a href="{{ route('moh.users') }}" class="btn btn-primary btn-sm" type="button">
                    <i class="fa fa-plus"></i>
                    Add Category
                </a>
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
                        <th>Birth Complications</th>

                    </tr>
                </thead>
                <tbody class="table-border-bottom-0" id="sortable_categories">
                    @forelse ($client as $row)
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
                            {{ $row->birth_complications }}
                        </td>
                        {{-- <td>
                            <div class="table-actions">
                                <a href="{{ route('moh.manage-categories.edit-category',['id'=>$item->id]) }}" class="text-primary">
                                    <i class="dw dw-edit2"></i>
                                </a>
                                <a href="javascript:;" class="text-danger deleteCategoryBtn" data-id="{{ $item->id }}">
                                    <i class="dw dw-delete-3"></i>
                                </a>
                            </div>
                        </td> --}}
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
@endsection
