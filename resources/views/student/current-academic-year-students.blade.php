@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Students_2025_2026') }}
@endsection
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Students_2025_2026') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Students_list_2025_2026') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')

    @include('components.flash-messages')

    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        @can('Student-add')
                            <div class="col-12 col-sm-12 col-lg-6
                                @can('Student-show-deleted')
                                    @if(\App\Models\Student::onlyTrashed()->count())
                                        col-xl-6
                                    @else
                                        col-xl-12
                                    @endif
                                @endcan
                                @cannot('Student-show-deleted') col-xl-12 @endcannot
                            ">
                                <a class="btn btn-outline-primary btn-block" href="{{ route('student.create') }}">{{ trans('main_trans.Add_student') }}</a>
                            </div>
                        @endcan
                        @can('Student-show-deleted')
                            @if(\App\Models\Student::onlyTrashed()->count())
                                <div class="col-12 col-sm-12 col-lg-6 col-xl-6">
                                    <a class="btn btn-outline-primary btn-block" href="{{ route('archived-student.index') }}">{{ trans('main_trans.Deleted_students') }}</a>
                                </div>
                            @endif
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example1" data-page-length='100' style=" text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-5p-f border-bottom-0"></th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Full_name') }}</th>
{{--                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.First_name') }}</th>--}}
{{--                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Father_name') }}</th>--}}
{{--                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Last_name') }}</th>--}}
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Email') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Phone') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Country_code') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Certificate_type') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.School') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.City') }}</th>
                                <th class="wd-10p border-bottom-0">
                                    {{trans('main_trans.Devices')}}
                                    @if(!config('features.student_devices'))
                                    <i class="fas fa-crown text-warning" title="{{ trans('main_trans.Pro_Only') }}"></i>
                                    @endif
                                </th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Status') }}</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
{{--                            @php($i=0)--}}
                            @foreach ($data as $key => $student)
                                <tr>
{{--                                    <td>{{ ++$i }}</td>--}}
                                    <td>{{ $student->id }}</td>
                                    <td>
                                        @if($student->photo == null)
                                            <img alt="avatar" class="rounded-circle avatar-md mr-2" src="{{URL::asset('assets/image/sanad.jpg')}}">
                                        @else
                                            <img alt="avatar" class="rounded-circle avatar-md mr-2" src="{{URL::asset('assets/image/Students/' . $student->id . '/' . $student->photo)}}">
                                        @endif
                                    </td>
                                    <td>{{ $student->first_name . " " . $student->father_name . " " . $student->last_name}}</td>
{{--                                    <td>{{ $student->first_name }}</td>--}}
{{--                                    <td>{{ $student->father_name }}</td>--}}
{{--                                    <td>{{ $student->last_name }}</td>--}}
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->phone }}</td>
                                    <td>{{ $student->country_code }}</td>
                                    <td>{{ $student->type->name }}</td>
                                    <td>{{ $student->school }}</td>
                                    <td>{{ trans('main_trans.' . $student->city ) }}</td>

                                    <td>
                                        @if(config('features.student_devices'))
                                            <span class="badge badge-info text-white">
                                                {{$student->getActiveDevicesCount()}}/{{$student->max_devices}}
                                            </span>
                                            @if($student->getActiveDevicesCount() > 0)
                                                <button class="btn btn-sm btn-outline-info ml-1"
                                                        data-toggle="modal"
                                                        data-target="#devicesModal"
                                                        data-student-id="{{$student->id}}"
                                                        data-student-name="{{$student->first_name}} {{$student->last_name}}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endif
                                        @else
                                            <span class="badge badge-outline-warning" 
                                                  style="cursor: pointer; border: 1px solid #f0ad4e; color: #f0ad4e;"
                                                  onclick="showProModal(event)">
                                                <i class="fas fa-crown mr-1"></i> Pro
                                            </span>
                                        @endif
                                    </td>
{{--                                    <td>--}}
{{--                                        <div class="d-flex align-items-center">--}}
{{--                                            <span class="mr-2">{{$student->max_devices}}</span>--}}
{{--                                            @can('Student-edit')--}}
{{--                                                <button class="btn btn-sm btn-outline-primary"--}}
{{--                                                        data-toggle="modal"--}}
{{--                                                        data-target="#deviceLimitModal"--}}
{{--                                                        data-student-id="{{$student->id}}"--}}
{{--                                                        data-current-limit="{{$student->max_devices}}"--}}
{{--                                                        data-student-name="{{$student->first_name}} {{$student->last_name}}">--}}
{{--                                                    <i class="fas fa-edit"></i>--}}
{{--                                                </button>--}}
{{--                                            @endcan--}}
{{--                                        </div>--}}
{{--                                    </td>--}}

                                    <td>
                                        @if ($student->status == 1 )
                                            <span class="label text-success d-flex">
                                                    <div class="dot-label bg-success" style="margin: -5px 20px 0px 0px"></div>{{ trans('main_trans.Enable') }}
                                                </span>
                                        @else
                                            <span class="label text-danger d-flex">
                                                    <div class="dot-label bg-danger" style="margin: -5px 20px 0px 0px"></div>{{ trans('main_trans.Disable') }}
                                                </span>
                                        @endif
                                    </td>

                                    <td>
                                        @can('Student-show')
                                            <a href="{{ route('student.show', $student->id) }}" class="btn btn-success"
                                               title="{{ trans('main_trans.Show') }}"><i class="fas fa-eye"></i></a>
                                        @endcan

                                        @can('Student-edit')
                                            <a href="{{ route('student.edit', $student->id) }}" class="btn btn-info"
                                               title="{{ trans('main_trans.Edit') }}"><i class="fas fa-pen"></i></a>
                                        @endcan

                                        @can('Student-delete')
                                            <a class="modal-effect btn btn-danger" data-effect="effect-scale"
                                               data-user-id="{{ $student->id }}" data-username="{{ $student->first_name . ' ' . $student->last_name}}"
                                               data-toggle="modal" href="#modaldemo8" title="{{ trans('main_trans.Delete') }}"><i
                                                    class="fas fa-trash"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->

        <!-- Modal effects -->
        <div class="modal" id="modaldemo8">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Student_delete') }}</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('student.destroy', 'test') }}" method="post">
                        {{ method_field('delete') }}
                        @csrf
                        <div class="modal-body">
                            <p>{{ trans('main_trans.Are_you_sure_to_delete') }}</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="username" id="username" type="text" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                            <button type="submit" class="btn btn-danger">{{ trans('main_trans.Delete') }}</button>
                        </div>
                    </form>
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

    @if(config('features.student_devices'))
    <!-- Devices View Modal -->
    <div class="modal fade" id="devicesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('main_trans.Student_devices')}}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="devicesContent">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection

@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <!-- Internal Modal js-->
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>

    <script>
        $('#modaldemo8').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var user_id = button.data('user-id')
            var username = button.data('username')
            var modal = $(this)
            modal.find('.modal-body #id').val(user_id);
            modal.find('.modal-body #username').val(username);
        })

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

        // Devices View Modal
        $('#devicesModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var studentId = button.data('student-id');
            var studentName = button.data('student-name');

            var modal = $(this);
            modal.find('.modal-title').text('Devices - ' + studentName);

            // Load devices via AJAX
            $.get('/student/' + studentId + '/devices')
                .done(function(data) {
                    modal.find('#devicesContent').html(data);
                })
                .fail(function() {
                    modal.find('#devicesContent').html('<p class="text-danger">Error loading devices</p>');
                });
        });
    </script>

@endsection
