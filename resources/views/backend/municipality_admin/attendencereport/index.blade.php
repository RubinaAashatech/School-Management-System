@extends('backend.layouts.master')
@section('content')
<div class="container">
    <h1>Attendance Report</h1>
    <form action="{{ route('admin.attendance_reports.report') }}" method="GET">
        <div class="col-lg-3 col-sm-3 mt-2">
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
        </div>
        <div class="search-button-container d-flex col-md-3 justify-content-end mt-2">
            <button id="searchButton" class="btn btn-sm btn-primary">Search</button>
        </div>
    </form>
    @if(isset($studentAttendances))
        {{-- <h3>Attendance Report @if(isset($date)) for {{ $date }} @endif</h3> --}}
        <h3>Student Attendance</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Attendance Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentAttendances as $attendance)
                    <tr>
                        <td>{{ $attendance->student->user->f_name }} {{ $attendance->student->user->l_name }}</td>
                        <td>{{ $attendance->attendance_type_id == 1 ? 'Present' : 'Absent' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No attendance records found.</p>
    @endif
</div>
<script src="http://nepalidatepicker.sajanmaharjan.com.np/nepali.datepicker/js/nepali.datepicker.v4.0.4.min.js"></script>
<script type="text/javascript">
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
    });
</script>
@endsection