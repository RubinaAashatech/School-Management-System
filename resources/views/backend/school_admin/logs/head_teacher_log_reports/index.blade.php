@extends('backend.layouts.master')
@section('content')
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                {{-- <h2>{{ $page_title }}</h2> --}}
            </div>
            @include('backend.school_admin.lessons.partials.action')

        </div>
        <div class="mt-4">
            <div class="card">
                <div class="card-body">
                    <h5>Select Criteria</h5>
                    <div class="mt-4 d-flex flex-column">
                        <div class="p-2 label-input">
                            <label for="datetimepicker">Date:</label>
                            <div class="form-group">
                                <div class="input-group date" id="admission-datetimepicker" data-target-input="nearest">
                                    <input id="nepali-datepicker" name="date" type="text" class="form-control datetimepicker-input" />
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
                                $('#nepali-datepicker').val(formattedDate);
                            });
                        </script>
                        <div class="search-button-container d-flex col-md-3 justify-content-end mt-2">
                            <button id="searchButton" class="btn btn-sm btn-primary">Search</button>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <p id="messagePlaceholder"></p>
                    <div class="hr-line-dashed"></div>
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
                                <td id="totalStudents">{{ $totalStudents}}</td>
                            </tr>
                            <tr>
                                <td>Present Students: </td>
                                <td class="">
                                    <div id="presentMaleCount">Male: {{ $presentBoys}} </div>
                                    <div class="mt-2" id="presentFemaleCount">Female: {{ $presentGirls}} </div>
                                    <div class="mt-2" id="totalPresentStudents">Total: {{ $presentStudents }}</div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Absent Students: </td>
                                <td class="">
                                    <div id="absentMaleCount">Male: {{ $absentBoys}} </div>
                                    <div id="absentFemaleCount" class="mt-2">Female: {{ $absentGirls}} </div>
                                    <div id="totalAbsentStudents" class="mt-2">Total: {{ $absentStudents }}</div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Present Number of Teachers/Staffs: </td>
                                <td id="presentStaffCount">{{ $presentStaffs }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Absent Number of Teachers/Staffs: </td>
                                <td id="absentStaffCount">{{ $absentStaffs }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Record of the Major Incidents: </td>
                                <td colspan="2" id="majorIncident">{{ $majorIncident }}</td>
                            </tr>
                            <tr>
                                <td>Major Work Observation: </td>
                                <td id="majorWorkObservation">{{ $majorWorkObservation }}</td>
                            </tr>
                            <tr>
                                <td>ECA: </td>
                                <td id="assemblyManagement">{{ $assemblyManagement }}</td>
                            </tr>
                            <tr>
                                <td>Miscellaneous: </td>
                                <td id="miscellaneous">{{ $miscellaneous }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="http://nepalidatepicker.sajanmaharjan.com.np/nepali.datepicker/js/nepali.datepicker.v4.0.4.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        // Initialize nepali-datepicker
        $('#nepali-datepicker').nepaliDatePicker({
            dateFormat: 'YYYY-MM-DD', // Set the format for submitting the date
            closeOnDateSelect: true,
            onChange: function () {
                // Optionally handle change event
            }
        });

        // Fetch and display data based on the selected Nepali date
        $('#searchButton').click(function () {
            var selectedDate = $('#nepali-datepicker').val();

            // Implement your AJAX call here to fetch data based on the selectedDate
            $.ajax({
                url: '{{ route("admin.attendance_reports.report") }}', // Use Laravel named route
                method: 'GET',
                data: {
                    date: selectedDate
                },
                success: function (response) {
                    console.log('Data fetched successfully:', response); // Debugging line

                    // Update existing table data with fetched data
                    $('#presentBoys').text(response.presentBoys);
                    $('#presentGirls').text(response.presentGirls);
                    $('#absentBoys').text(response.absentBoys);
                    $('#absentGirls').text(response.absentGirls);
                    $('#totalStudents').text(response.totalStudents);
                    // Add other data updates as needed
                },
                error: function (error) {
                    console.error('Error fetching data:', error); // Debugging line
                }
            });
        });

        // Automatically submit the form on page load with today's date
        $('#searchButton').click();
    });
</script>
@endsection