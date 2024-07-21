@extends('backend.layouts.master')

@section('content')
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                <h2>{{ $page_title }}</h2>
            </div>
            @include('backend.school_admin.staff_attendance.partials.action')
        </div>

        <div class="modal fade" id="holidayRangeModal" tabindex="-1" role="dialog" aria-labelledby="holidayRangeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="holidayRangeModalLabel">Mark Holiday Range</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="holidayStartDate">Start Date:</label>
                            <input type="text" class="form-control" id="holidayStartDate">
                        </div>
                        <div class="form-group">
                            <label for="holidayEndDate">End Date:</label>
                            <input type="text" class="form-control" id="holidayEndDate">
                        </div>
                        <div class="form-group">
                            <label for="holidayReason">Reason:</label>
                            <input type="text" class="form-control" id="holidayReason" placeholder="e.g., Summer Vacation, Dashain Vacation">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveHolidayRange">Save Holiday Range</button>
                    </div>
                </div>
            </div>
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
                        <div class="col-lg-3 col-sm-3 mt-2">
                            <label for="datetimepicker">Date:</label>
                            <div class="form-group">
                                <div class="input-group date" id="admission-datetimepicker" data-target-input="nearest">
                                    <input id="admission-datepicker" name="date" type="text" class="form-control datetimepicker-input" />
                                </div>
                                @error('date')
                                <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>
                       
                        <script>
                            $(document).ready(function () {
                                // Fetch current Nepali date
                                var currentDate = NepaliFunctions.GetCurrentBsDate();
                    
                                // Pad month and day with leading zero if they are less than 10
                                var padZero = function (num) {
                                    return num < 10 ? '0' + num : num;
                                };
                    
                                // Format the current date with padded month and day
                                var formattedDate = currentDate.year + '-' + padZero(currentDate.month) + '-' + padZero(currentDate.day);
                    
                                // Set the formatted date to the input field
                                $('#admission-datepicker').val(formattedDate);
                            });
                        </script>
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
                            <button type="button" class="btn btn-primary" id="saveAttendanceButton">Save Attendance</button>
                            <button type="button" class="btn btn-primary" id="markHolidayButton" style="margin-left: 5px;">Mark Holiday</button>
                            <button type="button" class="btn btn-primary" id="exportDataButton" style="margin-left: 5px;">Export Data</button>
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
                                                <th>SN</th>
                                                <th>Staff Name</th>
                                                <th>Attendance</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="staffTableBody">
                                           
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
  

@section('scripts')
    @include('backend.includes.nepalidate')

    <script>
        $(document).ready(function() {
            var attendance_types = @json($attendance_types);
            getStaffDetails(attendance_types);
            $('#searchButton').click(function() {
                var formData = $('#filterForm').serialize();
                var role = $('select[name="role"]').val();
                var date = $('#admission-datepicker').val();

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
                                    '<td>' + (index + 1) + '</td>' + 
                                    '<td>' + staff.f_name + ' ' + staff.l_name + '</td>'+
                                    '<td>';
                                if (typeof attendance_types !== 'undefined') {
                                    $.each(attendance_types, function(i,
                                        attendance_type) {
                                            var isChecked = staff.attendance_type_id == attendance_type.id || (staff.attendance_type_id === undefined && attendance_type.id == 1);
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
                                            (isChecked ? 'checked' : '') +
                                '> ' +
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

             // Mark holiday button click event

             $('#markHolidayButton').click(function() {

// Send an AJAX request to mark holiday

                        $.ajax({
                        url: '',
                        type: 'POST',
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {

                        // Check all holiday radio buttons
                        $('input[type="radio"][value="4"]').prop('checked', true);

                        },
                        error: function(xhr, status, error) {

                        // Handle the error response
                        console.error('Error marking holiday:', error);
                        alert('Error marking holiday. Please try again.');
                        }
                        });
                        });



            $('#saveAttendanceButton').click(function() {
                var role = $('select[name="role"]').val();
                var date = $('#admission-datepicker').val();
                var attendanceData = [];
                $('#staffTable tbody tr').each(function() {
                    var staffId = $(this).find('td:eq(0)').text();
                    var attendanceTypeId = $('input[name="attendance_type_id[' + staffId + ']"]:checked').val();
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
                        toastr.error('Error occurred while saving attendance. Please try again later.');
                    }
                });
            });

            $('#exportDataButton').click(function() {
                var data = [];
                // Add headers
                data.push(["Staff Id", "Staff Name", "Attendance", "Remarks"]);
                
                // Add table rows
                $('#staffTable tbody tr').each(function() {
                    var row = [];
                    row.push($(this).find('td:eq(0)').text()); // Staff Id
                    row.push($(this).find('td:eq(1)').text()); // Staff Name
                    
                    // Attendance
                    var attendanceType = $(this).find('input[type="radio"]:checked').next('span').text();
                    row.push(attendanceType);
                    
                    // Remarks
                    row.push($(this).find('input[name^="remarks"]').val());
                    
                    data.push(row);
                });

                // Convert data array to CSV string
                let csvContent = "data:text/csv;charset=utf-8,";
                data.forEach(rowArray => {
                    let row = rowArray.join(",");
                    csvContent += row + "\r\n";
                });

                // Create a link element
                const link = document.createElement("a");
                link.setAttribute("href", encodeURI(csvContent));
                link.setAttribute("download", "attendance_data.csv");

                // Append the link element to the body and trigger the download
                document.body.appendChild(link);
                link.click();

                // Remove the link element from the document
                document.body.removeChild(link);
            });


               // Initialize date pickers for the holiday range modal
    $('#holidayStartDate, #holidayEndDate').nepaliDatePicker()
    $("#holidayStartDate").nepaliDatePicker({
    container: "#holidayRangeModal",
    dateFormat: "YYYY-MM-DD",
    ndpYear: true,
    ndpMonth: true,
    ndpYearCount: 200,
    onChange: function() {
        $(this).change();
    }
});

$("#holidayEndDate").nepaliDatePicker({
    container: "#holidayRangeModal",
    dateFormat: "YYYY-MM-DD",
    ndpYear: true,
    ndpMonth: true,
    ndpYearCount: 200,
    onChange: function() {
        $(this).change();
    }
});

// Open the holiday range modal
$('#markHolidayRangeButton').click(function() {
    $('#holidayRangeModal').modal('show');
});

// Handle saving the holiday range
$('#saveHolidayRange').click(function() {
    var startDate = $('#holidayStartDate').val();
    var endDate = $('#holidayEndDate').val();
    var reason = $('#holidayReason').val();

    if (!startDate || !endDate) {
        toastr.warning('Please select both start and end dates.');
        return;
    }

    if (confirm('Are you sure you want to mark holidays from ' + startDate + ' to ' + endDate + '?')) {
        $.ajax({
            url: '{{ route("admin.staff.mark-holiday-range") }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { 
                start_date: startDate, 
                end_date: endDate,
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#holidayRangeModal').modal('hide');
                    // Optionally, update UI or refresh data
                } else {
                    toastr.error(response.message || 'Error marking holiday range.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error details:', xhr.responseText);
                toastr.error('Error marking holiday range. Please check the console for details.');
            }
        });
    }
});
        });
    </script>
@endsection
@endsection


