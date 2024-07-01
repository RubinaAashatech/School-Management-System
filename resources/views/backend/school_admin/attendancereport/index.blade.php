@extends('backend.layouts.master')
@section('content')
<div class="container">
    <h1>Student Attendance Report</h1>
    <div class="row">
        <div class=" col-lg-3 col-sm-3 mt-2">
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
        <div class="col-lg-3 col-sm-3 mt-2">
            <div class="p-2 label-input">
                <label for="from_date">From Date:</label>
                <input type="text" id="from_date" name="from_date" class="form-control nepali-datepicker">
                @error('from_date')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
        </div>
        <div class="col-lg-3 col-sm-3 mt-2">
            <div class="p-2 label-input">
                <label for="to_date">To Date:</label>
                <input type="text" id="to_date" name="to_date" class="form-control nepali-datepicker">
                @error('to_date')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
        </div>
        <div class="col-lg-3 col-sm-3 mt-2">
            <div class="p-2 label-input">
                <label for="student_name">Student Name:</label>
                <input type="text" id="student_name" name="student_name" class="form-control">
                @error('student_name')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
        </div>
        <div class="col-lg-3 col-sm-3 mt-2">
            <div class="p-2 label-input">
                <label for="admission_no">Admission No:</label>
                <input type="text" id="admission_no" name="admission_no" class="form-control">
                @error('admission_no')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
        </div>
        <div class="col-lg-3 col-sm-3 mt-5">
            <div class="search-button-container d-flex align-items-end">
                <button id="searchButton" class="btn btn-sm btn-primary">Search</button>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 mt-2 d-flex justify-content-end">
            <div id="buttons-container" class="d-flex align-items-center"></div>
        </div>
    </div>
    <table id="attendanceTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Attendance Type</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be populated by DataTables -->
        </tbody>
    </table>
</div>

<!-- DataTables and Buttons extension CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">

<!-- jQuery and DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Buttons extension JS -->
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js"></script>
<script src="http://nepalidatepicker.sajanmaharjan.com.np/nepali.datepicker/js/nepali.datepicker.v4.0.4.min.js"></script>

<style>
    #buttons-container {
        display: flex;
        align-items: center;
    }
    #buttons-container .dt-buttons {
        display: flex;
        flex-direction: row;
    }
    #buttons-container .dt-buttons button {
        margin-right: 5px;
    }
    .dataTables_wrapper .dataTables_filter {
        float: left;
        text-align: right;
    }
</style>

<script type="text/javascript">
$(document).ready(function() {
    // Initialize nepali-datepicker for all date inputs
    $('.nepali-datepicker').nepaliDatePicker({
        dateFormat: 'YYYY-MM-DD',
        closeOnDateSelect: true
    });

    // Attach change event handler to the class dropdown
    // Attach change event handler to the class dropdown
    $('select[name="class_id"]').change(function() {
        // Get the selected class ID
        var classId = $(this).val();
        
        // Fetch sections based on the selected class ID
        $.ajax({
            url: '{{ route("admin.get.sections.by.class", ":classId") }}'.replace(':classId', classId),
            type: 'GET',
            success: function(data) {
                // Clear existing options
                $('select[name="section_id"]').empty();

                // Add the default option
                $('select[name="section_id"]').append('<option value="">Select Section</option>');

                // Check if data is empty or not
                if ($.isEmptyObject(data)) {
                    console.error('No sections found for class ID:', classId);
                } else {
                    // Add new options based on the fetched sections
                    $.each(data, function(key, value) {
                        $('select[name="section_id"]').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching sections:', error);
            }
        });
    });



    // Initialize DataTable with Buttons extension but without data
    var table = $('#attendanceTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: '{{ route("admin.attendance_reports.data") }}',
            data: function (d) {
                d.date = $('#nepali-datepicker').val();
                d.class = $('#classSelect').val();
                d.section = $('#sectionSelect').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.student_name = $('#student_name').val();
                d.admission_no = $('#admission_no').val();
            }
        },
        columns: [
            { data: 'student_name', name: 'student_name' },
            { data: 'attendance_type', name: 'attendance_type' }
        ],
        dom: '<"d-flex justify-content-between"lfB>rtip',
        buttons: {
            dom: {
                button: {
                    className: 'btn btn-sm btn-primary'
                }
            },
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            container: '#buttons-container'
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        ordering: false,
        language: {
            emptyTable: "No matching records found"
        },
        drawCallback: function(settings) {
            var api = this.api();
            if (api.rows({page: 'current'}).count() === 0) {
                $(api.table().container()).find('.dataTables_paginate').hide();
            } else {
                $(api.table().container()).find('.dataTables_paginate').show();
            }
        },
        initComplete: function(settings, json) {
            var api = this.api();
            if (api.rows({page: 'current'}).count() === 0) {
                $(api.table().container()).find('.dataTables_paginate').hide();
            } else {
                $(api.table().container()).find('.dataTables_paginate').show();
            }
        }
    });

    // Redraw the table when the search button is clicked
    $('#searchButton').on('click', function() {
        table.draw();
    });
    
});



</script>
@endsection

