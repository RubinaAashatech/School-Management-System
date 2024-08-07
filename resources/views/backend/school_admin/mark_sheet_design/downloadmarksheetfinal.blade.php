<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gradeshee</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        @import url('https://fonts.cdnfonts.com/css/algeria');

        :root {
            --input-color: rgb(39, 60, 223);
        }

        .s_name,
        .s_exam,
        .s_estd,
        .s_sheet {
            font-family: "Times New Roman", Times, serif;
            font-size: 25px;
            font-weight: bolder;
        }

        .s_address,
        .s_state {
            font-family: "Times New Roman", Times, serif;
            font-size: 15px;
            font-weight: bolder;
        }

        .gradesheet {
            border: 5px solid var(--input-color);

        }

        .gradesheet_design {
            border: 2px solid var(--input-color);
            padding: 20px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .gradesheet_logo {
            margin-top: 55px;
            height: 150px;
        }

        .s_name,
        .s_address,
        .s_state,
        .s_estd,
        .s_exam,
        .s_sheet,
        .input,
        .first-input,
        .interval-grades,
        .one-credit,
        .foot-input {
            color: var(--input-color);
        }

        .foot-input {
            border-top: 1px dashed var(--input-color);
            /* margin: 0px 5px; */
        }

        .one-credit {
            line-height: 30px;
        }

        .interval-grades {
            height: 40px;
            line-height: 1px;
        }

        .output {
            border-bottom: 1px dashed var(--input-color);
        }

        .first-input {
            font-weight: bold;
        }

        .first-input,
        .output {
            /* border: 1px solid red; */
            padding: 0px 0px;
            height: 25px;
        }

        .credit,
        .grade {
            border: 1px solid red;
            width: 10px;
        }

        .s_sheet {
            font-family: 'Algeria', sans-serif;
            font-size: 40px;
        }
    </style>
</head>

<body>
    <section class="gradesheet">
        <div class="container">
            <div class="gradesheet_design">
                <div class="row gradesheet_head">
                    <div class="col-2">
                        {{-- <img class="gradesheet_logo" src="{{ asset($school->logo) }}" alt="logo"> --}}
                    </div>
                    <div class="text-center col-8">
                        <p>
                            <span class="s_name">{{ $school->name }}</span><br>
                            <span class="s_address">{{ $school->address }}</span><br>
                            <span class="s_state">{{ $school->district->name }}, Nepal</span><br>
                            <span class="s_estd">Estd: 2033 --</span><br>
                            <span class="s_exam">{{ $examinations->exam }}</span><br>
                            <span class="s_sheet">GRADE - SHEET</span>
                        </p>
                    </div>

                    <div class="col-2">

                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-4 first-input">
                        <p>THE GRADE SECURED BY:</p>
                    </div>
                    <div class="col-8 output text-center">
                        <p>{{ $studentSessions->f_name . ' ' . $studentSessions->m_name . ' ' . $studentSessions->l_name }}
                        </p>
                    </div>

                    <div class="col-3 first-input">
                        <p>CHILD OF</p>
                    </div>
                    <div class="col-3 output text-center">
                        <p>{{ $studentSessions->father_name }}</p>
                    </div>
                    <div class="col-3 first-input">
                        <p>AND</p>
                    </div>
                    <div class="col-3 output text-center">
                        <p>{{ $studentSessions->mother_name }}</p>
                    </div>

                    <div class="col-md-2 first-input">
                        <p>DOB:</p>
                    </div>

                    <div class="col-2 output text-center">
                        <p>{{ $studentSessions->dob }}</p>
                    </div>

                    <div class="col-2 first-input">
                        <p>ROLL NO: </p>
                    </div>

                    <div class="col-2 output text-center">
                        <p>{{ $studentSessions->roll_no }}</p>
                    </div>

                    <div class="col-2 first-input">
                        <p>GRADE:</p>
                    </div>

                    <div class="col-2 output text-center">
                        <p>{{ $studentSessions->class }} </p>
                    </div>

                    <div class="col-5 first-input">
                        {{-- <p>IN THE ANNUAL EXAMINATION CONDUCTED IN:</p> --}}
                        <p>IN THE {{ strtoupper($examinations->exam) }}</p>
                    </div>

                    <div class="col-4 first-input">
                        <p>ARE GIVEN BELOW.</p>
                    </div>
                </div>


                <div class="row">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" class="input">S.N.</th>
                                <th scope="col" class="input">SUBJECTS</th>
                                <th scope="col" class="input credit">CREDIT HOUR</th>
                                <th scope="col" class="input grade">GRADE POINT</th>
                                <th scope="col" class="input">GRADE</th>
                                <th scope="col" class="input">FINAL GRADE</th>
                                <th scope="col" class="input">REMARKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($examinations->subjectByRoutine as $routine)
                                @php
                                    $resultData = $studentSessions->SubjectWiseExamResults(
                                        $examinations,
                                        $routine->id,
                                        $studentSessions,
                                    );
                                    // $credit_hour = $studentSessions->getCreditHour(
                                    //     $examinations,
                                    //     $routine->id,
                                    //     $studentSessions,
                                    // );
                                @endphp
                                <tr>
                                    <th scope="row" class="text-center">001</th>
                                    <td>
                                        {{ $routine ? $routine->subject : '-' }} (TH)<br>
                                        {{ $routine ? $routine->subject : '-' }} (IN)
                                    </td>
                                    <td>
                                        {{ $resultData['creditHour'] ? $resultData['creditHour']->credit_hour / 2 : '-' }}
                                        <br>
                                        {{ $resultData['creditHour'] ? $resultData['creditHour']->credit_hour / 2 : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $resultData['examResult'] ? $resultData['examResult']->internal_grade_point : '-' }}<br>
                                        {{ $resultData['examResult'] ? $resultData['examResult']->external_grade_point : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $resultData['examResult'] ? $resultData['examResult']->internal_grade_name : '-' }}<br>
                                        {{ $resultData['examResult'] ? $resultData['examResult']->external_grade_name : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $resultData['grade'] ? $resultData['grade']->grade_name : '-' }}
                                    </td>
                                    <td>
                                        {{ $resultData['grade'] ? $resultData['grade']->achievement_description : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>

                                <td class="text-right" colspan="7">
                                    <b><span class="input">GRADE POINT AVERAGE (GPA):</span>
                                        {{ $studentSessions->GPACalculation() }}</b>
                                </td>

                            </tr>
                        </tfoot>
                    </table>
                </div>


                <div class="row">
                    <div class="col-6 ">
                        <p class="one-credit">
                            1. One Credit Hour Equals To 32 Working Hours. <br>
                            2. INTERNAL <b>(IN)</b>: This Covers The Participation Practical/Project
                            Works & Terminal Examination.<br>
                            3. EXTERNAL <b>(TH)</b>: This Covers Written External Examination.<br>
                            4. <b>ABS</b>: Absent &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 5. <b>*NG</b> :
                            Not Graded<br>
                            6. <b>GPA</b> = Σ ( Credit Hour × Grade Point )/Total Credit Hour Of The
                            Grade

                        </p>
                        <p>
                            <span class="input"> Attendance:</span> 66.82% <br>
                            <span class="input">Result:</span> Upgraded

                        </p>
                    </div>

                    <div class="col-6">
                        <table class="table table-bordered interval-grades">
                            <thead>
                                <p class="text-center  input"> Interval And Grades</p>
                                <tr>
                                    <th scope="col">S.N.</th>
                                    <th scope="col">Interval In %</th>
                                    <th scope="col">GRADE</th>
                                    <th scope="col">Detail</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($markgrades as $grade)
                                    <tr>
                                        <th scope="row">{{ $grade->id }}</th>
                                        <td>
                                            {{ $grade->percentage_from }} -
                                            {{ $grade->percentage_to }}
                                        </td>
                                        <td>
                                            {{ $grade->grade_name }}
                                        </td>
                                        <td>
                                            {{ $grade->achievement_description }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="row text-center mt-5 justify-content-around">
                    <div class="col-3 foot-input">
                        <p>TARANATH KHANAL</p>
                        <p>PREPARED BY</p>
                    </div>
                    <div class="col-3">
                        <em class="date-input">Date Of Issue:</em> {{ $today->format('Y-m-d') }}
                    </div>
                    <div class="col-3 foot-input">
                        <p>{{ $school->head_teacher }}</p>
                        <p>APPROVED BY</p>
                    </div>
                </div>
            </div>
        </div>

    </section>
</body>

</html>
