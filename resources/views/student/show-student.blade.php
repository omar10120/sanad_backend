@extends('layouts.master')
@section('title'){{trans('main_trans.Student_details')}}@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{trans('main_trans.Students')}}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/{{trans('main_trans.Student_details')}}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
@include('components.flash-messages')

<div class="row">
    <!-- Student Profile Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                @if($student->photo)
                    <img src="{{URL::asset('assets/image/Students/'.$student->id.'/'.$student->photo)}}"
                         alt="Student Photo"
                         class="rounded-circle avatar-xl mb-3">
                @else
                    <img src="{{URL::asset('assets/image/sanad.jpg')}}"
                         alt="Default Photo"
                         class="rounded-circle avatar-xl mb-3">
                @endif

                <h5 class="card-title">{{$student->first_name}} {{$student->father_name}} {{$student->last_name}}</h5>
                <p class="text-muted">{{$student->email}}</p>

                <div class="row text-center mt-3">
                    <div class="col-6">
                        @if(config('features.student_devices'))
                        <h6 class="mb-0">{{$student->getActiveDevicesCount()}}</h6>
                        @else
                        <h6 class="mb-0 text-muted">-</h6>
                        @endif
                        <small class="text-muted">{{trans('main_trans.Active_devices')}}</small>
                    </div>
                    <div class="col-6">
                        @if(config('features.student_devices'))
                        <h6 class="mb-0">{{$student->max_devices}}</h6>
                        @else
                        <h6 class="mb-0 text-muted">-</h6>
                        @endif
                        <small class="text-muted">{{trans('main_trans.Max_devices')}}</small>
                    </div>
                </div>

                <div class="mt-3">
                    @if($student->status == 1)
                        <span class="badge badge-success">{{trans('main_trans.Active')}}</span>
                    @else
                        <span class="badge badge-danger">{{trans('main_trans.Inactive')}}</span>
                    @endif
                </div>

                <div class="mt-3">
                    @can('Student-edit')
                        <a href="{{route('student.edit', $student->id)}}" class="btn btn-primary btn-sm">
                            {{trans('main_trans.Edit_student')}}
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Student Details -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">{{trans('main_trans.Basic_information')}}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">{{trans('main_trans.Phone')}}:</label>
                        <p>{{$student->phone}}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">{{trans('main_trans.City')}}:</label>
                        <p>{{trans('main_trans.'.$student->city)}}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">{{trans('main_trans.School')}}:</label>
                        <p>{{$student->school ?: trans('main_trans.Not_specified')}}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">{{trans('main_trans.Certificate_type')}}:</label>
                        <p>{{$student->type->name ?? trans('main_trans.Not_assigned')}}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Management -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    {{trans('main_trans.Device_management')}}
                    @if(!config('features.student_devices'))
                    <span class="badge badge-warning ml-2" style="font-size: 10px;">
                        <i class="fas fa-crown"></i> {{ trans('main_trans.Pro_Only') }}
                    </span>
                    @endif
                </h6>
            </div>
            <div class="card-body">
                @if(!config('features.student_devices'))
                    <!-- Pro Feature Overlay -->
                    <div class="text-center py-4">
                        <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                        <h5>{{ trans('main_trans.Pro_Feature_Title') }}</h5>
                        <p class="text-muted">{{ trans('main_trans.Pro_Feature_Message') }}</p>
                    </div>
                @elseif($student->studentDevices->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{trans('main_trans.Device_info')}}</th>
                                    <th>{{trans('main_trans.OS')}}</th>
                                    <th>{{trans('main_trans.First_login')}}</th>
                                    <th>{{trans('main_trans.Last_login')}}</th>
                                    <th>{{trans('main_trans.Status')}}</th>
                                    <th>{{trans('main_trans.Actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->studentDevices as $studentDevice)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>
                                                {{$studentDevice->device->brand}}
                                                {{$studentDevice->device->model}}
                                            </strong>
                                            <br>
                                            <small class="text-muted">
                                                {{$studentDevice->device->device_id}}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        {{$studentDevice->device->os_name}}
                                        {{$studentDevice->device->os_version}}
                                    </td>
                                    <td>
                                        {{$studentDevice->first_login_at ? $studentDevice->first_login_at->format('Y-m-d H:i') : '-'}}
                                    </td>
                                    <td>
                                        {{$studentDevice->last_login_at ? $studentDevice->last_login_at->format('Y-m-d H:i') : '-'}}
                                    </td>
                                    <td>
                                        @if($studentDevice->is_current)
                                            <span class="badge badge-success">{{trans('main_trans.Current')}}</span>
                                        @else
                                            <span class="badge badge-secondary">{{trans('main_trans.Inactive')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('Student-edit')
                                        <form action="{{route('student.remove-device', [$student->id, $studentDevice->device->id])}}"
                                              method="POST"
                                              style="display: inline;"
                                              onsubmit="return confirm('{{trans('main_trans.Confirm_remove_device')}}')"> 
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-mobile-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">{{trans('main_trans.No_devices_registered')}}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Device Limit Modal -->
<div class="modal fade" id="deviceLimitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{trans('main_trans.Update_device_limit')}}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="deviceLimitForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="max_devices">{{trans('main_trans.Maximum_devices')}}</label>
                        <input type="number"
                               class="form-control"
                               id="max_devices"
                               name="max_devices"
                               min="1"
                               max="10"
                               required>
                        <small class="form-text text-muted">
                            {{trans('main_trans.Device_limit_help_text')}}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{trans('main_trans.Cancel')}}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{trans('main_trans.Update')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>

@endsection

@section('js')
<script>
// Device Limit Modal
$('#deviceLimitModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var studentId = button.data('student-id');
    var currentLimit = button.data('current-limit');
    var studentName = button.data('student-name');

    var modal = $(this);
    modal.find('.modal-title').text('Update Device Limit - ' + studentName);
    modal.find('#max_devices').val(currentLimit);

    var form = modal.find('#deviceLimitForm');
    form.attr('action', '/student/' + studentId + '/device-limit');
});
</script>
@endsection
