<div>
    <a href="{{ url()->previous() }}"><button class="btn-primary btn-sm"><i class="fa fa-angle-double-left"></i>
            Back</button></a>
    @can('list_student_leaverequests')
        <a href="{{ route('admin.student-leaverequests.index') }}"><button class="btn-info btn-sm">All List <i
                    class="fa fa-list"></i></button></a>
    @endcan
    @can('create_student_leaverequests')
        <a href="#">
            <button type="button" class="btn btn-block btn-success btn-sm" data-bs-toggle="modal"
                data-bs-target="#createLeaveType">
                Add Student Leave <i class="fas fa-plus"></i>
            </button>
        </a>
    @endcan

</div>
