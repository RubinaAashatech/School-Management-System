@can('edit_designations')
    <a href="#" class="btn btn-outline-primary btn-sm mx-1 edit-designation" data-id="{{ $designation->id }}"
        data-name="{{ $designation->name }}" data-is_active="{{ $designation->is_active }}" data-toggle="tooltip"
        data-placement="top" title="Edit">
        <i class="fa fa-edit"></i>
    </a>
@endcan

@can('delete_designations')
    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
        data-bs-target="#delete{{ $designation->id }}" data-toggle="tooltip" data-placement="top" title="Delete">
        <i class="far fa-trash-alt"></i>
    </button>
    <div class="modal fade" id="delete{{ $designation->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST" action="{{ route('admin.designations.destroy', $designation->id) }}"
                    accept-charset="UTF-8" method="POST">
                    <div class="modal-body">

                        <input name="_method" type="hidden" value="DELETE">
                        <input name="_token" type="hidden" value="{{ csrf_token() }}">

                        <p>Are you sure to delete <span class="must" id="underscore"> {{ $designation->name }} </span>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-danger">Yes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endcan
