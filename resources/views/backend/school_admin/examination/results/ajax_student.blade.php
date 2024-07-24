<div class="col-md-12">
    <input type="hidden" name="exam_schedule_id" value="{{ $examinationScheduleId }}">
    <input type="hidden" name="subject_id" value="{{ $subjectId }}">
    <div class="table-responsive">
        <h4>{{ $subjectName }}</h4>
        <table id="subjectTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Admission No</th>
                    <th>Roll Number</th>
                    <th>Student Name</th>
                    <th>Father Name</th>
                    <th>Gender</th>
                    <th>Attendance</th>
                    <th>Participant Assessment</th>
                    <th>Practical/Project Assessment</th>
                    <th>Theory Assessment</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($examStudents as $index => $examStudent)
                    <tr class="std_adm_{{ $examStudent->studentSession->student->admission_no }}">
                        <input type="hidden" name="student_id[{{ $index }}]" value="{{ $examStudent->studentSession->student->id }}">
                        <input type="hidden" name="exam_student_id[{{ $index }}]" value="{{ $examStudent->id }}">
                        <input type="hidden" name="student_session_id[{{ $index }}]" value="{{ $examStudent->student_session_id }}">
                        <td>{{ $examStudent->studentSession->student->admission_no }}</td>
                        <td>{{ $examStudent->studentSession->student->roll_no }}</td>
                        <td>{{ $examStudent->studentSession->student->user->f_name . ' ' . $examStudent->studentSession->student->user->m_name . ' ' . $examStudent->studentSession->student->user->l_name }}</td>
                        <td>{{ $examStudent->studentSession->student->user->father_name }}</td>
                        <td>{{ $examStudent->studentSession->student->user->gender }}</td>
                        <td>
                            <div>
                                <input type="hidden" name="attendance[{{ $index }}]" value="1">
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="attendance_chk" name="attendance[{{ $index }}]" value="0" {{ $examStudent->is_active ? '' : 'checked' }}>
                                    Present
                                </label>
                            </div>
                        </td>
                        <td>
                            <input type="number" class="participant_assessment form-control" name="participant_assessment[{{ $index }}]" value="{{ $examStudent->participant_assessment ?? '' }}" step="any">
                        </td>
                        <td>
                            <input type="number" class="practical_assessment form-control" name="practical_assessment[{{ $index }}]" value="{{ $examStudent->practical_assessment ?? '' }}" step="any">
                        </td>
                        <td>
                            <input type="number" class="theory_assessment form-control" name="theory_assessment[{{ $index }}]" value="{{ $examStudent->theory_assessment ?? '' }}" step="any">
                        </td>
                        <td>
                            <input type="text" class="form-control note" name="notes[]" value="{{ $examStudent->notes ?? '' }}">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-top col-md-12 d-flex justify-content-end p-2">
        <button type="submit" class="btn btn-sm btn-success mt-2">Submit</button>
        <button id="exportCsv" class="btn btn-sm btn-primary mt-2 ml-2">Export to CSV</button>
    </div>
</div>

<script>
$(document).ready(function() {
    // Function to toggle input fields
    function toggleInputFields(row, isEnabled) {
        row.find('input.participant_assessment, input.practical_assessment, input.theory_assessment, input.note')
           .prop('disabled', !isEnabled);
    }

    // Handle checkbox change
    $('.attendance_chk').change(function() {
        var row = $(this).closest('tr');
        var isPresent = $(this).is(':checked');
        
        // Update the hidden attendance input
        row.find('input[name^="attendance"][type="hidden"]').val(isPresent ? '0' : '1');

        // Enable input fields only if the student is present
        toggleInputFields(row, isPresent);
    });

    // Initial setup
    $('.attendance_chk').each(function() {
        var row = $(this).closest('tr');
        var isPresent = $(this).is(':checked');
        
        // Set initial state of input fields based on attendance
        toggleInputFields(row, isPresent);
    });

    function downloadCSV(csv, filename) {
        var csvFile;
        var downloadLink;

        // CSV FILE
        csvFile = new Blob([csv], { type: 'text/csv' });

        // DOWNLOAD LINK
        downloadLink = document.createElement("a");

        // File name
        downloadLink.download = filename;

        // Create a link to the file
        downloadLink.href = window.URL.createObjectURL(csvFile);

        // Hide the link
        downloadLink.style.display = "none";

        // Add the link to the DOM
        document.body.appendChild(downloadLink);

        // Click the link to download the file
        downloadLink.click();
    }

    function exportTableToCSV(filename) {
        var csv = [];
        var table = document.getElementById("subjectTable");
        var rows = table.querySelectorAll("tr");

        // Loop through each row
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var cols = row.querySelectorAll("td, th");
            var csvRow = [];

            // Loop through each column
            for (var j = 0; j < cols.length; j++) {
                csvRow.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
            }

            csv.push(csvRow.join(","));
        }

        // Download CSV
        downloadCSV(csv.join("\n"), filename);
    }

    // Event listener for the export button
    $("#exportCsv").on("click", function() {
        exportTableToCSV('subject_marks.csv');
    });
});
</script>
