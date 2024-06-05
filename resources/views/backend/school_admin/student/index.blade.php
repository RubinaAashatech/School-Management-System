@extends('backend.layouts.master')

<!-- Main content -->
@section('content')
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">

            <div class="border-bottom border-primary">
                <h2>
                    {{ $page_title }}
                </h2>
            </div>
            @include('backend.school_admin.student.partials.action')
        </div>
        <div class="card mb-2">
            <div class="card-body">
                <form id="filterForm">
                    @csrf
                    <div class="d-flex justify-content-between">
                        <div class=" col-lg-3 col-sm-3 mt-2 ">
                            <label for="class_id"> Class:</label>
                            <div class="select">
                                <select name="class_id">
                                    <option value="">Select Class</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('class_id')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror
                        </div>

                        <div class=" col-lg-3 col-sm-3 mt-2">
                            <label for="section_id"> Section:</label>
                            <div class="select">
                                <select name="section_id">
                                    <option disabled>Select Section</option>
                                    <option value=""></option>
                                </select>
                            </div>
                            @error('section_id')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                    </div>
                    <!-- Add the Search button -->
                    <div class="form-group col-md-12 d-flex justify-content-end pt-2">
                        <button type="button" class="btn btn-primary" id="searchButton">Search</button>
                    </div>

                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-12">
                            <div class="report-table-container">

                                <div class="table-responsive">
                                    <table id="student-table"
                                        class="table table-bordered table-striped dataTable dtr-inline"
                                        aria-describedby="example1_info">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Class</th>
                                                <th>Roll No</th>
                                                <th>Father Name</th>
                                                <th>Mother Name</th>
                                                <th>Guardian Is</th>
                                                <th>Status</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var dataTable = $('#student-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ url('admin/student/get') }}',
                        type: 'post',
                        data: function(d) {
                            d.class_id = $('select[name="class_id"]').val();
                            d.section_id = $('select[name="section_id"]').val();
                        }
                    },
                    columns: [{
                        data: 'id',
                        name: 'id'
                    }, {
                        data: 'f_name',
                        name: 'f_name'
                    }, {
                        data: 'l_name',
                        name: 'l_name'
                    }, {
                        data: 'class',
                        name: 'class'
                    }, {
                        data: 'roll_no',
                        name: 'roll_no',
                        orderable: true
                    }, {
                        data: 'father_name',
                        name: 'father_name'
                    }, {
                        data: 'mother_name',
                        name: 'mother_name'
                    }, {
                        data: 'guardian_is',
                        name: 'guardian_is'
                    }, {
                        data: 'status',
                        name: 'status'
                    }, {
                        data: 'created_at',
                        name: 'created_at'
                    }, {
                        data: 'actions',
                        name: 'actions'
                    }],

                    initComplete: function() {
                        this.api().columns().every(function() {
                            var column = this;
                            var input = document.createElement("input");
                            $(input).appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    column.search($(this).val()).draw();
                                });
                        });
                    }
                });
                // SEARCH BUTTON CLICK EVENT
                $('#searchButton').on('click', function() {
                    dataTable.order([
                        [4, 'asc']
                    ]).ajax.reload();
                });

            });
        </script>

        {{-- GETTING CLASS AND SECTION --}}
        <script>
            $('select[name="class_id"]').change(function() {
                // Get the selected class ID
                var classId = $(this).val();

                console.log('Selected Class ID:', classId);

                // Fetch sections based on the selected class ID
                $.ajax({
                    url: 'get-section-by-class/' + classId,
                    type: 'GET',
                    success: function(data) {
                        console.log('Sections Data:', data);

                        // Clear existing options
                        $('select[name="section_id"]').empty();

                        // Add the default option
                        $('select[name="section_id"]').append('<option disabled>Select Section</option>');

                        // Add new options based on the fetched sections
                        $.each(data, function(key, value) {
                            $('select[name="section_id"]').append('<option value="' + key + '">' +
                                value + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching sections:', error);
                    }
                });
            });
        </script>
    @endsection
