@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Deleted_youtube_link_videos') }} - {{ $lesson_video_selected->title }}
@endsection
@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <style>
        .youtube-link-cell { max-width: 280px; word-break: break-all; }
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Coures') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Deleted_youtube_link_videos') }} - {{ $lesson_video_selected->title }}</span>
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
                    <a class="btn btn-primary btn-sm"
                       href="{{ route('lesson-video.youtube', [
                            'lesson_video' => $lesson_video_selected->id,
                            'subject_video' => $subject_video_selected->id,
                            'teacher' => $teacher_selected->id,
                            'unit' => $unit_selected->id,
                       ]) }}">
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
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Youtube_link') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Video_time') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($youtubeLinks as $youtubeLink)
                                <tr>
                                    <td>{{ $youtubeLink->id }}</td>
                                    <td>{{ $youtubeLink->name }}</td>
                                    <td class="youtube-link-cell">
                                        <a href="{{ $youtubeLink->youtube_link }}" target="_blank" rel="noopener noreferrer">
                                            {{ $youtubeLink->youtube_link }}
                                        </a>
                                    </td>
                                    <td>{{ $youtubeLink->video_time ?? '-' }}</td>
                                    <td>
                                        @can('YoutubeLinkVideo-edit')
                                            <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical"
                                               data-id="{{ $youtubeLink->id }}" data-name="{{ $youtubeLink->name }}" data-toggle="modal"
                                               href="#modal2" title="{{ trans('main_trans.Restore') }}">
                                                <i class="fas fa-trash-restore"></i>
                                            </a>
                                        @endcan
                                        @can('YoutubeLinkVideo-delete')
                                            <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                               data-id="{{ $youtubeLink->id }}" data-name="{{ $youtubeLink->name }}" data-toggle="modal"
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
                    <h6 class="modal-title">{{ trans('main_trans.Restore_youtube_link_video') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('archived-youtube-link-video.update') }}" method="post">
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
                    <h6 class="modal-title">{{ trans('main_trans.Delete_youtube_link_video_permanently') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('archived-youtube-link-video.destroy') }}" method="post">
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
