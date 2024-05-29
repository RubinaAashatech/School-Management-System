    <div class="modal fade" id="marksheetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="modalContent" style="background-image: url(''); background-size: cover;">

                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body">

                    <section class="gradesheet">
                        <div class="container">
                            <div class="gradesheet_design">
                                <div class="row gradesheet_head">
                                    <div class="col-2">
                                        <img class="gradesheet_logo" src="{{ asset($school->logo) }}" alt="logo">
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

                                    <div class="col-5 output text-center">
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
                                                <th scope="col" class="input">Marks Distribution</th>
                                                <th scope="col" class="input">Marks Distribution</th>
                                                <th scope="col" class="input credit">TOTAL</th>
                                                <th scope="col" class="input grade">GRADE POINT</th>
                                                <th scope="col" class="input grade">GRADE</th>
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
                                                    // dd($resultData);
                                                @endphp
                                                <tr>
                                                    <th scope="row" class="text-center">001</th>
                                                    <th scope="row" class="text-center">
                                                        {{ $routine ? $routine->subject : '-' }}
                                                    </th>
                                                    <td>
                                                        Participant Marks<br>
                                                        Practical Marks<br>
                                                        Theory Marks
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $resultData['examResult'] ? $resultData['examResult']->participant_assessment : '-' }}<br>
                                                        {{ $resultData['examResult'] ? $resultData['examResult']->practical_assessment : '-' }}<br>
                                                        {{ $resultData['examResult'] ? $resultData['examResult']->theory_assessment : '-' }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $resultData['examResult'] ? $resultData['examResult']->partial_sum : '-' }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $resultData['grade'] ? $resultData['grade']->grade_points_to : '-' }}
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
                                    <div class="col-3 footdd-input">
                                        <em class="input">Date Of Issue:</em> {{ $today->format('Y-m-d') }}
                                    </div>
                                    <div class="col-3 foot-input">
                                        <p>{{ $school->head_teacher }}</p>
                                        <p>APPROVED BY</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>
                </div>

            </div>
        </div>
    </div>
    </div>
    {{-- @endforeach --}}
    {{-- MODAL FOR MARKSHEET DESIGN END --}}
