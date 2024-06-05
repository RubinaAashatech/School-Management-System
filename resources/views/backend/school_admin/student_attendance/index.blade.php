@extends('backend.layouts.master')

@section('content')
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                <h2>{{ $page_title }}</h2>
            </div>
            @include('backend.school_admin.student_attendance.partials.action')
        </div>

        <div class="card">
            <div class="class-body">
        <form id="attendanceFilterForm">
            <div class="col-md-12 col-lg-12 d-flex justify-content-around">
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
                        // Format the current date
                        var formattedDate = currentDate.year + '-' + currentDate.month+ '-' + currentDate.day;
                        // Set the formatted date to the input field
                        $('#admission-datepicker').val(formattedDate);
                    });
                </script>
            </div>

            <!-- Add the Search button -->
            <div class="form-group col-md-12 d-flex justify-content-end pt-2">
                <button type="button" class="btn btn-primary" id="searchButton">Search</button>
            </div>
        </form>
    </div>
</div>

<div id="studentContainer">
    <div class="card mt-2">
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <!-- Save Attendance and Mark Holiday button -->
                <div class="row mb-2">
                    <div class="col-sm-12 col-md-12 col-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" id="saveAttendanceButton">Save Attendance</button>
                        <button type="button" class="btn btn-primary" id="markHolidayButton" style="margin-left: 5px;">Mark Holiday</button>
                    </div>
                </div>
                <!-- Search input -->
                <div class="row mb-2">
                    <div class="col-sm-3 col-md-3 col-3 d-flex justify-content-end position-relative">
                        <div style="position: relative;">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search">
                            <span id="clearSearchInput" class="position-absolute top-50 end-0 translate-middle-y text-muted" style="cursor: pointer;">&times;</span>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-12">
                        <div class="report-table-container">
                            <div class="table-responsive">
                                <table id="student-table" class="table table-bordered table-striped dataTable dtr-inline"
                                    aria-describedby="example1_info">
                                    <thead>
                                        <tr>
                                            <th>Admission No</th>
                                            <th>Roll No</th>
                                            <th>Name</th>
                                            <th>Attendance</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentTableBody">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Function to fetch students dynamically
        function fetchStudents() {
            $.ajax({
                url: '/admin/student/get', 
                type: 'POST',
                success: function(response) {
                    updateTable(response.students); 
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching student data:', error);
                }
            });
        }

        // Function to update table based on student data
        function updateTable(students) {
            const tableBody = $('#studentTableBody');
            tableBody.empty();

            if (students.length === 0) {
                tableBody.append('<tr><td colspan="5" class="text-center">No results found</td></tr>');
                return;
            }

            students.forEach(student => {
                const row = `<tr>
                    <td>${student.admission_no}</td>
                    <td>${student.roll_no}</td>
                    <td>${student.f_name}</td>
                    <td>${student.attendance_type_id}</td>
                    <td>${student.remarks}</td>
                </tr>`;
                tableBody.append(row);
            });
        }

        fetchStudents();

        $('#searchInput').on('input', function () {
            const query = $(this).val().toLowerCase();
            updateTableBasedOnSearch(query);
        });

        $('#clearSearchInput').on('click', function () {
            $('#searchInput').val('');
            fetchStudents();
        });

        // Function to update table based on search input
        function updateTableBasedOnSearch(query) {
            const tableRows = $('#studentTableBody').find('tr');

            tableRows.each(function() {
                const studentName = $(this).find('td:nth-child(3)').text().toLowerCase();
                if (studentName.includes(query)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
       
    });
</script>


    @section('scripts')
    @include('backend.includes.nepalidate')
    <script>
        $(document).ready(function() {
            // Attach change event handler to the class dropdown
            $('select[name="class_id"]').change(function() {
                // Get the selected class ID
                var classId = $(this).val();
                // Fetch sections based on the selected class ID
                $.ajax({
                    url: 'get-section-by-class/' + classId, // Replace with the actual route
                    type: 'GET',
                    success: function(data) {
                        // Clear existing options
                        $('select[name="section_id"]').empty();
    
                        // Add the default option
                        $('select[name="section_id"]').append('<option value="" selected>Select Section</option>');
    
                        // Add new options based on the fetched sections
                        $.each(data, function(key, value) {
                            $('select[name="section_id"]').append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });
            });

               // Initially hide the Save Attendance and Mark Holiday buttons
               $('#saveAttendanceButton, #markHolidayButton').hide();

               $('#searchButton').click(function() {

              // Get the selected class ID and section ID
              var classId = $('select[name="class_id"]').val();
              var sectionId = $('select[name="section_id"]').val();
              var date = $('#admission-datepicker').val();
              var attendance_types = @json($attendance_types);

            // Fetch students based on the selected class and section
            $.ajax({
            url: 'get-students-by-section/' + classId + '/' + sectionId + '/' + date,
            type: 'GET',
            success: function(data) {
            // Clear existing content in the student container
            $('#studentTableBody').empty();

            // Check if there are any students
            if (data.original && data.original.length > 0) {
                // Append new rows based on the fetched students
                $.each(data.original, function(index, studentData) {
                    var student = studentData.student; // Extract student data
                    var user = studentData.user; // Extract user data
                    var row = '<tr data-student-id="' + student.id + '">' +
                        '<td>' + student.admission_no + '</td>' +
                        '<td>' + student.roll_no + '</td>' +
                        '<td>' + (user ? (user.f_name ? user.f_name + ' ' : '') + (user.m_name ? user.m_name + ' ' : '') + (user.l_name ? user.l_name : '') : '') + '</td>' +
                        '<td>';

                    // Check if attendance_types is defined
                    if (typeof attendance_types !== 'undefined') {
                        // Append radio buttons for each attendance type
                        $.each(attendance_types, function(i, attendance_type) {
                            var isChecked = student.attendance_type_id == attendance_type.id || (student.attendance_type_id === undefined && attendance_type.id == 1);
                            row += '<label for="attendance_type_' +
                                student.id + '_' + attendance_type.id +
                                '" class="attendance-radio">' +
                                '<input type="radio" name="attendance_type_id[' +
                                student.id + ']" value="' +
                                attendance_type.id +
                                '" id="attendance_type_' + student
                                .id + '_' + attendance_type.id +
                                '" ' +
                                (isChecked ? 'checked' : '') +
                                '> ' +
                                '<span>' + attendance_type.type +
                                '</span>' +
                                '</label>';
                        });
                        // Show the Save Attendance button
                        $('#saveAttendanceButton').show();
                        // Show the Mark Holiday button
                        $('#markHolidayButton').show();
                    } else {
                        // Handle the case where attendance_types is not defined
                        row += 'Attendance types not available';
                    }

                    row += '</td>' +
                        '<td><input type="text" name="remarks[' + student
                        .id + ']" value="' +
                        (student.remarks ? student.remarks : '') +
                        '"></td>' +
                        '</tr>';

                    $('#studentTableBody').append(row);
                });

                // Populate existing attendance data in the form
                populateExistingAttendance(data.original);
            } else {
                // If there are no students, display a message or handle accordingly
                $('#studentTableBody').append(
                    '<tr><td colspan="5">No students found for the selected section</td></tr>'
                );
                // Hide the Save Attendance and Mark Holiday buttons
                $('#saveAttendanceButton, #markHolidayButton').hide();
            }
        }
    });
});
        
                // Function to populate existing attendance data in the form
                // Function to populate existing attendance data in the form
                function populateExistingAttendance(students) {
                    $.each(students, function(index, studentData) {
                        var student = studentData.student;
                        var user = studentData.user;

                        // Assuming there is only one attendance record per student for the given date
                        if (studentData.student_attendances && studentData.student_attendances.length > 0) {
                            var attendance = studentData.student_attendances[0];
                            var attendanceTypeId = attendance.attendance_type_id;

                            // Assuming the attendance_type_id and remarks correspond to the existing data
                            $('input[name="attendance_type_id[' + student.id + ']"][value="' +
                                attendanceTypeId + '"]').attr('checked', true);
                            $('input[name="remarks[' + student.id + ']"]').val(attendance.remarks);
                        }
                    });
                }





                // Attach change event handler to the section dropdown
                // $('#searchButton').click(function() {
                //     // Get the selected class ID and section ID
                //     var classId = $('select[name="class_id"]').val();
                //     var sectionId = $('select[name="section_id"]').val();
                //     var date = $('#date').val();
                //     var attendance_types = @json($attendance_types);

                //     // console.log(classId)
                //     // console.log(sectionId)
                //     // console.log(attendance_types)

                //     // Fetch students based on the selected class and section
                //     $.ajax({
                //         url: 'get-students-by-section/' + classId + '/' + sectionId + '/' + date,
                //         type: 'GET',
                //         success: function(data) {
                //             // Clear existing content in the student container
                //             $('#studentTableBody').empty();

                //             // console.log(data)
                //             // console.log(data.length)

                //             // Check if there are any students
                //             if (data.original && data.original.length > 0) {
                //                 // Append new rows based on the fetched students
                //                 $.each(data.original, function(index, student) {
                //                     // Assuming that the student model has corresponding properties
                //                     var row = '<tr data-student-id="' + student.student.id +
                //                         '">' +
                //                         '<td>' + student.student.admission_no + '</td>' +
                //                         '<td>' + student.student.roll_no + '</td>' +
                //                         '<td>' + student.user.f_name + '</td>' +
                //                         '<td>';

                //                     // Check if attendance_types is defined
                //                     if (typeof attendance_types !== 'undefined') {
                //                         // Append radio buttons for each attendance type
                //                         $.each(attendance_types, function(i,
                //                             attendance_type) {
                //                             row += '<label for="attendance_type_' +
                //                                 student.id + '_' + attendance_type
                //                                 .id +
                //                                 '" class="attendance-radio">' +
                //                                 '<input type="radio" name="attendance_type_id[' +
                //                                 student.id + ']" value="' +
                //                                 attendance_type.id +
                //                                 '" id="attendance_type_' + student
                //                                 .id + '_' + attendance_type.id +
                //                                 '" ' +
                //                                 (student.attendance_type_id ==
                //                                     attendance_type.id ? 'checked' :
                //                                     '') + '> ' +
                //                                 '<span>' + attendance_type.type +
                //                                 '</span>' +
                //                                 '</label>';
                //                         });
                //                         // Show the Save Attendance button
                //                         $('#saveAttendanceButton').show();
                //                     } else {
                //                         // Handle the case where attendance_types is not defined
                //                         row += 'Attendance types not available';
                //                     }

                //                     row += '</td>' +
                //                         '<td><input type="text" name="remarks[' + student
                //                         .id + ']" value="' +
                //                         (student.remarks ? student.remarks : '') +
                //                         '"></td>' +
                //                         '</tr>';

                //                     $('#studentTableBody').append(row);
                //                 });
                //             } else {
                //                 // If there are no students, display a message or handle accordingly
                //                 $('#studentTableBody').append(
                //                     '<tr><td colspan="5">No students found for the selected section</td></tr>'
                //                 );
                //                 // Hide the Save Attendance button
                //                 $('#saveAttendanceButton').hide();
                //             }
                //         }
                //     });
                // });

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

                // Handle the success response
                alert('Holiday marked successfully!');

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



                // Attach click event handler to the Save Attendance button
                $('#saveAttendanceButton').click(function() {
                    // Get the selected class ID, section ID, and date
                    var classId = $('select[name="class_id"]').val();
                    var sectionId = $('select[name="section_id"]').val();
                    var date = $('#admission-datepicker').val();
                    // console.log(classId);
                    // console.log(sectionId);
                    // console.log(date);

                    // Prepare an array to store attendance data
                    var attendanceData = [];


                    // Loop through each row in the table
                    $('#studentTableBody tr').each(function() {
                        var studentId = $(this).data('student-id');
                        // console.log(studentId);
                        var attendanceTypeId = $('input[name="attendance_type_id[' + studentId +
                            ']"]:checked').val();
                        var remarks = $('input[name="remarks[' + studentId + ']"]').val();

                        console.log("Attendance Type:" + attendanceTypeId);
                        // console.log(remarks);

                        // Add data to the array
                        attendanceData.push({
                            student_id: studentId,
                            attendance_type_id: attendanceTypeId,
                            date: date,
                            remarks: remarks
                        });
                    });

                    // console.log(attendanceData);

                    // Send an AJAX request to save the attendance data
                    $.ajax({
                        url: 'student-attendances/save-attendance',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: {
                            class_id: classId,
                            section_id: sectionId,
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
