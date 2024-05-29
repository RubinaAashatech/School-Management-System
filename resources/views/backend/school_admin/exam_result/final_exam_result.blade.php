@extends('backend.layouts.master')

@section('content')

    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                <h2>{{ $page_title }}</h2>
            </div>
            @include('backend.school_admin.exam_result.partials.action')
        </div>

        <div id="studentContainer">
            <div class="card mt-2">
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
                                                    <th>Admission No</th>
                                                    <th>Roll No</th>
                                                    <th>Student Name</th>
                                                    <th>Father Name</th>
                                                    <th>Class</th>
                                                    <th>Section</th>
                                                    @foreach ($examinations->subjectByRoutine as $routine)
                                                        <th colspan="5">{{ $routine->subject }}</th>
                                                    @endforeach
                                                    <th>GPA</th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <!-- Headers for practical, theory, grade point, and total for each subject -->
                                                    @foreach ($examinations->subjectByRoutine as $routine)
                                                        <th>Internal (IN)</th>
                                                        <th>Theory (TH)</th>
                                                        <th>Total</th>
                                                        <th>Grade Point</th>
                                                        <th>Grade</th>
                                                    @endforeach
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="studentTableBody">
                                                @foreach ($studentSessions as $studentSession)
                                                    <tr>
                                                        <td>{{ $studentSession->admission_no }}</td>
                                                        <td>{{ $studentSession->roll_no }}</td>
                                                        <td>{{ $studentSession->f_name . ' ' . $studentSession->m_name . ' ' . $studentSession->l_name }}
                                                        </td>
                                                        <td>{{ $studentSession->father_name }}</td>

                                                        <td>{{ $studentSession->class }}</td>
                                                        <td>{{ $studentSession->section_name }}</td>
                                                        @foreach ($examinations->subjectByRoutine as $routine)
                                                            @php
                                                                $resultData = $studentSession->SubjectWiseExamResults(
                                                                    $examinations,
                                                                    $routine->id,
                                                                    $studentSession,
                                                                );
                                                                // dd($resultData);
                                                            @endphp
                                                            {{-- <td>{{ $resultData['examResult'] ? $resultData['examResult']->participant_assessment : '-' }}
                                                            </td> --}}
                                                            <td>{{ $resultData['examResult'] ? $resultData['examResult']->internal_total : '-' }}
                                                            </td>
                                                            <td>{{ $resultData['examResult'] ? $resultData['examResult']->theory_assessment : '-' }}
                                                            </td>
                                                            <td>{{ $resultData['examResult'] ? $resultData['examResult']->total_terminal_final_marks : '-' }}
                                                            </td>
                                                            <td>{{ $resultData['grade'] ? $resultData['grade']->grade_points_to : '-' }}
                                                            </td>
                                                            <td>{{ $resultData['grade'] ? $resultData['grade']->grade_name : '-' }}
                                                            </td>
                                                        @endforeach
                                                        <td>{{ $studentSession->GPACalculation() }}</td>
                                                    </tr>
                                                @endforeach
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
                // Attach change event handler to the class dropdown
                $('select[name="class_id"]').change(function() {
                    // Get the selected class ID
                    var classId = $(this).val();
                    // Fetch sections based on the selected class ID
                    $.ajax({
                        url: 'get-section-by-class/' +
                            classId, // Replace with the actual route
                        type: 'GET',
                        success: function(data) {
                            // Clear existing options
                            $('select[name="section_id"]').empty();

                            // Add the default option
                            $('select[name="section_id"]').append(
                                '<option disabled selected>Select Section</option>');

                            // Add new options based on the fetched sections
                            $.each(data, function(key, value) {
                                $('select[name="section_id"]').append('<option value="' +
                                    key + '">' + value + '</option>');
                            });
                        }
                    });
                });

                // Initially hide the Save Attendance button
                $('#generateResultButton').hide();

                $('#searchButton').click(function() {
                    // Get the selected class ID and section ID
                    var classId = $('select[name="class_id"]').val();
                    var sectionId = $('select[name="section_id"]').val();
                    var date = $('#nepali-datepicker').val();
                    var attendance_types = 'attendance_types';

                    // Fetch students based on the selected class and section
                    $.ajax({
                        url: 'get-students-by-section/' + classId + '/' + sectionId + '/' + date,
                        type: 'GET',
                        success: function(data) {
                            // console.log("Data received:", data);
                            // Clear existing content in the student container
                            $('#studentTableBody').empty();

                            // Check if there are any students
                            if (data.original && data.original.length > 0) {
                                // Append new rows based on the fetched students
                                $.each(data.original, function(index, studentData) {
                                    // console.log(data.original)
                                    var student = studentData
                                        .student; // Extract student data
                                    var user = studentData.user; // Extract user data
                                    // console.log(user);
                                    var row = '<tr data-student-id="' + student.id +
                                        '">' +
                                        '<td>' + student.admission_no + '</td>' +
                                        '<td>' + student.roll_no + '</td>' +
                                        '<td>' + (user ? user.f_name : '') + '</td>' +
                                        // Access f_name through user property
                                        '<td>';
                                    // Check if attendance_types is defined
                                    if (typeof attendance_types !== 'undefined') {
                                        // Append radio buttons for each attendance type
                                        $.each(attendance_types, function(i,
                                            attendance_type) {
                                            var isChecked = student
                                                .attendance_type_id ==
                                                attendance_type.id || (student
                                                    .attendance_type_id ===
                                                    undefined && attendance_type
                                                    .id == 1);
                                            row += '<label for="attendance_type_' +
                                                student.id + '_' + attendance_type
                                                .id +
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
                                        $('#generateResultButton').show();



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
                                // Hide the Save Attendance button
                                $('#generateResultButton').hide();
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


                // Attach click event handler to the Save Attendance button
                $('#generateResultButton').click(function() {
                    // Get the selected class ID, section ID, and date
                    var classId = $('select[name="class_id"]').val();
                    var sectionId = $('select[name="section_id"]').val();
                    var date = $('#nepali-datepicker').val();
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
                });


            });
        </script>
    @endsection
@endsection
