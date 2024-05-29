@extends('backend.layouts.master')

<!-- Main content -->
@section('content')
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">

            <div class="border-bottom border-primary">
                <h2>
                    {{ $page_title }}
                </h2>
            </div>

        </div>
        <div class="card mb-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="row">
                            <div class="col-lg-3 col-sm-3">
                                <label for="class_id">Class:</label>
                                <input type="text" class="form-control" value="{{ $className }}" disabled>
                            </div>

                            <div class="col-lg-3 col-md-3">
                                <label for="section_id">Section:</label>
                                <input type="text" class="form-control" value="{{ $sectionName }}" disabled>
                            </div>
                        </div>

                        <div class="mt-4" id="ajax_response">

                        </div>
                    </div>
                    {{-- <div class="form-group col-md-12 d-flex justify-content-end pt-2">
                                <button type="submit" class="btn btn-success" id="submit">Submit</button>
                            </div> --}}

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createExamMarks" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Enter Marks Obtained By Students </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="examheight100 relative">
                        <div class="marksEntryForm">
                            <div class="divider"></div>
                            <div class="row">
                                <div class="col-md-9">
                                    <form method="POST" enctype="multipart/form-data" id="fileUploadForm">

                                        <div class="input-group mb10">
                                            <div class="dropify-wrapper" style="height: 35.1111px;">
                                                Import Marks:
                                                <input id="my-file-selector" data-height="34" class="dropify"
                                                    type="file">
                                            </div>
                                            <div class="input-group-btn">
                                                <input type="submit" class="btn btn-sm btn-success mt-2" value="Submit"
                                                    id="btnSubmit">
                                            </div>
                                        </div>
                                    </form>
                                </div>


                                <div class="col-md-3">
                                    <a class="btn btn-primary pull-right" href="#" target="_blank"><i
                                            class="fa fa-download"></i> Export Sample</a>
                                </div>
                            </div>
                            <hr>

                            <form method="post" action="{{ route('admin.students-mark.save') }}" id="student_marks">
                                @csrf
                                <div class="row" id="students_details">

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var examinationId = '{{ $examinations->id }}';
            var classId = '{{ $classId }}';
            var sectionId = '{{ $sectionId }}';
            var examScheduleId = '{{ $examSchedule }}';

            console.log(examinationId);
            console.log(classId);
            console.log(sectionId);
            console.log('Exam Schedule', examScheduleId);

            fetchSubjects(classId, sectionId, examinationId);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Define a function to fetch subjects based on class ID and section ID
            function fetchSubjects(classId, sectionId, examinationId) {
                $.ajax({
                    url: '{{ url('admin/exam-results/get-routine-wise-subject/class-section-and-examination') }}',
                    type: 'GET',
                    data: {
                        class_id: classId,
                        sections: sectionId,
                        examination_id: examinationId
                    },
                    success: function(data) {
                        $('#ajax_response').empty();
                        if (data.message) {
                            alert(data.message);
                        } else {
                            $('#ajax_response').empty();
                            $('#ajax_response').html(data);

                            //               // Extract and use the subject name
                            // var subjectName = data.subject;
                            // console.log('Subject Name:', subjectName);

                            // You can now use the subjectName variable as needed in your script
                            // For example, update some UI element with the subject name
                            // $('#some_element').text(subjectName);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#ajax_response').empty();
                        if (xhr.status === 400) {
                            var errorMessage = JSON.parse(xhr.responseText).message;
                            toastr.error(errorMessage);
                        } else {

                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            }

            // Define a function to fetch student details based on student assigned to examination by class ID and section ID
            function fetchStudentDetails(classId, sectionId, subjectId, examId, examScheduleId) {
                $.ajax({
                    url: '{{ url('admin/exam-results/get-students-by-class-section-subject-and-examination') }}',
                    type: 'POST',
                    data: {
                        class_id: classId,
                        section_id: sectionId,
                        subject_id: subjectId,
                        examination_id: examId,
                        examination_schedule_id: examScheduleId
                    },
                    success: function(data) {
                        $('#students_details').empty();
                        if (data.message) {
                            alert(data.message);
                        } else {
                            $('#students_details').empty();
                            $('#students_details').html(data);

                        }
                    },
                    error: function(xhr, status, error) {
                        $('#students_details').empty();
                        if (xhr.status === 400) {
                            var errorMessage = JSON.parse(xhr.responseText).message;
                            toastr.error(errorMessage);
                        } else {

                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            }


            $(document).on('click', '.assignMarks', function() {

                var examScheduleId = this.dataset.exam_schedule_id;
                var classId = this.dataset.class_id;
                var sectionId = this.dataset.section_id;
                var subjectId = this.dataset.subject_id;
                var examId = this.dataset.exam_id;
                var subjectGroupId = this.dataset.subject_group_id;



                // Log the retrieved values to the console (you can replace this with your desired functionality)
                console.log('Exam ID:', examId);
                console.log('Subject ID:', subjectId);
                console.log('Class ID:', classId);
                console.log('Section ID:', sectionId);
                console.log('Subject Group ID:', subjectGroupId);




                fetchStudentDetails(classId, sectionId, subjectId, examId, examScheduleId);
                $('#createExamMarks').modal('show');
            });

            $(document).on('click', '.attendance_chk', function() {
                var isChecked = $(this).prop('checked');

                // Check if the checkbox is checked
                if (isChecked) {
                    $(this).closest('tr').find('.participant_assessment').prop('disabled', true).val(0);
                    $(this).closest('tr').find('.practical_assessment').prop('disabled', true).val(0);
                    $(this).closest('tr').find('.theory_assessment').prop('disabled', true).val(0);
                } else {
                    $(this).closest('tr').find('.participant_assessment').prop('disabled', false).val('');
                    $(this).closest('tr').find('.practical_assessment').prop('disabled', false).val('');
                    $(this).closest('tr').find('.theory_assessment').prop('disabled', false).val('');
                }
            });


        });
    </script>
@endsection
