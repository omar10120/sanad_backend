@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Subjects') . ' - ' . $type_selected->name }}
@endsection
@section('css')
    <!---Internal Fileupload css-->
    <link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Internal Data table css -->
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <!-- jQuery UI for drag and drop -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

    <!-- google icon material -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
    .drag-handle {
        cursor: move;
        width: 30px;
        text-align: center;
    }

    .ui-sortable-helper {
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .ui-state-highlight {
        height: 45px;
        background-color: #e3f2fd;
        border: 2px dashed #2196f3;
    }

    #sortable-body tr:hover .drag-handle {
        color: #007bff;
    }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Study') }}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Subjects') . ' - ' . $type_selected->name}}</span>
            </div>
        </div>


    </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

    @include('components.flash-messages')

    <!-- row -->
    <div class="row row-sm">
        <!--div-->
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0 row">
                    <div class="col-12 col-sm-12 col-lg-2 col-xl-2">
                        <div class="pull-right">
                            <a class=" btn btn-primary btn-block" href="{{ url('type') }}"><i class="fas fa-arrow-right"></i>{{' ' . trans('main_trans.Back') }}</a>
                        </div>
                    </div>
                    @can('Subject-add')
                        <div class="col-12 col-sm-12 col-lg-4
                                    @can('Subject-show-deleted')
                                        @if($type_selected->subjects()->onlyTrashed()->count())
                                            col-xl-4
                                        @else
                                            col-xl-6
                                        @endif
                                    @endcan
                                    @cannot('Subject-show-deleted') col-xl-6 @endcannot
                                    ">
                            <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical"
                               data-toggle="modal" href="#modal1">{{ trans('main_trans.Add_subject') }}</a>
                        </div>
                    @endcan
                    @can('Subject-edit')
                        <div class="col-12 col-sm-12 col-lg-4 col-xl-4">
                            <button id="reorder-btn" class="btn btn-info btn-block">
                                <i class="fas fa-sort"></i> {{ __('main_trans.Reorder') }}
                            </button>
                        </div>
                    @endcan
                    @can('Subject-show-deleted')
                        @if($type_selected->subjects()->onlyTrashed()->count())
                            <div class="col-12 col-sm-12 col-lg-4 col-xl-2">
                                <a class="btn btn-outline-primary btn-block" href="{{ route('archived-subject.type', $type_selected->id) }}">{{ trans('main_trans.Deleted_subjects') }}</a>
                            </div>
                        @endif
                    @endcan
                </div>

                <div class="card-body">

                    <!-- row opened -->
                    @if($subjects->count() > 0)
                        <div class="table-responsive hoverable-table">
                            <table class="table table-hover" id="sortable-table" data-page-length='50'
                                   style=" text-align: center;">
                                <thead>
                                <tr>
                                    <th class="wd-5p-f drag-handle border-bottom-0" style="display: none;">{{ trans('main_trans.Order') }}</th>
                                    <th class="wd-5p-f border-bottom-0">#</th>
                                    {{-- <th class="wd-5p-f border-bottom-0">{{ trans('main_trans.Icon') }}</th> --}}
                                    <th class="wd-5p-f border-bottom-0">{{ trans('main_trans.Subject_photo') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Certificate_types') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_lessons') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_tags') }}</th>
                                    {{--                                            <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_tags') }}</th>--}}
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_questions') }}</th>
                                    <th class="wd-30p-f border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody id="sortable-body">
                                @foreach ($subjects->sortBy('order') as $subject)
                                <tr data-id="{{ $subject->id }}" style="cente">
                                    <td class="drag-handle" style="display: none;">
                                        <i class="fas fa-grip-vertical text-muted"></i>
                                    </td>
                                    <td>{{ $subject->id }}</td>
                                    {{-- <td>
                                        <span class="material-icons mg-md-20"
                                              style="font-size: 48px">{{$subject->icon}}</span>
                                    </td> --}}
                                    <td>
                                        @if($subject->icon_photo == null)
                                            <img width="200px" size="200px" src="{{URL::asset('assets/image/sanad.jpg')}}">
                                        @else
                                            <img width="100px" src="{{URL::asset('assets/image/Subjects/' . $subject->id . '/' . $subject->icon_photo)}}">
                                        @endif
                                    </td>
                                    <td>
                                        <b>
                                            {{$subject->name}}
                                        </b>
                                    </td>
                                    <td>
                                        @foreach ($subject->types as $t)
                                            <label class="badge badge-purple"
                                                   style="font-size: 14px !important; padding: 8px !important;">{{ $t->name }}</label>
                                        @endforeach
                                    </td>
                                    <td>{{ $subject->lessons->count() }}</td>
                                    <td>{{ $subject->tags->count() }}</td>
                                    <td>{{ $subject->questionsCount() }}</td>
                                    <td>
                                        @can('Lesson-show')
                                            <a class="btn btn-success"
                                               href="{{ route('subject.lesson', $subject->id )}}"
                                               title="{{trans('main_trans.Lessons')}}"><i
                                                    class="fas fa-book"></i>{{ ' ' . trans('main_trans.Lessons')}}</a>
                                        @endcan
                                        @can('Tag-show')
                                            <a class="btn btn-secondary" href="{{ route('subject.tag', $subject->id )}}"
                                               title="{{trans('main_trans.Tags')}}"><i
                                                    class="fas fa-tag"></i>{{ ' ' . trans('main_trans.Tags')}}</a>
                                        @endcan
                                        @can('Subject-edit')
                                            <a class="btn btn-info my-1" href="{{route('subject.edit', $subject->id)}}"
                                               title="{{trans('main_trans.Edit')}}"><i class="fas fa-pen"></i></a>
                                        @endcan

                                        @can('Subject-edit')
                                            <form action="{{ route('subjects.toggle', $subject->id) }}" method="POST"
                                                  style="display: initial">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="btn {{ $subject->is_active ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                                    <i class="fas {{!$subject->is_active ? 'fa-times' : 'fa-check'}} "></i>
                                                    {{ !$subject->is_active ? ' ' . trans('main_trans.Disable') : ' ' . trans('main_trans.Enable') }}
                                                </button>
                                            </form>
                                        @endcan

                                        @can('Subject-delete')
                                            <a class="modal-effect btn btn-danger my-1"
                                               data-effect="effect-flip-vertical"
                                               data-id="{{ $subject->id }}" data-name="{{ $subject->name }}"
                                               data-toggle="modal"
                                               href="#modal3" title="{{trans('main_trans.Delete')}}"><i
                                                    class="fas fa-trash"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5>{{ trans('main_trans.No_Subjects_Available') }}</h5>
                            <p>{{ trans('main_trans.No_Subjects_Available_Description') }}</p>
                        </div>
                    @endif

                </div><!-- bd -->
            </div><!-- bd -->
        </div>
        <!--/div-->

        <!-- Add modal -->
        <div class="modal" id="modal1">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Add_subject') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <form method="POST" action="{{ route('subject.store') }}" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="name"
                                       class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
                                <div class="col-md-8">
                                    <input id="name" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="icon"
                                       class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Icon') }}</label>
                                <div class="col-md-8">
                                    <input id="icon" class="form-control" name="icon">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="link"
                                       class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Link') }}</label>
                                <div class="col-md-8">
                                    <input id="link" class="form-control" name="link">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="teacher"
                                       class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Teacher') }}</label>
                                <div class="col-md-8">
                                    <input id="teacher" class="form-control" name="teacher">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="description"
                                       class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Description') }}</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" name="description" id="description" cols="30"
                                              rows="5"></textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="types"
                                       class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Certificate_types') }}</label>
                                <div class="col-md-8">
                                    <select name="types[]" id="types" class="form-control" required multiple>
                                        @foreach ($types as $type)
                                            <option value="{{$type->id}}">{{$type->name}}</option>
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
                            <button class="btn ripple btn-primary"
                                    type="submit">{{ trans('main_trans.Add_subject') }}</button>
                            <button class="btn ripple btn-secondary" data-dismiss="modal"
                                    type="button">{{ trans('main_trans.Close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add modal -->

        <!-- delete -->
        <div class="modal" id="modal3">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Delete_subject') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal"
                                type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="../subject/destroy" method="post">
                        {{method_field('delete')}}
                        {{csrf_field()}}
                        <div class="modal-body">
                            <p>{{ trans('main_trans.Are_you_sure_to_delete') }}</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <div class="row mb-3 mx-1">
                                <input class="form-control" name="name" id="name" type="text" readonly>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                            <button type="submit" class="btn btn-danger">{{ trans('main_trans.Delete') }}</button>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End delete -->

    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!-- Internal Data tables -->
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
    <!--Internal  Datatable js -->
    <script src="{{URL::asset('assets/js/table-data.js')}}"></script>
    <!-- Internal Modal js-->
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>

    <!--Internal Fileuploads js-->
    <script src="{{URL::asset('assets/plugins/fileuploads/js/fileupload.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fileuploads/js/file-upload.js')}}"></script>
    <!--Internal Fancy uploader js-->
    <script src="{{URL::asset('assets/plugins/fancyuploder/jquery.ui.widget.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fancyuploder/jquery.fileupload.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fancyuploder/jquery.iframe-transport.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fancyuploder/fancy-uploader.js')}}"></script>
    <!-- jQuery UI for drag and drop -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $('#modal3').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var name = button.data('name')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #name').val(name);
        })

        // Drag and drop functionality
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
                    update: function(event, ui) {
                        // Visual feedback during drag
                    }
                }).disableSelection();
            }

            function exitReorderMode() {
                const orderedIds = [];
                $('#sortable-body tr').each(function() {
                    orderedIds.push($(this).data('id'));
                });

                // AJAX call to save new order
                $.ajax({
                    url: '{{ route("subjects.reorder") }}',
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
