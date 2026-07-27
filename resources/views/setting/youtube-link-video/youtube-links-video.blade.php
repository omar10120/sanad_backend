@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Videos') }} - {{ $lesson_video_selected->title }}
@endsection
@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    <style>
        .drag-handle { cursor: move; width: 30px; text-align: center; }
        .ui-sortable-helper { background-color: #f8f9fa; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .ui-state-highlight { height: 45px; background-color: #e3f2fd; border: 2px dashed #2196f3; }
        #sortable-body tr:hover .drag-handle { color: #007bff; }
        .youtube-link-cell { max-width: 280px; word-break: break-all; }
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Coures') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Videos') }} - {{ $lesson_video_selected->title }}</span>
            </div>
        </div>
    </div>
@endsection
@section('content')

    @include('components.flash-messages')

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0 row" style="margin-right: 0; margin-left: 0;">
                    <div class="col-12 col-sm-12 col-lg-2 col-xl-2">
                        <a class="btn btn-primary btn-block" href="{{ route('unit.lesson-video', ['unit' => $unit_selected->id, 'subject_video' => $subject_video_selected->id, 'teacher' => $teacher_selected->id]) }}">
                            <i class="fas fa-arrow-right"></i> {{ trans('main_trans.Back') }}
                        </a>
                    </div>
                    @can('YoutubeLinkVideo-add')
                        <div class="col-12 col-sm-12 col-lg-4 @can('YoutubeLinkVideo-edit') col-xl-4 @else col-xl-6 @endcan">
                            <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical" data-toggle="modal" href="#modal1">
                                {{ trans('main_trans.Add_youtube_link_video') }}
                            </a>
                        </div>
                    @endcan
                    @can('YoutubeLinkVideo-edit')
                        <div class="col-12 col-sm-12 col-lg-4 col-xl-4">
                            <button id="reorder-btn" class="btn btn-info btn-block">
                                <i class="fas fa-sort"></i> {{ trans('main_trans.Reorder') }}
                            </button>
                        </div>
                    @endcan
                    @can('YoutubeLinkVideo-edit')
                        @if($archivedYoutubeLinksCount)
                            <div class="col-12 col-sm-12 col-lg-4 col-xl-2">
                                <a class="btn btn-outline-primary btn-block"
                                   href="{{ route('archived-youtube-link-video.lesson-video', [
                                        'lesson_video' => $lesson_video_selected->id,
                                        'subject_video' => $subject_video_selected->id,
                                        'teacher' => $teacher_selected->id,
                                        'unit' => $unit_selected->id,
                                   ]) }}">
                                    {{ trans('main_trans.Deleted_youtube_link_videos') }}
                                </a>
                            </div>
                        @endif
                    @endcan
                </div>

                <div class="card-body">
                    @if($youtubeLinks->count() > 0)
                        <div class="table-responsive hoverable-table">
                            <table class="table table-hover" id="sortable-table" data-page-length='50' style="text-align: center;">
                                <thead>
                                <tr>
                                    <th class="wd-5p-f drag-handle border-bottom-0" style="display: none;">{{ trans('main_trans.Order') }}</th>
                                    <th class="wd-5p-f border-bottom-0">#</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                    <th class="wd-15p border-bottom-0">{{ trans('main_trans.Youtube_link') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Video_time') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody id="sortable-body">
                                @foreach ($youtubeLinks as $youtubeLink)
                                    <tr data-id="{{ $youtubeLink->id }}">
                                        <td class="drag-handle" style="display: none;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
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
                                                   data-id="{{ $youtubeLink->id }}"
                                                   data-name="{{ $youtubeLink->name }}"
                                                   data-youtube_link="{{ $youtubeLink->youtube_link }}"
                                                   data-video_time="{{ $youtubeLink->video_time }}"
                                                   data-is_active="{{ $youtubeLink->is_active ? 1 : 0 }}"
                                                   data-lesson_video_selected="{{ $youtubeLink->lesson_video_id }}"
                                                   data-toggle="modal"
                                                   href="#modal2" title="{{ trans('main_trans.Edit') }}">
                                                    <i class="fas fa-pen"></i> {{ trans('main_trans.Edit') }}
                                                </a>
                                                <form action="{{ route('youtube-link-video.toggle', $youtubeLink->id) }}" method="POST"
                                                      style="display: initial">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="btn {{ $youtubeLink->is_active ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                                        <i class="fas {{ !$youtubeLink->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                                        {{ !$youtubeLink->is_active ? ' ' . trans('main_trans.Disable') : ' ' . trans('main_trans.Enable') }}
                                                    </button>
                                                </form>
                                            @endcan
                                            @can('YoutubeLinkVideo-delete')
                                                <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                                   data-id="{{ $youtubeLink->id }}"
                                                   data-name="{{ $youtubeLink->name }}"
                                                   data-toggle="modal"
                                                   href="#modal3" title="{{ trans('main_trans.Delete') }}">
                                                    <i class="fas fa-trash"></i> {{ trans('main_trans.Delete') }}
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <h5>{{ trans('main_trans.No_youtube_link_videos_available') }}</h5>
                            <p class="text-muted">{{ trans('main_trans.No_youtube_link_videos_available_description') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modal1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Add_youtube_link_video') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('youtube-link-video.store') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="subject_video" value="{{ $subject_video_selected->id }}">
                    <input type="hidden" name="teacher" value="{{ $teacher_selected->id }}">
                    <input type="hidden" name="unit" value="{{ $unit_selected->id }}">
                    <div class="modal-body">
                        <div class="row mb-3 mx-1">
                            <label for="name" class="col-sm-3 col-form-label">{{ trans('main_trans.Name') }}</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="name" id="name" type="text" required>
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="youtube_link" class="col-sm-3 col-form-label">{{ trans('main_trans.Youtube_link') }}</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="youtube_link" id="youtube_link" type="url" placeholder="https://www.youtube.com/watch?v=..." required>
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="video_time" class="col-sm-3 col-form-label">{{ trans('main_trans.Video_time') }}</label>
                            <div class="col-sm-9">
                                <!-- <input class="form-control" name="video_time" id="video_time" type="number" min="0" placeholder="{{ trans('main_trans.Video_time_placeholder') }}"> -->
                                <input class="form-control" type="time" id="video_time" name="video_time"
                                     >
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="lesson_video_id" class="col-sm-3 col-form-label">{{ trans('main_trans.Lesson') }}</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="lesson_video_id" id="lesson_video_id" required>
                                    <option value="">{{ trans('main_trans.Select_lesson') }}</option>
                                    @foreach($lessonVideos as $lessonVideo)
                                        <option value="{{ $lessonVideo->id }}" {{ $lessonVideo->id == $lesson_video_selected->id ? 'selected' : '' }}>{{ $lessonVideo->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="is_active" class="col-sm-3 col-form-label">{{ trans('main_trans.Status') }}</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="is_active" id="is_active" required>
                                    <option value="1" selected>{{ trans('main_trans.Enable') }}</option>
                                    <option value="0">{{ trans('main_trans.Disable') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ trans('main_trans.Add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="modal2">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Edit_youtube_link_video') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('youtube-link-video.update', 'youtube-link-video') }}" method="post">
                    {{ method_field('patch') }}
                    {{ csrf_field() }}
                    <input type="hidden" name="subject_video" value="{{ $subject_video_selected->id }}">
                    <input type="hidden" name="teacher" value="{{ $teacher_selected->id }}">
                    <input type="hidden" name="unit" value="{{ $unit_selected->id }}">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" value="">
                        <div class="row mb-3 mx-1">
                            <label for="name" class="col-sm-3 col-form-label">{{ trans('main_trans.Name') }}</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="name" id="name" type="text" required>
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="youtube_link" class="col-sm-3 col-form-label">{{ trans('main_trans.Youtube_link') }}</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="youtube_link" id="youtube_link" type="url" required>
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="video_time" class="col-sm-3 col-form-label">{{ trans('main_trans.Video_time') }}</label>
                            <div class="col-sm-9">
                                <!-- <input class="form-control" name="video_time" id="video_time" type="number" min="0"> -->
                                <input class="form-control" type="time" id="video_time" name="video_time"
                                     >
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="lesson_video_id" class="col-sm-3 col-form-label">{{ trans('main_trans.Lesson') }}</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="lesson_video_id" id="lesson_video_id" required>
                                    <option value="">{{ trans('main_trans.Select_lesson') }}</option>
                                    @foreach($lessonVideos as $lessonVideo)
                                        <option value="{{ $lessonVideo->id }}">{{ $lessonVideo->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="edit_is_active" class="col-sm-3 col-form-label">{{ trans('main_trans.Status') }}</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="is_active" id="edit_is_active" required>
                                    <option value="1">{{ trans('main_trans.Enable') }}</option>
                                    <option value="0">{{ trans('main_trans.Disable') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                        <button type="submit" class="btn btn-info">{{ trans('main_trans.Edit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="modal3">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Delete_youtube_link_video') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('youtube-link-video.destroy', 'youtube-link-video') }}" method="post">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                    <input type="hidden" name="subject_video" value="{{ $subject_video_selected->id }}">
                    <input type="hidden" name="teacher" value="{{ $teacher_selected->id }}">
                    <input type="hidden" name="unit" value="{{ $unit_selected->id }}">
                    <div class="modal-body">
                        <p>{{ trans('main_trans.Are_you_sure_to_delete') }}</p><br>
                        <input type="hidden" name="id" id="id" value="">
                        <div class="row mb-3 mx-1">
                            <input class="form-control" name="name" id="name" type="text" readonly>
                        </div>
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
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/pdfmake.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/vfs_fonts.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/js/table-data.js')}}"></script>
<script src="{{URL::asset('assets/js/modal.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    $('#modal1').on('show.bs.modal', function() {
        var modal = $(this);
        modal.find('.modal-body #name').val('');
        modal.find('.modal-body #youtube_link').val('');
        modal.find('.modal-body #video_time').val('00:00');
        modal.find('.modal-body #lesson_video_id').val('{{ $lesson_video_selected->id }}');
        modal.find('.modal-body #is_active').val('1');
    });

    $('#modal2').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find('.modal-body #id').val(button.data('id'));
        modal.find('.modal-body #name').val(button.data('name'));
        modal.find('.modal-body #youtube_link').val(button.data('youtube_link'));
        modal.find('.modal-body #video_time').val(button.data('video_time') || '00:00');
        modal.find('.modal-body #lesson_video_id').val(button.data('lesson_video_selected'));
        modal.find('.modal-body #edit_is_active').val(String(button.data('is_active')));
    });

    $('#modal3').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find('.modal-body #id').val(button.data('id'));
        modal.find('.modal-body #name').val(button.data('name'));
    });

    $(document).ready(function() {
        let isReorderMode = false;

        $('#reorder-btn').click(function() {
            if (!isReorderMode) {
                enterReorderMode();
            } else {
                exitReorderMode();
            }
        });

        function enterReorderMode() {
            isReorderMode = true;
            $('#reorder-btn').removeClass('btn-info').addClass('btn-success').html('<i class="fas fa-check"></i> {{ __("main_trans.Save Order") }}');
            $('.drag-handle').show();

            $("#sortable-body").sortable({
                handle: '.drag-handle',
                placeholder: 'ui-state-highlight',
            }).disableSelection();
        }

        function exitReorderMode() {
            const orderedIds = [];
            $('#sortable-body tr').each(function() {
                orderedIds.push($(this).data('id'));
            });

            $.ajax({
                url: '{{ route("youtube-links-video.reorder", $lesson_video_selected->id) }}',
                method: 'POST',
                data: {
                    ordered_ids: orderedIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('{{ __("main_trans.Order updated successfully") }}');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function() {
                    toastr.error('{{ __("main_trans.Error updating order") }}');
                }
            });

            $("#sortable-body").sortable("destroy");
            $('.drag-handle').hide();
            $('#reorder-btn').removeClass('btn-success').addClass('btn-info').html('<i class="fas fa-sort"></i> {{ __("main_trans.Reorder") }}');
            isReorderMode = false;
        }
    });
</script>
@endsection
