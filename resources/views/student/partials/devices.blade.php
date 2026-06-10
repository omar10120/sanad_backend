@if($devices->count() > 0)
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{{trans('main_trans.Device_info')}}</th>
                    <th>{{trans('main_trans.OS')}}</th>
                    <th>{{trans('main_trans.Status')}}</th>
                    <th>{{trans('main_trans.Last_login')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devices as $studentDevice)
                <tr>
                    <td>
                        <div>
                            <strong>{{$studentDevice->device->brand}} {{$studentDevice->device->model}}</strong>
                            <br>
                            <small class="text-muted">{{$studentDevice->device->device_id}}</small>
                        </div>
                    </td>
                    <td>{{$studentDevice->device->os_name}} {{$studentDevice->device->os_version}}</td>
                    <td>
                        @if($studentDevice->is_current)
                            <span class="badge badge-success">{{trans('main_trans.Current')}}</span>
                        @else
                            <span class="badge badge-secondary">{{trans('main_trans.Inactive')}}</span>
                        @endif
                    </td>
                    <td>{{$studentDevice->last_login_at ? $studentDevice->last_login_at->format('Y-m-d H:i') : '-'}}</td>
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
