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


    <style>
        
        a {
            outline: none;
            text-decoration: none;
            color: #555;
        }

        a:hover,
        a:focus {
            outline: none;
            text-decoration: none;
        }

        img {
            border: 0;
        }

        input,
        textarea,
        select {
            outline: none;
            resize: none;
            font-family: 'Muli', sans-serif;
        }

        a,
        input,
        button {
            outline: none !important;
        }

        button::-moz-focus-inner {
            border: 0;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
            padding: 0;
            font-weight: 700;
            color: #202342;
            font-family: 'Muli', sans-serif;
        }

        img {
            border: 0;
            vertical-align: top;
            max-width: 100%;
            height: auto;
        }

        ul,
        ol {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        p {
            margin: 0 0 15px 0;
            padding: 0;
        }

        .container-fluid {
            max-width: 1900px;
        }

        /* Common Class */
        .pd-5 {
            padding: 5px;
        }

        .pd-10 {
            padding: 10px;
        }

        .pd-20 {
            padding: 20px;
        }

        .pd-30 {
            padding: 30px;
        }

        .pb-10 {
            padding-bottom: 10px;
        }

        .pb-20 {
            padding-bottom: 20px;
        }

        .pb-30 {
            padding-bottom: 30px;
        }

        .pt-10 {
            padding-top: 10px;
        }

        .pt-20 {
            padding-top: 20px;
        }

        .pt-30 {
            padding-top: 30px;
        }

        .pr-10 {
            padding-right: 10px;
        }

        .pr-20 {
            padding-right: 20px;
        }

        .pr-30 {
            padding-right: 30px;
        }

        .pl-10 {
            padding-left: 10px;
        }

        .pl-20 {
            padding-left: 20px;
        }

        .pl-30 {
            padding-left: 30px;
        }

        .px-30 {
            padding-left: 30px;
            padding-right: 30px;
        }

        .px-20 {
            padding-left: 20px;
            padding-right: 20px;
        }

        .py-30 {
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .py-20 {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .mb-30 {
            margin-bottom: 30px;
        }

        .mb-50 {
            margin-bottom: 50px;
        }

        .font-30 {
            font-size: 30px;
            line-height: 1.46em;
        }

        .font-24 {
            font-size: 24px;
            line-height: 1.5em;
        }

        .font-20 {
            font-size: 20px;
            line-height: 1.5em;
        }

        .font-18 {
            font-size: 18px;
            line-height: 1.6em;
        }

        .font-16 {
            font-size: 16px;
            line-height: 1.75em;
        }

        .font-14 {
            font-size: 14px;
            line-height: 1.85em;
        }

        .font-12 {
            font-size: 12px;
            line-height: 2em;
        }

        .weight-300 {
            font-weight: 300;
        }

        .weight-400 {
            font-weight: 400;
        }

        .weight-500 {
            font-weight: 500;
        }

        .weight-600 {
            font-weight: 600;
        }

        .weight-700 {
            font-weight: 700;
        }

        .weight-800 {
            font-weight: 800;
        }

        .text-blue {
            color: #07023d;
        }

        .text-dark {
            color: #000000;
        }

        .text-white {
            color: #ffffff;
        }

        .height-100-p {
            height: 100%;
        }

        .bg-white {
            background: #ffffff;
        }

        .border-radius-10 {
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
        }

        .border-radius-100 {
            -webkit-border-radius: 100%;
            -moz-border-radius: 100%;
            border-radius: 100%;
        }

        .box-shadow {
            -webkit-box-shadow: 0px 0px 28px rgba(0, 0, 0, .08);
            -moz-box-shadow: 0px 0px 28px rgba(0, 0, 0, .08);
            box-shadow: 0px 0px 28px rgba(0, 0, 0, .08);
        }

        .gradient-style1 {
            background-image: linear-gradient(135deg, #43CBFF 10%, #9708CC 100%);
        }

        .gradient-style2 {
            background-image: linear-gradient(135deg, #72EDF2 10%, #5151E5 100%);
        }

        .gradient-style3 {
            background-image: radial-gradient(circle 732px at 96.2% 89.9%, rgba(70, 66, 159, 1) 0%, rgba(187, 43, 107, 1) 92%);
        }

        .gradient-style4 {
            background-image: linear-gradient(135deg, #FF9D6C 10%, #BB4E75 100%);
        }

        /* widget style 1 */

        .widget-style1 {
            padding: 20px 10px;
        }

        .widget-style1 .circle-icon {
            width: 60px;
        }

        .widget-style1 .circle-icon .icon {
            width: 60px;
            height: 60px;
            background: #ecf0f4;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .widget-style1 .widget-data {
            width: calc(100% - 150px);
            padding: 0 15px;
        }

        .widget-style1 .progress-data {
            width: 90px;
        }

        .widget-style1 .progress-data .apexcharts-canvas {
            margin: 0 auto;
        }

        .widget-style2 .widget-data {
            padding: 20px;
        }

        .widget-style3 {
            padding: 30px 20px;
        }

        .widget-style3 .widget-data {
            width: calc(100% - 60px);
        }

        .widget-style3 .widget-icon {
            width: 60px;
            font-size: 45px;
            line-height: 1;
        }

        .apexcharts-legend-marker {
            margin-right: 6px !important;
        }
    </style>

    <div class="mt-4">
        {{-- <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                <h2>{{ $page_title }}</h2>
            </div>
        </div> --}}

        
        <div class="row">
            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-children"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $totalStudents }}</div>
                            <div class="weight-500">Total Students</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-people-group"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $totalStaffs }}</div>
                            <div class="weight-500">Total Staffs</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-child-dress"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $totalGirls }}</div>
                            <div class="weight-500">Total Girls</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-child"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $totalBoys }}</div>
                            <div class="weight-500">Total Boys</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-clipboard-user"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $presentStudents }}</div>
                            <div class="weight-500">Present Students</div>
                        </div>
                        <div class="progress-data">
                            <div id="chart3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-xmark"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $absentStudents }}</div>
                            <div class="weight-500">Absent Students</div>
                        </div>
                        <div class="progress-data">
                            <div id="chart2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-user"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $presentStaffs }}</div>
                            <div class="weight-500">Present Staffs</div>
                        </div>
                        <div class="progress-data">
                            <div id="chart3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-50">
                <div class="bg-white widget-style1 border-radius-10 height-100-p box-shadow">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-user-minus"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $absentStaffs }}</div>
                            <div class="weight-500">Absent Staffs</div>
                        </div>
                        <div class="progress-data">
                            <div id="chart2"></div>
                        </div>
                    </div>
                </div>
            </div>


           

            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-child-dress"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $presentGirls }}</div>
                            <div class="weight-500">Present Girls</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-child"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $presentBoys }}</div>
                            <div class="weight-500">Present Boys</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-xmark"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $absentGirls }}</div>
                            <div class="weight-500">Absent Girls</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-50">
                <div class="bg-white box-shadow border-radius-10 height-100-p widget-style1">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="circle-icon">
                            <div class="icon border-radius-100 font-24 text-blue"><i class="fa-solid fa-xmark"></i></div>
                        </div>
                        <div class="widget-data">
                            <div class="weight-800 font-18">{{ $absentBoys }}</div>
                            <div class="weight-500">Absent Boys</div>
                        </div>
                    </div>
                </div>
            </div>   
        </div>
        


        {{-- <div class="card mb-4">
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
        </div> --}}
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
                        <span class="fw-bold">School Wise Student's Attendance</span>
                        <div class="bg-gray pt-5">
                            <canvas id="schoolAttendanceChart" width="600" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 mt-4 ">
                        <span class="fw-bold">School Wise Staff's Attendance</span>
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
        // var school_student_count = @json($school_students_count);
        // var school_staffs_count = @json($school_staffs_count);
        // var school_wise_student_attendances = @json($school_wise_student_attendences);
        // var school_wise_staff_attendances = @json($school_wise_staffs_attendences);
        // var class_wise_students = @json($class_wise_students);

        // // Function to calculate and update summary counts
        // function updateSummaryCounts() {
        //     let totalStudents = 0;
        //     let presentStudents = 0;
        //     let absentStudents = 0;
        //     let totalStaffs = 0;
        //     let presentStaffs = 0;
        //     let absentStaffs = 0;

        //     school_wise_student_attendances.forEach(item => {
        //         totalStudents += item.total_student;
        //         presentStudents += item.present_student;
        //         absentStudents += item.absent_student;
        //     });

        //     school_wise_staff_attendances.forEach(item => {
        //         totalStaffs += item.total_staffs;
        //         presentStaffs += item.present_staffs;
        //         absentStaffs += item.absent_staffs;
        //     });

        //     document.getElementById('totalStudents').innerText = totalStudents;
        //     document.getElementById('presentStudents').innerText = presentStudents;
        //     document.getElementById('absentStudents').innerText = absentStudents;
        //     document.getElementById('totalStaffs').innerText = totalStaffs;
        //     document.getElementById('presentStaffs').innerText = presentStaffs;
        //     document.getElementById('absentStaffs').innerText = absentStaffs;
        // }

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
            var schoolNames = school_wise_student_attendances.map(item => item.school_name);
            var totalStudent = school_wise_student_attendances.map(item => item.total_student);
            var presentStudents = school_wise_student_attendances.map(item => item.present_student);
            var absentStudents = school_wise_student_attendances.map(item => item.absent_student);

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
            var schoolNames = school_wise_staff_attendances.map(item => item.school_name);
            var totalStaffs = school_wise_staff_attendances.map(item => item.total_staffs);
            var presentStaffs = school_wise_staff_attendances.map(item => item.present_staffs);
            var absentStaffs = school_wise_staff_attendances.map(item => item.absent_staffs);
            var holidayStaffs = school_wise_staff_attendances.map(item => item.holiday_staffs);

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