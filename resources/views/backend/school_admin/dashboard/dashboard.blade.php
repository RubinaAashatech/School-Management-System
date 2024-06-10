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
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <h5>Total Students</h5>
                        <p id="totalStudents">0</p>
                    </div>
                    <div class="col-md-2">
                        <h5>Present Students</h5>
                        <p id="presentStudents">0</p>
                    </div>
                    <div class="col-md-2">
                        <h5>Absent Students</h5>
                        <p id="absentStudents">0</p>
                    </div>
                    <div class="col-md-2">
                        <h5>Total Staffs</h5>
                        <p id="totalStaffs">0</p>
                    </div>
                    <div class="col-md-2">
                        <h5>Present Staffs</h5>
                        <p id="presentStaffs">0</p>
                    </div>
                    <div class="col-md-2">
                        <h5>Absent Staffs</h5>
                        <p id="absentStaffs">0</p>
                    </div>
                </div>
            </div>
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
        // Data from the server
        var school_student_count = @json($school_students_count);
        var school_staffs_count = @json($school_staffs_count);
        var school_wise_student_attendences = @json($school_wise_student_attendences);
        var school_wise_staffs_attendences = @json($school_wise_staffs_attendences);
        var class_wise_students = @json($class_wise_students);

        // Function to calculate and update summary counts
        function updateSummaryCounts() {
            let totalStudents = 0;
            let presentStudents = 0;
            let absentStudents = 0;
            let totalStaffs = 0;
            let presentStaffs = 0;
            let absentStaffs = 0;

            school_wise_student_attendences.forEach(item => {
                totalStudents += item.total_student;
                presentStudents += item.present_student;
                absentStudents += item.absent_student;
            });

            school_wise_staffs_attendences.forEach(item => {
                totalStaffs += item.total_staffs;
                presentStaffs += item.present_staffs;
                absentStaffs += item.absent_staffs;
            });

            document.getElementById('totalStudents').innerText = totalStudents;
            document.getElementById('presentStudents').innerText = presentStudents;
            document.getElementById('absentStudents').innerText = absentStudents;
            document.getElementById('totalStaffs').innerText = totalStaffs;
            document.getElementById('presentStaffs').innerText = presentStaffs;
            document.getElementById('absentStaffs').innerText = absentStaffs;
        }

        // Update summary counts on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', updateSummaryCounts);

        // Chart.js configurations...
        const ctxStudent = document.getElementById('schoolWiseStudentChart');
        new Chart(ctxStudent, {
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

        const ctxStaff = document.getElementById('schoolWiseStaffsChart');
        new Chart(ctxStaff, {
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

        // School-wise student attendance chart
        document.addEventListener('DOMContentLoaded', function() {
            var schoolNames = school_wise_student_attendences.map(item => item.school_name);
            var totalStudent = school_wise_student_attendences.map(item => item.total_student);
            var presentStudents = school_wise_student_attendences.map(item => item.present_student);
            var absentStudents = school_wise_student_attendences.map(item => item.absent_student);

            var ctxAttendance = document.getElementById('schoolAttendanceChart').getContext('2d');
            new Chart(ctxAttendance, {
                type: 'bar',
                data: {
                    labels: schoolNames,
                    datasets: [{
                        label: 'Total Students',
                        data: totalStudent,
                        backgroundColor: 'rgba(0, 0, 200, 0.5)',
                        borderWidth: 1
                    }, {
                        label: 'Present Students',
                        data: presentStudents,
                        backgroundColor: 'rgba(50, 200, 50, 0.5)',
                        borderWidth: 1
                    }, {
                        label: 'Absent Students',
                        data: absentStudents,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        // School-wise staff attendance chart
        document.addEventListener('DOMContentLoaded', function() {
            var schoolNames = school_wise_staffs_attendences.map(item => item.school_name);
            var totalStaffs = school_wise_staffs_attendences.map(item => item.total_staffs);
            var presentStaffs = school_wise_staffs_attendences.map(item => item.present_staffs);
            var absentStaffs = school_wise_staffs_attendences.map(item => item.absent_staffs);
            var holidayStaffs = school_wise_staffs_attendences.map(item => item.holiday_staffs);

            var ctxStaffAttendance = document.getElementById('schoolsSaffAttendanceChart').getContext('2d');
            new Chart(ctxStaffAttendance, {
                type: 'bar',
                data: {
                    labels: schoolNames,
                    datasets: [{
                        label: 'Total Staffs',
                        data: totalStaffs,
                        backgroundColor: 'rgba(0, 0, 200, 0.5)',
                        borderWidth: 1
                    }, {
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
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        // Class-wise students chart
        document.addEventListener('DOMContentLoaded', function() {
            var labels = [];
            var datasets = [];
            class_wise_students.forEach(function(item) {
                labels.push(item.school_name);
                var schoolDatasets = [];
                for (var i = 1; i <= Object.keys(item).length - 1; i++) {
                    schoolDatasets.push({
                        label: 'Class ' + i,
                        data: [item['total_student_class_' + i]],
                        backgroundColor: getRandomColor(),
                        borderColor: getRandomColor(),
                        borderWidth: 1
                    });
                }
                datasets.push(schoolDatasets);
            });

            var classWiseStudents = document.getElementById('classWiseStudents').getContext('2d');
            new Chart(classWiseStudents, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: datasets.flat()
                },
                options: {
                    // Add chart options as needed
                }
            });

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
