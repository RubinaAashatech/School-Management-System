@hasanyrole('Municipality Admin')
    <li class="nav-item">
        {{-- <hr class="my-4"> --}}
        <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
            <div class="col-auto navbar-vertical-label">{{__('Reports Management')}}</div>
            <div class="col ps-0">
                <hr class="mb-0 navbar-vertical-divider">
            </div>
        </div>
    <li class="nav-item">
        <a class="nav-link dropdown-indicator" href="#dashboard13" role="button" data-bs-toggle="collapse" aria-expanded="true"
            aria-controls="dashboard">
            <div class="d-flex align-items-center"><span class="nav-link-icon"><i
                        class="fas fa-credit-card"></i></span><span class="nav-link-text ps-1">{{ __("Attendence report")}}
                </span></div>
        </a>
        <ul class="nav collapse  {{ Request::segment(2) == 'attendence-report'? 'show' : '' }}"
            id="dashboard13">
            @can('list_attendence_report')
                <li class="nav-item"><a class="nav-link {{ Request::segment(2) == 'extracurricular-head' ? 'active' : '' }}"
                        href="{{ route('admin.attendance_reports.report') }}">
                        <div class="d-flex align-items-center"><i class="fa fa-angle-double-right"></i> {{ __('Attendence report')}}

                        </div>
                    </a>
                </li>
            @endcan
        </ul>
    </li>

 @endhasanyrole