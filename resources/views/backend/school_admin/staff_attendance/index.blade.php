@extends('backend.layouts.master')

@section('content')
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                <h2>{{ $page_title }}</h2>
            </div>
            @include('backend.school_admin.staff_attendance.partials.action')
        </div>

        <div class="card">
            <div class="card-body">
                <form id="filterForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Select Role:</label>
                                <select name="role" id="role" class="form-control">
                                    <option value="6">Teacher</option>
                                    <option value="7">Accountant</option>
                                    <option value="8">Librarian</option>
                                    <option value="9">Principal</option>
                                    <option value="10">Receptionist</option>
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date:</label>
                                <input type="text" name="date" id="date" class="form-control">
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <label for="datetimepicker">Date:</label>
                            <div class="form-group">
                                <div class="input-group date" id="datetimepicker" data-target-input="nearest">
                                    <input id="nepali-datepicker" name="date" type="text"
                                        class="form-control datetimepicker-input" />
                                </div>
                                @error('date')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>
                    </div>
            </div>
            <div class="row">
                <div class="col-md-12 mt-2 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="searchButton">Search</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <div id="resultContainer">
        <div class="card mt-2">
            <div class="card-body">
                <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row mb-2">
                        <div class="col-sm-12 col-md-12 col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" id="saveAttendanceButton">Save
                                Attendance</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-12">
                            <div class="report-table-container">
                                <div class="table-responsive">
                                    <table id="staffTable" class="table table-bordered table-striped dataTable dtr-inline"
                                        aria-describedby="example1_info">
                                        <thead>
                                            <tr>
                                                <th>Staff Id</th>
                                                <th>Staff Name</th>
                                                <th>Attendance</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="staffTableBody">
                                            <!-- Student data will be dynamically added here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@section('scripts')
    @include('backend.includes.nepalidate')

    <script>
        $(document).ready(function() {
            var attendance_types = @json($attendance_types);
            getStaffDetails(attendance_types);
            $('#searchButton').click(function() {
                var formData = $('#filterForm').serialize();
                var role = $('select[name="role"]').val();
                var date = $('#nepali-datepicker').val();

                getStaffDetails(attendance_types, role, date);
            });

            function getStaffDetails(attendance_types, role = null, date = null) {
                $.ajax({
                    url: '{{ route('admin.get.staff.name') }}',
                    type: 'GET',
                    data: {
                        role: role,
                        date: date,
                    },
                    success: function(data) {
                        $('#staffTableBody').empty();
                        if (data.original && data.original.length > 0) {
                            $.each(data.original, function(index, staffData) {

                                var staff = staffData.staff;
                                var user = staffData.user;
                                var row = '<tr data-staff-id="' + staffData.staff_id +
                                    '">' +
                                    '<td>' + staffData.staff_id + '</td>' +
                                    '<td>' + staff.f_name + '</td>' +
                                    '<td>';
                                if (typeof attendance_types !== 'undefined') {
                                    $.each(attendance_types, function(i,
                                        attendance_type) {
                                        row += '<label for="attendance_type_' +
                                            staffData.staff_id + '_' +
                                            attendance_type
                                            .id +
                                            '" class="attendance-radio l-radio">' +
                                            '<input type="radio" name="attendance_type_id[' +
                                            staffData.staff_id + ']" value="' +
                                            attendance_type.id +
                                            '" id="attendance_type_' + staffData
                                            .staff_id + '_' + attendance_type
                                            .id +
                                            '" ' +
                                            (staff.attendance_type_id ==
                                                attendance_type.id ? 'checked' :
                                                '') + '> ' +
                                            '<span>' + attendance_type.type +
                                            '</span>' +
                                            '</label>';
                                    });
                                    $('#saveAttendanceButton').show();
                                } else {
                                    row += 'Attendance types not available';
                                }


                                row += '</td>' +
                                    '<td><input type="text" name="remarks[' + staffData
                                    .staff_id + ']" value="' +
                                    (staff.remarks ? staff.remarks : '') +
                                    '"></td>' +
                                    '</tr>';

                                $('#staffTableBody').append(row);
                            });
                            populateExistingAttendance(data.original);
                        } else {
                            $('#staffTableBody').append(
                                '<tr><td colspan="4">No staff members found for the selected role and date</td></tr>'
                            );
                            $('#saveAttendanceButton').hide();
                        }
                    },
                    error: function(error) {
                        // Handle error
                    }
                });
            }

            function populateExistingAttendance(staffMembers) {


                var staffTableBody = $('#staffTableBody');

                // Check if staffTableBody is empty


                $.each(staffMembers, function(index, staffData) {
                    // console.log(staffData.staff_attendances);
                    var staff = staffData.staff;

                    if (staffData.staff_attendances && staffData.staff_attendances.length > 0) {
                        $.each(staffData.staff_attendances, function(i, attendance) {
                            var attendanceTypeId = attendance.attendance_type_id;
                            console.log("Attendance Type ID:", attendanceTypeId);
                            console.log("Remarks:", attendance.remarks);
                            $('input[name="attendance_type_id[' + staff.staff_id + ']"][value="' +
                                attendanceTypeId + '"]').prop('checked', true);
                            $('input[name="remarks[' + staff.staff_id + ']"]').val(attendance
                                .remarks);
                        });
                    } else {
                        console.log("No staff attendances found for Staff ID:", staff.staff_id);
                        // If no attendance is found, display the empty staffTableBody
                        staffTableBody.show();
                    }
                });
            }



            // Attach click event handler to the Save Attendance button
            $('#saveAttendanceButton').click(function() {

                var role = $('select[name="role"]').val();
                var date = $('#nepali-datepicker').val();
                var attendanceData = [];
                $('#staffTable tbody tr').each(function() {
                    var staffId = $(this).find('td:eq(0)').text();
                    var attendanceTypeId = $('input[name="attendance_type_id[' + staffId +
                        ']"]:checked').val();
                    var remarks = $('input[name="remarks[' + staffId + ']"]').val();
                    attendanceData.push({
                        staff_id: staffId,
                        attendance_type_id: attendanceTypeId,
                        date: date,
                        remarks: remarks

                    });
                });

                // Send an AJAX request to save the staff attendance data
                $.ajax({

                    url: 'staff-attendances/save-attendance',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: {
                        role: role,
                        attendance_data: attendanceData
                    },
                    success: function(response) {
                        // Handle the response (e.g., show a success message)
                        if (response.message) {
                            // If there's a success message in the response, display it
                            toastr.success(response.message);
                        } else {
                            // If no success message is provided, display a default success message
                            toastr.success('Attendance saved successfully');
                        }
                    },
                    error: function(error) {
                        // Handle errors (e.g., show an error message)
                        console.error(error);
                        toastr.error(
                            'Error occurred while saving attendance. Please try again later.'
                        );
                    }
                });
            });
        });
    </script>
@endsection
@endsection
