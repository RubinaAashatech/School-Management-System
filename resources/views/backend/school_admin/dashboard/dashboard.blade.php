@extends('backend.layouts.master')

@section('content')
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
                <h2>{{ $page_title }}</h2>
            </div>
            {{-- @include('backend.school_admin.assign_class_teacher.partials.action') --}}
        </div>
        <div class="card">
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
                    <div class="col-md-6 col-lg-6 mt-4 ">
                        <span class="fw-bold">School Wise Student's Attendence</span>
                        <div class="bg-gray pt-5">
                            <canvas id="schoolAttendanceChart" width="600" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 mt-4 ">
                        <span class="fw-bold">School Wise Staff's Attendence</span>
                        <div class="bg-gray pt-5">
                            <canvas id="schoolsSaffAttendanceChart" width="600" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 mt-4 ">
                        <span class="fw-bold">Class Wise Students</span>
                        <div class="bg-gray pt-5">
                            <canvas id="classWiseStudents" width="600" height="200"></canvas>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@section('scripts')
    @include('backend.includes.chartjs')
    <script>
        //number of student accordance to school
        var school_student_count = @json($school_students_count);
        var school_staffs_count = @json($school_staffs_count);
        var school_wise_student_attendences = @json($school_wise_student_attendences);
        var school_wise_staffs_attendences = @json($school_wise_staffs_attendences);
        var class_wise_students = @json($class_wise_students);

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
        //chart based on school student
        document.addEventListener('DOMContentLoaded', function() {

            // Extract school names and attendance data
            var schoolNames = school_wise_student_attendences.map(function(item) {
                return item.school_name;
            });

            var totalStudent = school_wise_student_attendences.map(function(item) {
                return item.total_student;
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
                            label: 'Total Students',
                            data: totalStudent,
                            backgroundColor: 'rgba(0, 0, 200, 0.5)',
                            borderWidth: 1
                        },
                        {
                            label: 'Present Students',
                            data: presentStudents,
                            backgroundColor: 'rgba(50, 200, 50, 0.5)',
                            borderWidth: 1
                        }, {
                            label: 'Absent Students',
                            data: absentStudents,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderWidth: 1
                        }
                    ]
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
        //chart based on school staffs
        document.addEventListener('DOMContentLoaded', function() {

            // Extract school names and attendance data
            var schoolNames = school_wise_staffs_attendences.map(function(item) {
                return item.school_name;
            });

            var totalStaffs = school_wise_staffs_attendences.map(function(item) {
                return item.total_staffs;
            });

            var presentStaffs = school_wise_staffs_attendences.map(function(item) {
                return item.present_staffs;
            });

            var absentStaffs = school_wise_staffs_attendences.map(function(item) {
                return item.absent_staffs;
            });

            var holidayStaffs = school_wise_staffs_attendences.map(function(item) {
                return item.holiday_staffs;
            });

            // Create a bar chart
            var ctx = document.getElementById('schoolsSaffAttendanceChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: schoolNames,
                    datasets: [{
                            label: 'Total Staffs',
                            data: totalStaffs,
                            backgroundColor: 'rgba(0, 0, 200, 0.5)',
                            borderWidth: 1
                        },
                        {
                            label: 'Present Staffs',
                            data: presentStaffs,
                            backgroundColor: 'rgba(50, 200, 50, 0.5)',
                            borderWidth: 1
                        }, {
                            label: 'Absent Staffs',
                            data: absentStaffs,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderWidth: 1
                        }, {
                            label: 'Holiday Staffs',
                            data: holidayStaffs,
                            backgroundColor: 'rgba(255, 9, 100, 0.5)',
                            borderWidth: 1
                        }
                    ]
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
        //chart based on class students
        document.addEventListener('DOMContentLoaded', function() {

            var labels = [];
            var datasets = [];
            class_wise_students.forEach(function(item) {
                // Push school name to labels array
                labels.push(item.school_name);

                // Initialize an array to store datasets for each school
                var schoolDatasets = [];

                // Iterate through each class and its student total
                for (var i = 1; i <= Object.keys(item).length - 1; i++) {
                    // Construct dataset object for each class
                    schoolDatasets.push({
                        label: 'Class ' + i, // Customize label
                        data: [item['total_student_class_' + i]],
                        backgroundColor: getRandomColor(),
                        borderColor: getRandomColor(),
                        borderWidth: 1
                    });
                }

                // Push datasets for the school to the main datasets array
                datasets.push(schoolDatasets);
            });

            // Create the chart
            var classWiseStudents = document.getElementById('classWiseStudents').getContext('2d');
            var myChart = new Chart(classWiseStudents, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: datasets // Flatten the datasets array
                },
                options: {
                    // Add chart options as needed
                }
            });

            // Function to generate random color
            function getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

        });
    </script>
@endsection
@endsection
