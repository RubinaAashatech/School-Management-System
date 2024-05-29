@extends('backend.layouts.master')


@section('content')
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                <h2>{{ $page_title }}</h2>
            </div>
            @include('backend.school_admin.lessons.partials.action')
        </div>

        <div class="mt-4">

            <div class="card">
                <div class="card-body">
                    <h5>Select Criteria</h5>

                    {{-- <div class="mt-4 d-flex flex-column">
                        <h6>Logged Date</h6>
                        <div class="date-button-container">
                            <input id="selectDate" type="text" placeholder="Enter Date" style="width: 25%;">
                        </div>

                    </div> --}}

                    <div class="mt-4 d-flex flex-column">
                        <label for="datetimepicker">Logged Date:</label>
                        <div class="form-group">
                            <div class="input-group date" id="datetimepicker"
                                data-target-input="nearest">
                                <input id="nepali-datepicker" type="text"
                                    class="form-control datetimepicker-input" />
                            </div>
                        </div>

                        <div class="search-button-container d-flex col-md-3 justify-content-end mt-2">
                            <button id="searchButton" class="btn btn-sm btn-primary" type="submit">Search</button>
                        </div>
                    </div>


                    <div class="hr-line-dashed"></div>
                    <p id="messagePlaceholder"></p>
                    <div class="hr-line-dashed"></div>
                    <div class="d-flex gap-2 m-2">
                        <h5 class="">Record Reports</h5>
                        <p>- <span id="logged_date" class="must underline"></span></p>

                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="table-responsive">
                        <table id="report-table" class="table table-bordered table-striped dataTable dtr-inline"
                            aria-describedby="example1_info">
                            <thead>
                                <tr>
                                    <th>Particulars</th>
                                    <th>Description</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total Students: </td>
                                    <td id="totalStudents"></td>
                                </tr>
                                <tr>
                                    <td>Present Students: </td>
                                    <td class="">
                                        <div id="presentMaleCount">Male: </div>
                                        <div class="mt-2" id="presentFemaleCount">Female: </div>
                                        <div class="mt-2" id="totalPresentStudents">Total: </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Absent Students: </td>
                                    <td class="">
                                        <div id="absentMaleCount">Male: </div>
                                        <div id="absentFemaleCount" class="mt-2">Female: </div>
                                        <div id="totalAbsentStudents" class="mt-2">Total: </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Present Number of Teachers/Staffs: </td>
                                    <td id="presentStaffCount"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Absent Number of Teachers/Staffs: </td>
                                    <td id="absentStaffCount"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Record of the Major Incidents: </td>
                                    <td colspan="2" id="majorIncident"></td>
                                </tr>
                                <tr>
                                    <td>Major Work Observation: </td>
                                    <td id="majorWorkObservation"></td>
                                    {{-- <td></td> --}}
                                </tr>
                                <tr>
                                    <td>ECA: </td>
                                    <td id="assemblyManagement"></td>
                                    {{-- <td></td> --}}
                                </tr>
                                <tr>
                                    <td>Miscelleaneous: </td>
                                    <td id="miscellaneous"></td>
                                    {{-- <td></td> --}}
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>

@section('scripts')
@include('backend.includes.nepalidate')
    <!-- Add this script block to your view -->
    <script>
        $(document).ready(function() {
            $('#searchButton').on('click', function() {
                var selectedDate = $('#nepali-datepicker').val(); // Get the selected date value


                // Update the logged_date span with the selected date value to display the date
                // $('#logged_date').text(selectedDate);

                // Make an AJAX request to fetch data from StudentAttendance controller method
                $.ajax({
                    url: '{{ route('admin.headteacherlogreports_get') }}',
                    type: 'GET',
                    data: {
                        date: selectedDate
                    }, // Pass the selected date as data
                    success: function(response) {
                        // Check if the response contains the message key
                        if (response.hasOwnProperty('message')) {
                            // Update the HTML with the message
                            $('#messagePlaceholder').text(response.message);
                        } else {
                            // Update the HTML with the fetched attendance report
                            $('#attendanceReportContainer').html(response);

                            // Handle the response
                            // Handle the response
                            var students = response.students;

                            // Get the count of students
                            var totalStudents = students.length;

                            // Update total students in the table
                            $('#totalStudents').text(totalStudents);

                            // Handle the response FOR PRESENT STUDENTS/STAFFS
                            var presentMaleCount = response.presentMaleCount;
                            var presentFemaleCount = response.presentFemaleCount;
                            var totalPresentStudents = presentMaleCount + presentFemaleCount;

                            var absentMaleCount = response.absentMaleCount;
                            var absentFemaleCount = response.absentFemaleCount;
                            var totalAbsentStudents = absentMaleCount + absentFemaleCount;

                            var presentStaffCount = response.presentStaffCount;
                            var absentStaffCount = response.absentStaffCount;

                            // Data from head_teacher_log
                            var majorIncident = response.majorIncident;
                            var majorWorkObservation = response.majorWorkObservation;
                            var assemblyManagement = response.assemblyManagement;
                            var miscellaneous = response.miscellaneous;


                            // Update UI with the fetched attendance counts
                            $('#presentMaleCount').text('Male: ' + presentMaleCount);
                            $('#presentFemaleCount').text('Female: ' + presentFemaleCount);
                            $('#totalPresentStudents').text('Total: ' + totalPresentStudents);

                            $('#absentMaleCount').text('Male: ' + absentMaleCount);
                            $('#absentFemaleCount').text('Female: ' + absentFemaleCount);

                            $('#presentStaffCount').text(presentStaffCount);
                            $('#absentStaffCount').text(absentStaffCount);

                            $('#majorIncident').text(majorIncident);
                            $('#majorWorkObservation').text(majorWorkObservation);
                            $('#assemblyManagement').text(assemblyManagement);
                            $('#miscellaneous').text(miscellaneous);


                            // Update the table with the list of students
                            $('#studentTable tbody').empty(); // Clear existing table rows
                            response.students.forEach(function(student) {
                                $('#studentTable tbody').append('<tr><td>' + student
                                    .id +
                                    '</td><td>' + student.name + '</td></tr>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
@endsection
