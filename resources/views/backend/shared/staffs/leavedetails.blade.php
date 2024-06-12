@extends('backend.layouts.master')

@section('content')
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                <h2>{{ $page_title }}</h2>
            </div>
            @include('backend.shared.staffs.partials.action')
        </div>
        <div class="card">
            <div class="card-body">
                <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-12">
                            <div class="box box-info" style="padding:5px;">
                                <div class="box-header with-border">
                                    <div class="pull-right box-tools">
                                        <a class="btn btn-primary btn-sm"></a>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <form action="{{ route('admin.staffs.leavedetails.store') }}" id="employeeform" name="employeeform" method="post" enctype="multipart/form-data">
                                        <div class="hr-line-dashed"></div>
                                        <h5>Leave Detail:</h5>
                                        <div class="hr-line-dashed"></div>
                                        <div class="col-md-12 col-lg-12 d-flex flex-wrap justify-content-between gap-1">
                                            <div class="col-lg-3 col-sm-3">
                                                <label for="medical_leave">Medical Leave</label>
                                                <input type="text" name="medical_leave" value="{{ old('medical_leave') }}" class="form-control" id="medical_leave" placeholder="Enter medical_leave" required>
                                                @error('medical_leave')
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                @enderror
                                            </div>
                                            <div class="form-group col-lg-4 col-sm-4">
                                                <label for="casual_leave">Casual Leave</label>
                                                <input type="text" name="casual_leave" value="{{ old('casual_leave') }}" class="form-control" id="casual_leave" placeholder="Enter casual_leave" required>
                                                @error('casual_leave')
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection