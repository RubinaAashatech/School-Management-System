@extends('backend.layouts.master')

@section('content')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* For the cards in the dashboard */
        .ag-courses_box {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: start;
            -ms-flex-align: start;
            align-items: flex-start;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;

            padding: 10px 0;
        }

        .ag-courses_item {
            -ms-flex-preferred-size: calc(33.33333% - 30px);
            flex-basis: calc(33.33333% - 150px);

            margin: 0 5px 10px;

            overflow: hidden;

            border-radius: 28px;
        }

        .ag-courses-item_link {
            display: block;
            padding: 30px 20px;
            background-image: linear-gradient(-45deg, rgba(0, 160, 255, 0.86), #0048a2), url(../img/generic/bg-navbar.png);

            overflow: hidden;

            position: relative;
        }

        .ag-courses-item_link:hover,
        .ag-courses-item_link:hover .ag-courses-item_date {
            text-decoration: none;
            color: #FFF;
        }

        .ag-courses-item_link:hover .ag-courses-item_bg {
            -webkit-transform: scale(10);
            -ms-transform: scale(10);
            transform: scale(10);
        }

        .ag-courses-item_title {
            min-height: 87px;
            margin: 0 0 25px;

            overflow: hidden;

            font-weight: bold;
            font-size: 30px;
            color: #FFF;

            z-index: 2;
            position: relative;
        }

        .ag-courses-item_date-box {
            font-size: 18px;
            color: #FFF;

            z-index: 2;
            position: relative;
        }

        .ag-courses-item_date {
            font-weight: bold;
            color: #f9b234;

            -webkit-transition: color .5s ease;
            -o-transition: color .5s ease;
            transition: color .5s ease
        }

        .ag-courses-item_bg {
            height: 128px;
            width: 128px;
            background-color: #f9b234;

            z-index: 1;
            position: absolute;
            top: -75px;
            right: -75px;

            border-radius: 50%;

            -webkit-transition: all .5s ease;
            -o-transition: all .5s ease;
            transition: all .5s ease;
        }

        .ag-courses_item:nth-child(2n) .ag-courses-item_bg {
            background-color: #3ecd5e;
        }

        .ag-courses_item:nth-child(3n) .ag-courses-item_bg {
            background-color: #e44002;
        }

        .ag-courses_item:nth-child(4n) .ag-courses-item_bg {
            background-color: #952aff;
        }

        .ag-courses_item:nth-child(5n) .ag-courses-item_bg {
            background-color: #cd3e94;
        }

        .ag-courses_item:nth-child(6n) .ag-courses-item_bg {
            background-color: #4c49ea;
        }



        @media only screen and (max-width: 979px) {
            .ag-courses_item {
                -ms-flex-preferred-size: calc(50% - 30px);
                flex-basis: calc(50% - 30px);
            }

            .ag-courses-item_title {
                font-size: 24px;
            }
        }

        @media only screen and (max-width: 767px) {
            .ag-format-container {
                width: 96%;
            }

        }

        @media only screen and (max-width: 639px) {
            .ag-courses_item {
                -ms-flex-preferred-size: 100%;
                flex-basis: 100%;
            }

            .ag-courses-item_title {
                min-height: 72px;
                line-height: 1;

                font-size: 24px;
            }

            .ag-courses-item_link {
                padding: 22px 40px;
            }

            .ag-courses-item_date-box {
                font-size: 16px;
            }
        }

        .no_transaction {
            width: 100% !important;
        }
    </style>

    @if (Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif

    @if (Session::has('error'))
        <div class="alert alert-danger">
            {{ Session::get('error') }}
        </div>
    @endif

    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                {{-- <h2>{{ $page_title }}</h2> --}}
            </div>
            @include('backend.school_admin.assign_class_teacher.partials.action')
         </div>

            </form>
            <hr>

            <div class="card-body">
                <div class="school-wise-report">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                            
                                <th>Total Students</th>
                                <th>Present Student</th>
                                <th>Absent Student</th>
                                <th>Total Staff</th>
                                <th>Present Staff</th>
                                <th>Absent Staff</th>
                            </tr>
                        </thead>
                        <tbody>
                          
                                <tr>
                                    
                                    <td> {{ $totalStudents }}</td>
                                    <td> {{ $presentStudents }}</td>
                                    <td> {{ $absentStudents }}</td>
                                    <td> {{ $totalStaffs }}</td>
                                    <td>{{ $presentStaffs }}</td>
                                    <td> {{ $absentStaffs }}</td>
                                   
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form id="filterForm"> 
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
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
        
            <div class="card-body">
                <div class="school-wise-report">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Schools</th>
                                <th>Total Students</th>
                                <th>Present Student</th>
                                <th>Absent Student</th>
                                <th>Late Student</th>
                                <th>Total Staff</th>
                                <th>Present Staff</th>
                                <th>Absent Staff</th>
                                <th>Late Staff</th>
                                <th>Holiday Staff</th>
                                <th>Major Incidents</th>
                                <th>ECA/CCA</th>
                                <th>Miscellaneous</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach ($schools_wise_reports as $report)
                                <tr>
                                    <td>{{ $report['school_name'] }}</td>
                                    <td>{{ $report['total_student'] }}</td>
                                    <td>{{ $report['present_student'] }}</td>
                                    <td>{{ $report['absent_student'] }}</td>
                                    <td>{{ $report['late_student'] }}</td>
                                    <td>{{ $report['total_staffs'] }}</td>
                                    <td>{{ $report['present_staffs'] }}</td>
                                    <td>{{ $report['absent_staffs'] }}</td>
                                    <td>{{ $report['late_staffs'] }}</td>
                                    <td>{{ $report['holiday_staffs'] }}</td>
                                    <td>{{ $report['major_incidents'] }}</td>
                                    <td>{{ $report['eca_cca'] }}</td>
                                    <td>{{ $report['miscellaneous'] }}</td>
                                </tr> 
                            @endforeach 
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div class="card mt-2">
            <div class="card-body">
                <div class="row p-4 justify-content-around" height="300">
                    <div class="col-md-5 col-lg-5 container-fluid">
                        <div class="bg-gray pt-5">
                            <canvas id="schoolWiseStudentChart" height="100%"></canvas>
                        </div>
                        <span class="fw-bold">School Wise Students</span>
                    </div>
                    <div class="col-md-5 col-lg-5 container-fluid">
                        <div class="bg-gray pt-5">
                            <canvas id="schoolWiseStaffsChart" height="100%">
                            </canvas>
                        </div>
                        <span class="fw-bold">School Wise Staffs</span>
                    </div>
                    <div class="col-md-12 col-lg-12 mt-4 ">
                        <div class="bg-gray pt-5">
                            <canvas id="schoolAttendanceChart" width="600" height="200"></canvas>
                        </div>
                        <span class="fw-bold">School Wise Student's Attendence</span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    @include('backend.includes.nepalidate')
    @include('backend.includes.chartjs')
    <script>
        // Attach click event handler to the search button
        $('#searchButton').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Get the selected class and section IDs
            var date = $('input[name="date"]').val();
            // Fetch schoolwise reports
            $.ajax({
                url: '{{ route('admin.school-wise-reports') }}',
                type: 'POST',
                data: {
                    date: date
                },
                success: function(data) {
                    // Clear existing table rows
                    $('.school-wise-report tbody').empty();
                    // Iterate over the fetched data and append rows to the table
                    $.each(data, function(key, value) {
                        var rowHtml = '<tr>';
                        rowHtml += '<td>' + value.school_name + '</td>';
                        rowHtml += '<td>' + value.total_student + '</td>';
                        rowHtml += '<td>' + value.present_student + '</td>';
                        rowHtml += '<td>' + value.absent_student + '</td>';
                        rowHtml += '<td>' + value.total_staffs + '</td>';
                        rowHtml += '<td>' + value.present_staffs + '</td>';
                        rowHtml += '<td>' + value.absent_staffs + '</td>';
                        rowHtml += '<td>' + value.major_incidents + '</td>';
                        rowHtml += '<td>' + value.eca_cca + '</td>';
                        rowHtml += '<td>' + value.miscellaneous + '</td>';
                        rowHtml += '</tr>';
                        $('.school-wise-report tbody').append(rowHtml);
                    });
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error('Ajax Request Error:', textStatus, errorThrown);
                }
            });
        });
        //number of student accordance to school
        var school_student_count = @json($school_students_count);
        var school_staffs_count = @json($school_staffs_count);
        var school_wise_student_attendences = @json($school_wise_student_attendences);
        //school-wise student
        const ctx = document.getElementById('schoolWiseStudentChart');
        new Chart(ctx, {
            type: 'bar',
            data: school_student_count,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        //school-wise staffs
        const schoolstaffcount = document.getElementById('schoolWiseStaffsChart');
        new Chart(schoolstaffcount, {
            type: 'bar',
            data: school_staffs_count,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Extract school names and attendance data
            var schoolNames = school_wise_student_attendences.map(function(item) {
                return item.school_name;
            });
            var presentStudents = school_wise_student_attendences.map(function(item) {
                return item.present_student;
            });
            var absentStudents = school_wise_student_attendences.map(function(item) {
                return item.absent_student;
            });
            // Create a bar chart
            var ctx = document.getElementById('schoolAttendanceChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: schoolNames,
                    datasets: [{
                        label: 'Present Students',
                        data: presentStudents,
                        backgroundColor: 'rgba(50, 200, 50, 0.5)', // Blue color for present students
                        borderWidth: 1
                    }, {
                        label: 'Absent Students',
                        data: absentStudents,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)', // Red color for absent students
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        });
    </script>
@endsection

@endsection 