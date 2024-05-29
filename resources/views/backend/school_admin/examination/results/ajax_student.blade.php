<div class="col-md-12">
    <input type="hidden" name="exam_schedule_id" value="{{ $examinationScheduleId }}">
    <input type="hidden" name="subject_id" value="{{ $subjectId }}">

    <div class="table-responsive">
        <h4>
            {{ $subjectName }}
        </h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Admission No</th>
                    <th>Roll Number</th>
                    <th>Student Name</th>
                    <th>Father Name</th>
                    {{-- <th>Category(Reserve Quota)</th> --}}
                    <th>Gender</th>
                    <th>Attendance</th>
                    <th>Participant Assessment</th>
                    <th>Practical/Project Assessment</th>
                    <th>Theory Assessment</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($examStudent as $index => $student)
                    <tr class="std_adm_326260">
                        <input type="hidden" name="student_id[{{ $index }}]"
                            value="{{ $student->student->id }}">
                        <input type="hidden" name="exam_student_id[{{ $index }}]"
                            value="{{ $student->exam_student_id }}">
                        <input type="hidden" name="student_session_id[{{ $index }}]"
                            value="{{ $student->id }}">
                        <td>{{ $student->student->user_id }}</td>
                        <td>{{ $student->student->roll_no }}</td>
                        <td>{{ $student->user->f_name . ' ' . $student->user->m_name . ' ' . $student->user->l_name }}
                        </td>
                        <td>{{ $student->user->father_name }}</td>
                        <td>{{ $student->user->gender }}</td>
                        <td>
                            <div>
                                <input type="hidden" name="attendance[{{ $index }}]" value="1">
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="attendance_chk"
                                        name="attendance[{{ $index }}]" value="0"
                                        {{ $student->attendance === '0' ? 'checked' : '' }}>
                                    Absent
                                </label>

                            </div>
                        </td>
                        <td>
                            <input type="number" class="participant_assessment form-control"
                                name="participant_assessment[{{ $index }}]"
                                value="{{ $student->participant_assessment }}" step="any">
                        </td>
                        <td>
                            <input type="number" class="practical_assessment form-control"
                                name="practical_assessment[{{ $index }}]"
                                value="{{ $student->practical_assessment }}" step="any">
                        </td>
                        <td>
                            <input type="number" class="theory_assessment form-control"
                                name="theory_assessment[{{ $index }}]"
                                value="{{ $student->theory_assessment }}" step="any">
                        </td>
                        <td> <input type="text" class="form-control note" name="notes[]"
                                value="{{ $student->notes }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="border-top col-md-12 d-flex justify-content-end p-2">
        <button type="submit" class="btn btn-sm btn-success mt-2">Submit</button>

    </div>
    <br>
    <br>
</div>
