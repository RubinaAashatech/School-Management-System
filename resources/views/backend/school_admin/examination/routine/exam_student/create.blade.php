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

        </div>
        <div class="card mb-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="row">
                            <div class="col-lg-3 col-sm-3">
                                <label for="class_id">Class:</label>
                                <input type="text" class="form-control" value="{{ $className }}" disabled>
                            </div>

                            <div class="col-lg-3 col-md-3">
                                <label for="section_id">Section:</label>
                                <input type="text" class="form-control" value="{{ $sectionName }}" disabled>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-2 ajaxHide" style="display: none">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="scroll-area-fullheight">
                            <div class="studentAllotForm">
                                <form method="post" id="allot_exam_student">
                                    <div class="row mb-2">
                                        <div class="col-sm-12 col-md-12 col-12 d-flex justify-content-end">
                                            <button type="button" class="btn btn-primary" id="saveAssignStudents">Assign
                                                Students</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="student-table"
                                                    class="table table-bordered table-striped dataTable dtr-inline"
                                                    aria-describedby="example1_info">
                                                    <thead>
                                                        <tr>
                                                            <th width="60"><label class="checkbox-inline bolds"><input
                                                                        type="checkbox" class="select_all"
                                                                        autocomplete="off">
                                                                    All</label></th>
                                                            <th>Admission No</th>
                                                            <th>Student Roll No</th>
                                                            <th>Student Name</th>
                                                            <th>Father Name</th>
                                                            <th>Gender</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="studentTableBody">
                                                        <!-- Student data will be dynamically added here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('scripts')
    <script>
        $(document).ready(function() {

            var examination_id = '{{ $examinations->id }}';
            var classId = '{{ $classId }}';
            var sectionId = '{{ $sectionId }}';

            console.log(examination_id);
            console.log(classId);
            console.log(sectionId);

            // Fetch student data immediately after page load
            getAllStudentBasedOnClassSection(examination_id, classId, sectionId);

            // Function to update the state of the "All" checkbox based on individual checkboxes
            function updateSelectAllCheckbox() {
                var allChecked = $('#studentTableBody input[type="checkbox"]:checked').length ===
                    $('#studentTableBody input[type="checkbox"]').length;
                $('.select_all').prop('checked', allChecked);
            }

            // Attach change event handler to dynamically generated checkboxes using event delegation
            function checkboxValuesAndDatas() {
                $('#studentTableBody').on('change', 'input[type="checkbox"]', function() {
                    updateSelectAllCheckbox();
                });

                // Attach a change event handler to the "All" checkbox
                $('.select_all').change(function() {
                    var isChecked = $(this).prop('checked');
                    $('#studentTableBody input[type="checkbox"]').prop('checked',
                        isChecked);
                });

                // Trigger the change event to set the initial state
                $('#studentTableBody input[type="checkbox"]').trigger('change');
            }

            // Fetch students based on the selected class and section
            function getAllStudentBasedOnClassSection(examination_id, classId, sectionId, callback) {
                $.ajax({
                    url: baseURL + '/admin/assign-students/by-class-section/' + examination_id +
                        '/' +
                        classId + '/' + sectionId,
                    type: 'GET',
                    success: function(data) {
                        $('.ajaxHide').css('display', 'block');
                        // Clear existing content in the student container
                        $('#studentTableBody').empty();

                        // Check if there are any students
                        if (data.original && data.original.length > 0) {
                            // Append new rows based on the fetched students
                            $.each(data.original, function(index, studentData) {
                                var student = studentData
                                    .student; // Extract student data
                                var user = studentData.user; // Extract user data
                                // console.log(user);
                                var row = '<tr data-student-id="' + student.id +
                                    '">' +
                                    '<td><input type="checkbox" name="student_session_id[]" value="' +
                                    studentData.id + '"> </td>' +
                                    '<td>' + student.admission_no + '</td>' +
                                    '<td>' + student.roll_no + '</td>' +
                                    '<td>' + (user ? user.f_name : '') + '</td>' +
                                    '<td>' + (user ? user.father_name : '') + '</td>' +
                                    '<td>' + (user ? user.gender : '') + '</td>' +
                                    '<td>' +
                                    '</tr>';

                                $('#studentTableBody').append(row);
                            });
                            // Populate existing exam data in the form
                            populateExistingExamAssigned(data.original);
                            // Update the "All" checkbox status
                            updateSelectAllCheckbox();
                        } else {
                            // If there are no students, display a message or handle accordingly
                            $('#studentTableBody').append(
                                '<tr><td colspan="5">No students found for the selected section</td></tr>'
                            );
                        }
                    }
                });

                //checkbox values is checked then it should be populated on form
                checkboxValuesAndDatas();

                if (typeof callback === 'function') {
                    callback();
                }
            }


            // Function to populate existing exam data in the form
            function populateExistingExamAssigned(students) {
                $.each(students, function(index, studentData) {
                    if (studentData.exam_students && studentData.exam_students.length > 0) {
                        $.each(studentData.exam_students, function(index, student) {

                            $('input[name="student_session_id[]"][value="' +
                                student.student_session_id + '"]').attr('checked', true);
                        });
                    }
                });
            }

            // Attach click event handler to the Save Attendance button
            $('#saveAssignStudents').click(function() {



                var examinationId = '{{ $examinations->id }}';
                var classId = '{{ $classId }}';
                var sectionId = '{{ $sectionId }}';


                // Collect checkbox values
                var checkboxValues = [];
                $('input[type="checkbox"][name="student_session_id[]"]:checked').each(function() {
                    checkboxValues.push($(this).val());
                });

                // Send an AJAX request to save the attendance data
                $.ajax({
                    url: '{{ route('admin.assign-students.save') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: {
                        examination_id: examinationId,
                        class_id: classId,
                        section_id: sectionId,
                        student_sessions: checkboxValues
                    },
                    success: function(response) {
                        // Handle the response (e.g., show a success message)
                        if (response.message) {
                            // If there's a success message in the response, display it
                            toastr.success(response.message);
                        } else {
                            // If no success message is provided, display a default success message
                            toastr.success('Assigned  successfully');
                        }
                    },
                    error: function(error) {
                        // Handle errors (e.g., show an error message)
                        console.error(error);
                        toastr.error(
                            'Error occurred while assigning exam. Please try again later.'
                        );
                    }

                });
            });

        });
    </script>
@endsection
@endsection
