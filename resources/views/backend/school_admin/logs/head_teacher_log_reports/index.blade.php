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
                    <h5>Select Date</h5>
                    <div class="mt-4 d-flex flex-column">
                        <div class="p-2 label-input">
                            <label for="nepali-datepicker">Date:</label>
                            <div class="form-group">
                                <div class="input-group date" id="admission-datetimepicker" data-target-input="nearest">
                                    <input id="nepali-datepicker" name="date" type="text" class="form-control datetimepicker-input" />
                                </div>
                                @error('date')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>
                        <div class="search-button-container d-flex col-md-3 justify-content-end mt-2">
                            <button id="searchButton" class="btn btn-sm btn-primary">Search</button>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <p id="messagePlaceholder"></p>
                    <div class="hr-line-dashed"></div>
                    <div class="table-responsive">
                        <table id="report-table" class="table table-bordered table-striped dataTable dtr-inline" aria-describedby="example1_info">
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
                                    <td id="totalStudents">{{ $totalStudents }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Present Students: </td>
                                    <td class="">
                                        <div id="presentBoys">Male: {{ $presentBoys }}</div>
                                        <div class="mt-2" id="presentGirls">Female: {{ $presentGirls }}</div>
                                        <div class="mt-2" id="totalPresentStudents">Total: {{ $presentStudents }}</div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Absent Students: </td>
                                    <td class="">
                                        <div id="absentBoys">Male: {{ $absentBoys }}</div>
                                        <div id="absentGirls" class="mt-2">Female: {{ $absentGirls }}</div>
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
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>ECA: </td>
                                    <td id="assemblyManagement">{{ $assemblyManagement }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Miscellaneous: </td>
                                    <td id="miscellaneous">{{ $miscellaneous }}</td>
                                    <td></td>
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
@include('backend.includes.nepalidate')
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

    // Initialize nepali-datepicker
    $('#nepali-datepicker').nepaliDatePicker({
        dateFormat: 'YYYY-MM-DD',
        closeOnDateSelect: true,
        onChange: function () {
            // Optionally handle change event
        }
    });

    // Function to update table with fetched data
    function updateTable(data) {
        $('#totalStudents').text(data.totalStudents);
        $('#presentBoys').text('Male: ' + data.presentBoys);
        $('#presentGirls').text('Female: ' + data.presentGirls);
        $('#totalPresentStudents').text('Total: ' + data.presentStudents);
        $('#absentBoys').text('Male: ' + data.absentBoys);
        $('#absentGirls').text('Female: ' + data.absentGirls);
        $('#totalAbsentStudents').text('Total: ' + data.absentStudents);
        $('#presentStaffCount').text(data.presentStaffs);
        $('#absentStaffCount').text(data.absentStaffs);
        $('#majorIncident').text(data.majorIncident);
        $('#majorWorkObservation').text(data.majorWorkObservation);
        $('#assemblyManagement').text(data.assemblyManagement);
        $('#miscellaneous').text(data.miscellaneous);
    }

    // Fetch and display data based on the selected Nepali date
    $('#searchButton').click(function () {
        var selectedDate = $('#nepali-datepicker').val();

        $.ajax({
            url: '{{ route("admin.headteacherlog-reports.index") }}',
            method: 'GET',
            data: {
                date: selectedDate
            },
            success: function (response) {
                console.log('Data fetched successfully:', response);
                if (response.message) {
                    $('#messagePlaceholder').text(response.message);
                    $('#report-table tbody').hide();
                } else {
                    $('#messagePlaceholder').text('');
                    $('#report-table tbody').show();
                    updateTable(response);
                }
            },
            error: function (error) {
                console.error('Error fetching data:', error);
                $('#messagePlaceholder').text('Error fetching data. Please try again.');
            }
        });
    });
});
</script>
@endsection