@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Course_subjects') . ' - ' . $type_selected->name }}
@endsection
@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
                </div>

                <div class="card-body">
                    @if($subjectVideos->count() > 0)
                        <div class="table-responsive hoverable-table">
                            <table class="table table-hover" id="example1" data-page-length='50' style="text-align: center;">
                                <thead>
                                <tr>
                                    <th class="wd-5p-f border-bottom-0">#</th>
                                    <th class="wd-5p-f border-bottom-0">{{ trans('main_trans.Icon') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Certificate_types') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_teachers') }}</th>
                                    <th class="wd-30p-f border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($subjectVideos as $subjectVideo)
                                    <tr>
                                        <td>{{ $subjectVideo->id }}</td>
                                        <td>
                                            @if($subjectVideo->icon)
                                                <span class="material-icons" style="font-size: 48px">{{ $subjectVideo->icon }}</span>
                                            @else
                                                <img width="80px" src="{{URL::asset('assets/image/sanad.jpg')}}">
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
                                                <a class="btn btn-success" href="#" title="{{ trans('main_trans.Teachers') }}">
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
                <form method="POST" action="{{ route('subject-video.store') }}" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
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
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Link') }}</label>
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
                            <label class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Certificate_types') }}</label>
                            <div class="col-md-8">
                                <select name="types[]" class="form-control" required multiple>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}" {{ $type->id == $type_selected->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
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
                <form method="POST" action="{{ route('subject-video.update') }}" autocomplete="off">
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
                                <select name="types[]" id="edit_types" class="form-control" required multiple>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
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
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $('#example1').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_',
            }
        });

        $('select[name="types[]"]').select2({ width: '100%' });

        $('#modal2').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $('#edit_id').val(button.data('id'));
            $('#edit_name').val(button.data('name'));
            $('#edit_icon').val(button.data('icon'));
            $('#edit_link').val(button.data('link'));
            $('#edit_description').val(button.data('description'));
            var types = String(button.data('types') || '').split(',').filter(Boolean);
            $('#edit_types').val(types).trigger('change');
        });

        $('#modal3').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            $('#delete_id').val(button.data('id'));
            $('#delete_name').val(button.data('name'));
        });
    </script>
@endsection
