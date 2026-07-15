@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Lessons') }} - {{ $unit_selected->name }}
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
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Coures') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Lessons') }} - {{ $unit_selected->name }}</span>
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
                        <a class="btn btn-primary btn-block" href="{{ route('teacher.unit', ['teacher' => $teacher_selected->id, 'subject_video' => $subject_video_selected->id]) }}">
                            <i class="fas fa-arrow-right"></i> {{ trans('main_trans.Back') }}
                        </a>
                    </div>
                    @can('LessonVideo-add')
                        <div class="col-12 col-sm-12 col-lg-4
                            @can('LessonVideo-show-deleted')
                                @if($archivedLessonVideosCount)
                                    col-xl-4
                                @else
                                    col-xl-6
                                @endif
                            @endcan
                            @cannot('LessonVideo-show-deleted') col-xl-6 @endcannot">
                            <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical" data-toggle="modal" href="#modal1">
                                {{ trans('main_trans.Add_lesson_video') }}
                            </a>
                        </div>
                    @endcan
                    @can('LessonVideo-edit')
                        <div class="col-12 col-sm-12 col-lg-4 col-xl-4">
                            <button id="reorder-btn" class="btn btn-info btn-block">
                                <i class="fas fa-sort"></i> {{ trans('main_trans.Reorder') }}
                            </button>
                        </div>
                    @endcan
                    @can('LessonVideo-show-deleted')
                        @if($archivedLessonVideosCount)
                            <div class="col-12 col-sm-12 col-lg-4 col-xl-2">
                                <a class="btn btn-outline-primary btn-block" href="{{ route('archived-lesson-video.unit', ['unit' => $unit_selected->id, 'subject_video' => $subject_video_selected->id, 'teacher' => $teacher_selected->id]) }}">
                                    {{ trans('main_trans.Deleted_lesson_videos') }}
                                </a>
                            </div>
                        @endif
                    @endcan
                </div>

                <div class="card-body">
                    @if($lessonVideos->count() > 0)
                        <div class="table-responsive hoverable-table">
                            <table class="table table-hover" id="sortable-table" data-page-length='50' style="text-align: center;">
                                <thead>
                                <tr>
                                    <th class="wd-5p-f drag-handle border-bottom-0" style="display: none;">{{ trans('main_trans.Order') }}</th>
                                    <th class="wd-5p-f border-bottom-0">#</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_videos') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody id="sortable-body">
                                @foreach ($lessonVideos as $lessonVideo)
                                    <tr data-id="{{ $lessonVideo->id }}">
                                        <td class="drag-handle" style="display: none;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>{{ $lessonVideo->id }}</td>
                                        <td>{{ $lessonVideo->title }}</td>
                                        <td>{{ $lessonVideo->youtube_links_count }}</td>
                                        <td>
                                            @can('YoutubeLinkVideo-show')
                                                <a class="btn btn-success" href="{{ route('lesson-video.youtube', ['lesson_video' => $lessonVideo->id, 'subject_video' => $subject_video_selected->id, 'teacher' => $teacher_selected->id, 'unit' => $unit_selected->id]) }}" title="{{ trans('main_trans.Videos') }}">
                                                    <i class="fab fa-youtube"></i> {{ trans('main_trans.Videos') }}
                                                </a>
                                            @endcan
                                            @can('LessonVideo-edit')
                                                <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical"
                                                   data-id="{{ $lessonVideo->id }}" data-title="{{ $lessonVideo->title }}"
                                                   data-unit_selected="{{ $lessonVideo->unit_id }}" data-toggle="modal"
                                                   href="#modal2" title="{{ trans('main_trans.Edit') }}">
                                                    <i class="fas fa-pen"></i> {{ trans('main_trans.Edit') }}
                                                </a>
                                            @endcan

                                            @can('LessonVideo-edit')
                                                <form action="{{ route('lesson-video.toggle', $lessonVideo->id) }}" method="POST"
                                                      style="display: initial">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="btn {{ $lessonVideo->is_active ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                                        <i class="fas {{ !$lessonVideo->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                                        {{ !$lessonVideo->is_active ? ' ' . trans('main_trans.Disable') : ' ' . trans('main_trans.Enable') }}
                                                    </button>
                                                </form>
                                            @endcan
                                          

                                            @can('LessonVideo-delete')
                                                <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                                   data-id="{{ $lessonVideo->id }}" data-title="{{ $lessonVideo->title }}" data-toggle="modal"
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
                            <h5>{{ trans('main_trans.No_lesson_videos_available') }}</h5>
                            <p class="text-muted">{{ trans('main_trans.No_lesson_videos_available_description') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modal1">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Add_lesson_video') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('lesson-video.store') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="subject_video" value="{{ $subject_video_selected->id }}">
                    <input type="hidden" name="teacher" value="{{ $teacher_selected->id }}">
                    <div class="modal-body">
                        <div class="row mb-3 mx-1">
                            <label for="title" class="col-sm-2 col-form-label">{{ trans('main_trans.Name') }}</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="title" id="title" type="text" required>
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="unit_id" class="col-sm-2 col-form-label">{{ trans('main_trans.Unit') }}</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="unit_id" id="unit_id" required>
                                    <option value="">{{ trans('main_trans.Select_unit') }}</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ $unit->id == $unit_selected->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="is_active" class="col-sm-2 col-form-label">{{ trans('main_trans.Active') }}</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="is_active" id="is_active" required>
                                    <option value="1">{{ trans('main_trans.Enable') }}</option>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Edit_lesson_video') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('lesson-video.update', 'lesson-video') }}" method="post">
                    {{ method_field('patch') }}
                    {{ csrf_field() }}
                    <input type="hidden" name="subject_video" value="{{ $subject_video_selected->id }}">
                    <input type="hidden" name="teacher" value="{{ $teacher_selected->id }}">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" value="">
                        <div class="row mb-3 mx-1">
                            <label for="title" class="col-sm-2 col-form-label">{{ trans('main_trans.Name') }}</label>
                            <div class="col-sm-10">
                                <input class="form-control" name="title" id="title" type="text" required>
                            </div>
                        </div>
                        <div class="row mb-3 mx-1">
                            <label for="unit_id" class="col-sm-2 col-form-label">{{ trans('main_trans.Unit') }}</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="unit_id" id="unit_id" required>
                                    <option value="">{{ trans('main_trans.Select_unit') }}</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
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
                    <h6 class="modal-title">{{ trans('main_trans.Delete_lesson_video') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('lesson-video.destroy', 'lesson-video') }}" method="post">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                    <input type="hidden" name="subject_video" value="{{ $subject_video_selected->id }}">
                    <input type="hidden" name="teacher" value="{{ $teacher_selected->id }}">
                    <div class="modal-body">
                        <p>{{ trans('main_trans.Are_you_sure_to_delete') }}</p><br>
                        <input type="hidden" name="id" id="id" value="">
                        <div class="row mb-3 mx-1">
                            <input class="form-control" name="title" id="title" type="text" readonly>
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
        modal.find('.modal-body #title').val('');
        modal.find('.modal-body #unit_id').val('{{ $unit_selected->id }}');
    });

    $('#modal2').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find('.modal-body #id').val(button.data('id'));
        modal.find('.modal-body #title').val(button.data('title'));
        modal.find('.modal-body #unit_id').val(button.data('unit_selected'));
    });

    $('#modal3').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        modal.find('.modal-body #id').val(button.data('id'));
        modal.find('.modal-body #title').val(button.data('title'));
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
                url: '{{ route("lessons-video.reorder", $unit_selected->id) }}',
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
