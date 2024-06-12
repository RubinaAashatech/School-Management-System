@extends('backend.layouts.master')
@section('content')
    <!-- Main content -->
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                <h2>
                    {{ $resignation }}
                </h2>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-12">
                        <form action="{{ route('admin.staffs.resignation.store') }}" id="employeeform"
                                        name="employeeform" method="post" enctype="multipart/form-data">
                                        @csrf
                        <div class="box box-info" style="padding:5px;">
                            <div class="box-header with-border">
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="staff_id">Staff ID:</label>
                                    <input type="" name="staff_id" value="{{ old('staff_id') }}" class="form-control" id="staff_id" placeholder="Enter Staff ID" required>
                                    @error('staff_id')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                                <div class="form-group col-lg-4 col-sm-4">
                                    <label for="resignation_letter">Resignation Letter</label>
                                    <input type="text" name="resignation_letter" value="{{ old('resignation_letter') }}" class="form-control" id="resignation_letter" placeholder="Enter resignation_letter" required>
                                    @error('resignation_letter')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                                {{-- <div class="form-group col-lg-3 col-sm-3 mt-2">
                                    <label for="note">Note :</label>
                                    <textarea name="note" class="form-control" id="note" placeholder="Note.." rows="15" cols="50">{{ old('note') }}</textarea>
                                    @error('note')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div> --}}
                                <div class="pull-right box-tools">
                                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection