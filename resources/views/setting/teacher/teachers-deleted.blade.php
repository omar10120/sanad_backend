@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Deleted_teachers') }} - {{ $subject_video_selected->name }}
@endsection
@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Coures') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Deleted_teachers') }} - {{ $subject_video_selected->name }}</span>
            </div>
        </div>
    </div>
@endsection
@section('content')

    @include('components.flash-messages')

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <a class="btn btn-primary btn-sm" href="{{ route('subject-video.teacher', $subject_video_selected->id) }}">
                        <i class="fas fa-arrow-right"></i> {{ trans('main_trans.Back') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example1" data-page-length='100' style="text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_units') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($teachers as $teacher)
                                <tr>
                                    <td>{{ $teacher->id }}</td>
                                    <td>{{ $teacher->name }}</td>
                                    <td>{{ $teacher->units()->count() }}</td>
                                    <td>
                                        @can('Teacher-restore-deleted')
                                            <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical"
                                               data-id="{{ $teacher->id }}" data-name="{{ $teacher->name }}" data-toggle="modal"
                                               href="#modal2" title="{{ trans('main_trans.Restore') }}">
                                                <i class="fas fa-trash-restore"></i>
                                            </a>
                                        @endcan
                                        @can('Teacher-delete')
                                            <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                               data-id="{{ $teacher->id }}" data-name="{{ $teacher->name }}" data-toggle="modal"
                                               href="#modal3" title="{{ trans('main_trans.Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
    </div>

    <div class="modal" id="modal2">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Restore_teacher') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('archived-teacher.update') }}" method="post">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <p>{{ trans('main_trans.Are_you_sure_to_restore') }}</p>
                        <input type="hidden" name="id" id="restore_id">
                        <input class="form-control" name="name" id="restore_name" type="text" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                        <button type="submit" class="btn btn-info">{{ trans('main_trans.Restore') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="modal3">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Delete_teacher_permanently') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('archived-teacher.destroy') }}" method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>{{ trans('main_trans.Are_you_sure_to_delete_permanently') }}</p>
                        <input type="hidden" name="id" id="delete_id">
                        <input class="form-control" name="name" id="delete_name" type="text" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                        <button type="submit" class="btn btn-danger">{{ trans('main_trans.Delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{URL::asset('assets/js/table-data.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
    <script>
        $('#modal2').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $('#restore_id').val(button.data('id'));
            $('#restore_name').val(button.data('name'));
        });
        $('#modal3').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $('#delete_id').val(button.data('id'));
            $('#delete_name').val(button.data('name'));
        });
    </script>
@endsection
