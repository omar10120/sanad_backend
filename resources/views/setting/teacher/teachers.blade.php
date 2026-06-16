@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Teachers') }} - {{ $subject_video_selected->name }}
@endsection
@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
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
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Teachers') }} - {{ $subject_video_selected->name }}</span>
            </div>
        </div>
    </div>
@endsection
@section('content')

    @include('components.flash-messages')

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0 row">
                    <div class="col-12 col-sm-12 col-lg-2 col-xl-2">
                        <a class="btn btn-primary btn-block" href="{{ route('type.subject-video', $subject_video_selected->types->first()->id) }}">
                            <i class="fas fa-arrow-right"></i> {{ trans('main_trans.Back') }}
                        </a>
                    </div>
                    @can('Teacher-add')
                        <div class="col-12 col-sm-12 col-lg-4
                            @can('Teacher-show-deleted')
                                @if($archivedTeachersCount)
                                    col-xl-4
                                @else
                                    col-xl-6
                                @endif
                            @endcan
                            @cannot('Teacher-show-deleted') col-xl-6 @endcannot">
                            <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical" data-toggle="modal" href="#modal1">
                                {{ trans('main_trans.Add_teacher') }}
                            </a>
                        </div>
                    @endcan
                    @can('Teacher-edit')
                        <div class="col-12 col-sm-12 col-lg-4 col-xl-4">
                            <button id="reorder-btn" class="btn btn-info btn-block">
                                <i class="fas fa-sort"></i> {{ trans('main_trans.Reorder') }}
                            </button>
                        </div>
                    @endcan
                    @can('Teacher-show-deleted')
                        @if($archivedTeachersCount)
                            <div class="col-12 col-sm-12 col-lg-4 col-xl-2">
                                <a class="btn btn-outline-primary btn-block" href="{{ route('archived-teacher.subject-video', $subject_video_selected->id) }}">
                                    {{ trans('main_trans.Deleted_teachers') }}
                                </a>
                            </div>
                        @endif
                    @endcan
                </div>

                <div class="card-body">
                    @if($teachers->count() > 0)
                        <div class="table-responsive hoverable-table">
                            <table class="table table-hover" id="sortable-table" data-page-length='50' style="text-align: center;">
                                <thead>
                                <tr>
                                    <th class="wd-5p-f drag-handle border-bottom-0" style="display: none;">{{ trans('main_trans.Order') }}</th>
                                    <th class="wd-5p-f border-bottom-0">#</th>
                                    <th class="wd-10p-f border-bottom-0">{{ trans('main_trans.Teacher_photo') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Course_subjects') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_units') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Price') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Estimation_time') }}</th>
                                    <th class="wd-20p-f border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody id="sortable-body">
                                @foreach ($teachers as $teacher)
                                    <tr data-id="{{ $teacher->id }}">
                                        <td class="drag-handle" style="display: none;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>{{ $teacher->id }}</td>
                                        <td>
                                            @if($teacher->photo)
                                                <img width="80px" src="{{ URL::asset('assets/image/Teachers/' . $teacher->id . '/' . $teacher->photo) }}">
                                            @else
                                                <img width="80px" src="{{ URL::asset('assets/image/sanad.jpg') }}">
                                            @endif
                                        </td>
                                        <td><b>{{ $teacher->name }}</b></td>
                                        <td>
                                            @foreach ($teacher->subjectVideos as $sv)
                                                <label class="badge badge-purple" style="font-size: 14px !important; padding: 8px !important;">{{ $sv->name }}</label>
                                            @endforeach
                                        </td>
                                        <td>{{ $teacher->units->count() }}</td>
                                        <td>{{ $teacher->price ?? '-' }}</td>
                                        <td>{{ $teacher->estimation_time }}</td>
                                        <td>
                                            @can('Unit-show')
                                                <a class="btn btn-success" href="{{ route('teacher.unit', ['teacher' => $teacher->id, 'subject_video' => $subject_video_selected->id]) }}" title="{{ trans('main_trans.Units') }}">
                                                    <i class="fas fa-layer-group"></i> {{ trans('main_trans.Units') }}
                                                </a>
                                            @endcan
                                            @can('Teacher-edit')
                                                <a class="modal-effect btn btn-info my-1" data-effect="effect-flip-vertical"
                                                   data-id="{{ $teacher->id }}"
                                                   data-name="{{ $teacher->name }}"
                                                   data-estimation_time="{{ $teacher->estimation_time }}"
                                                   data-whatsapp_link="{{ $teacher->whatsapp_link }}"
                                                   data-telegram_link="{{ $teacher->telegram_link }}"
                                                   data-instagram_link="{{ $teacher->instagram_link }}"
                                                    data-phone="{{ $teacher->phone }}"
                                                   data-price="{{ $teacher->price }}"
                                                   data-description="{{ $teacher->description }}"
                                                   data-subject_videos="{{ $teacher->subjectVideos->pluck('id')->implode(',') }}"
                                                   data-toggle="modal" href="#modal2">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('Teacher-delete')
                                                <a class="modal-effect btn btn-danger my-1" data-effect="effect-flip-vertical"
                                                   data-id="{{ $teacher->id }}" data-name="{{ $teacher->name }}"
                                                   data-toggle="modal" href="#modal3">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5>{{ trans('main_trans.No_teachers_available') }}</h5>
                            <p>{{ trans('main_trans.No_teachers_available_description') }}</p>
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
                    <h6 class="modal-title">{{ trans('main_trans.Add_teacher') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form method="POST" action="{{ route('teacher.store') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="subject_video_id" value="{{ $subject_video_selected->id }}">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }} <span class="tx-danger">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Estimation_time') }} <span class="tx-danger">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control" name="estimation_time" type="number" min="0" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Price') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="price" type="number" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Phone') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="phone">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Whatsapp_link') }} </label>
                            <div class="col-md-8">
                                <input class="form-control" name="whatsapp_link">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Instagram_link') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="instagram_link">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Telegram') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="telegram_link">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Description') }}</label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="description" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Course_subjects') }} <span class="tx-danger">*</span></label>
                            <div class="col-md-8">
                                <select name="subject_videos[]" class="form-control subject-videos-select" required multiple>
                                    @foreach ($subjectVideos as $subjectVideo)
                                        <option value="{{ $subjectVideo->id }}" {{ $subjectVideo->id == $subject_video_selected->id ? 'selected' : '' }}>
                                            {{ $subjectVideo->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Teacher_photo') }}</label>
                            <div class="col-md-8">
                                <input class="dropify" name="photo" type="file" data-height="120" accept=".jpg,.png,image/jpeg,image/png">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Add_teacher') }}</button>
                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ trans('main_trans.Close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="modal2">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Edit_teacher') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form method="POST" action="{{ route('teacher.update') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }} <span class="tx-danger">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Estimation_time') }} <span class="tx-danger">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control" name="estimation_time" id="edit_estimation_time" type="number" min="0" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Price') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="price" id="edit_price" type="number" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Phone') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="phone" id="edit_phone">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Whatsapp_link') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="whatsapp_link" id="edit_whatsapp_link">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Instagram_link') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="instagram_link" id="edit_instagram_link">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Telegram') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="telegram_link" id="edit_telegram_link">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Description') }}</label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="description" id="edit_description" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Course_subjects') }}</label>
                            <div class="col-md-8">
                                <select name="subject_videos[]" id="edit_subject_videos" class="form-control subject-videos-select" required multiple> <span class="tx-danger">*</span></label>
                                    @foreach ($subjectVideos as $subjectVideo)
                                        <option value="{{ $subjectVideo->id }}">{{ $subjectVideo->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Teacher_photo') }}</label>
                            <div class="col-md-8">
                                <input class="dropify" name="photo" type="file" data-height="120" accept=".jpg,.png,image/jpeg,image/png">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Edit_teacher') }}</button>
                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ trans('main_trans.Close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="modal3">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Delete_teacher') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('teacher.destroy') }}" method="post">
                    @method('delete')
                    @csrf
                    <div class="modal-body">
                        <p>{{ trans('main_trans.Are_you_sure_to_delete') }}</p>
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
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fileuploads/js/fileupload.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fileuploads/js/file-upload.js')}}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $('.subject-videos-select').select2({ width: '100%' });

        $('#modal2').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $('#edit_id').val(button.data('id'));
            $('#edit_name').val(button.data('name'));
            $('#edit_estimation_time').val(button.data('estimation_time'));
            $('#edit_whatsapp_link').val(button.data('whatsapp_link'));
            $('#edit_instagram_link').val(button.data('instagram_link'));
            $('#edit_telegram_link').val(button.data('telegram_link'));
            $('#edit_phone').val(button.data('phone'));
            $('#edit_price').val(button.data('price'));
            $('#edit_description').val(button.data('description'));
            var subjectVideos = String(button.data('subject_videos') || '').split(',').filter(Boolean);
            $('#edit_subject_videos').val(subjectVideos).trigger('change');
        });

        $('#modal3').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $('#delete_id').val(button.data('id'));
            $('#delete_name').val(button.data('name'));
        });

        $(document).ready(function () {
            let isReorderMode = false;

            $('#reorder-btn').click(function () {
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
                    placeholder: 'ui-state-highlight'
                }).disableSelection();
            }

            function exitReorderMode() {
                const orderedIds = [];
                $('#sortable-body tr').each(function () {
                    orderedIds.push($(this).data('id'));
                });

                $.ajax({
                    url: '{{ route("teachers.reorder", $subject_video_selected->id) }}',
                    method: 'POST',
                    data: {
                        ordered_ids: orderedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success('{{ __("main_trans.Order updated successfully") }}');
                            setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function () {
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
