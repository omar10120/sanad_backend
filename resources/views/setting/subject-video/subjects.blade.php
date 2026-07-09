@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Course_subjects') . ' - ' . $type_selected->name }}
@endsection
@section('css')
    <link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Course_subjects') . ' - ' . $type_selected->name }}</span>
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
                        <a class="btn btn-primary btn-block" href="{{ route('course-type.index') }}">
                            <i class="fas fa-arrow-right"></i> {{ trans('main_trans.Back') }}
                        </a>
                    </div>
                    @can('SubjectVideo-add')
                        <div class="col-12 col-sm-12 col-lg-4 col-xl-6">
                            <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical"
                               data-toggle="modal" href="#modal1">{{ trans('main_trans.Add_course_subject') }}</a>
                        </div>
                    @endcan
                    @can('SubjectVideo-edit')
                        <div class="col-12 col-sm-12 col-lg-4 col-xl-4">
                            <button id="reorder-btn" class="btn btn-info btn-block">
                                <i class="fas fa-sort"></i> {{ __('main_trans.Reorder') }}
                            </button>
                        </div>
                    @endcan
                </div>

                <div class="card-body">
                    @if($subjectVideos->count() > 0)
                        <div class="table-responsive hoverable-table">
                            <table class="table table-hover" id="sortable-table" data-page-length='50' style="text-align: center;">
                                <thead>
                                <tr>
                                    <th class="wd-5p-f drag-handle border-bottom-0" style="display: none;">{{ trans('main_trans.Order') }}</th>
                                    <th class="wd-5p-f border-bottom-0">#</th>
                                    <th class="wd-5p-f border-bottom-0">{{ trans('main_trans.Subject_photo') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Certificate_types') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_teachers') }}</th>
                                    <th class="wd-30p-f border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody id="sortable-body">
                                @foreach ($subjectVideos->sortBy('order') as $subjectVideo)
                                    <tr data-id="{{ $subjectVideo->id }}">
                                        <td class="drag-handle" style="display: none;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>{{ $subjectVideo->id }}</td>
                                        <td>
                                            @if($subjectVideo->icon_photo == null)
                                                <img width="100px" src="{{URL::asset('assets/image/sanad.jpg')}}">
                                            @else
                                                <img width="100px" src="{{URL::asset('assets/image/SubjectVideos/' . $subjectVideo->id . '/' . $subjectVideo->icon_photo)}}">
                                            @endif
                                        </td>
                                        <td><b>{{ $subjectVideo->name }}</b></td>
                                        <td>
                                            @foreach ($subjectVideo->types as $t)
                                                <label class="badge badge-purple" style="font-size: 14px !important; padding: 8px !important;">{{ $t->name }}</label>
                                            @endforeach
                                        </td>
                                        <td>{{ $subjectVideo->teachers->count() }}</td>
                                        <td>
                                            @can('Teacher-show')
                                                <a class="btn btn-success" href="{{ route('subject-video.teacher', $subjectVideo->id) }}" title="{{ trans('main_trans.Teachers') }}">
                                                    <i class="fas fa-chalkboard-teacher"></i> {{ trans('main_trans.Teachers') }}
                                                </a>
                                            @endcan
                                            @can('SubjectVideo-edit')
                                                <a class="modal-effect btn btn-info my-1" data-effect="effect-flip-vertical"
                                                   data-id="{{ $subjectVideo->id }}"
                                                   data-name="{{ $subjectVideo->name }}"
                                                   data-icon="{{ $subjectVideo->icon }}"
                                                   data-link="{{ $subjectVideo->link }}"
                                                   data-description="{{ $subjectVideo->description }}"
                                                   data-light-color="{{ $subjectVideo->light_color_code ?? '#ffffff' }}"
                                                   data-dark-color="{{ $subjectVideo->dark_color_code ?? '#000000' }}"
                                                   data-icon-photo="{{ $subjectVideo->icon_photo }}"
                                                   data-photo-url="{{ $subjectVideo->icon_photo ? URL::asset('assets/image/SubjectVideos/' . $subjectVideo->id . '/' . $subjectVideo->icon_photo) : '' }}"
                                                   data-types="{{ $subjectVideo->types->pluck('id')->implode(',') }}"
                                                   data-toggle="modal" href="#modal2">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form action="{{ route('subjects-video.toggle', $subjectVideo->id) }}" method="POST" style="display: initial">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn {{ $subjectVideo->is_active ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                                        <i class="fas {{ !$subjectVideo->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                                        {{ !$subjectVideo->is_active ? trans('main_trans.Disable') : trans('main_trans.Enable') }}
                                                    </button>
                                                </form>
                                            @endcan
                                            @can('SubjectVideo-delete')
                                                <a class="modal-effect btn btn-danger my-1" data-effect="effect-flip-vertical"
                                                   data-id="{{ $subjectVideo->id }}" data-name="{{ $subjectVideo->name }}"
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
                            <h5>{{ trans('main_trans.No_course_subjects_available') }}</h5>
                            <p>{{ trans('main_trans.No_course_subjects_available_description') }}</p>
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
                    <h6 class="modal-title">{{ trans('main_trans.Add_course_subject') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form method="POST" action="{{ route('subject-video.store') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3"> 
                            
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }} <span class="tx-danger">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Icon') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="icon">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Link') }} <span class="tx-danger">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control" name="link">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Description') }}</label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="description" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Certificate_types') }} <span class="tx-danger">*</span></label>
                            <div class="col-md-8">
                                <select name="types[]" class="form-control types-select" required multiple>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}" {{ $type->id == $type_selected->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="icon_photo" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Subject_photo') }}</label>
                            <div class="col-md-8">
                                <input class="dropify" id="icon_photo" name="icon_photo" type="file" data-height="120" accept=".jpg, .png, image/jpeg, image/png">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="light_color_code" class="col-md-3 col-form-label text-md-end">{{ trans('main_trans.Light_color') }}</label>
                            <div class="col-md-3">
                                <input id="light_color_code" class="form-control" name="light_color_code" type="color" value="#ffffff">
                            </div>
                            <label for="dark_color_code" class="col-md-3 col-form-label text-md-end">{{ trans('main_trans.Dark_color') }}</label>
                            <div class="col-md-3">
                                <input id="dark_color_code" class="form-control" name="dark_color_code" type="color" value="#000000">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Add_course_subject') }}</button>
                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ trans('main_trans.Close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="modal2">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ trans('main_trans.Edit_course_subject') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form method="POST" action="{{ route('subject-video.update') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Icon') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="icon" id="edit_icon">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Link') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="link" id="edit_link">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Description') }}</label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="description" id="edit_description" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Certificate_types') }}</label>
                            <div class="col-md-8">
                                <select name="types[]" id="edit_types" class="form-control types-select" required multiple>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="edit_icon_photo" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Subject_photo') }}</label>
                            <div class="col-md-8">
                                <!-- <input type=hidden class="dropify" id="edit_icon_photo" name="icon_photo" type="file" data-height="120" accept=".jpg, .png, image/jpeg, image/png"> -->
                                <input class="dropify" id="edit_icon_photo" name="icon_photo" type="file" data-height="120" accept=".jpg, .png, image/jpeg, image/png">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="edit_light_color_code" class="col-md-3 col-form-label text-md-end">{{ trans('main_trans.Light_color') }}</label>
                            <div class="col-md-3">
                                <input id="edit_light_color_code" class="form-control" name="light_color_code" type="color" value="#ffffff">
                            </div>
                            <label for="edit_dark_color_code" class="col-md-3 col-form-label text-md-end">{{ trans('main_trans.Dark_color') }}</label>
                            <div class="col-md-3">
                                <input id="edit_dark_color_code" class="form-control" name="dark_color_code" type="color" value="#000000">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Edit_course_subject') }}</button>
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
                    <h6 class="modal-title">{{ trans('main_trans.Delete_course_subject') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('subject-video.destroy') }}" method="post">
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
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/fancy-uploader.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $('.types-select').select2({ width: '100%' });

        $('#modal2').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $('#edit_id').val(button.data('id'));
            $('#edit_name').val(button.data('name'));
            $('#edit_icon').val(button.data('icon'));
            $('#edit_link').val(button.data('link'));
            $('#edit_description').val(button.data('description'));
            $('#edit_light_color_code').val(button.data('light-color') || '#ffffff');
            $('#edit_dark_color_code').val(button.data('dark-color') || '#000000');
            var types = String(button.data('types') || '').split(',').filter(Boolean);
            $('#edit_types').val(types).trigger('change');

            var photoUrl = button.data('photo-url') || '';
            var $photoInput = $('#edit_icon_photo');
            var drEvent = $photoInput.data('dropify');
            if (drEvent) {
                // drEvent.destroy();
                $photoInput.removeAttr('data-default-file');
            }
            if (photoUrl) {
                $photoInput.attr('data-default-file', photoUrl);
            }
            $photoInput.dropify({ height: 120 });
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
                    url: '{{ route("subjects-video.reorder") }}',
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
