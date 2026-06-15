@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Certificate_types') }}
@endsection
@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
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
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Coures') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Certificate_types') }}</span>
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
                    <div class="row">
                        <div class="col-12 col-sm-12 col-lg-6 col-xl-6">
                            @can('Type-add')
                                <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical" data-toggle="modal" href="#modal1">{{ trans('main_trans.Add_type') }}</a>
                            @endcan
                        </div>
                        @can('Type-edit')
                            <div class="col-12 col-sm-12 col-lg-6 col-xl-6">
                                <button id="reorder-btn" class="btn btn-info btn-block">
                                    <i class="fas fa-sort"></i> {{ __('main_trans.Reorder') }}
                                </button>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    @if($types->count() > 0)
                        <div class="table-responsive hoverable-table">
                            <table class="table table-hover" id="sortable-table" data-page-length='50' style="text-align: center;">
                                <thead>
                                <tr>
                                    <th class="wd-5p-f drag-handle border-bottom-0" style="display: none;">{{ trans('main_trans.Order') }}</th>
                                    <th class="wd-5p-f border-bottom-0">#</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_course_subjects') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody id="sortable-body">
                                @foreach ($types->sortBy('order') as $type)
                                    <tr data-id="{{ $type->id }}">
                                        <td class="drag-handle" style="display: none;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>{{ $type->id }}</td>
                                        <td>{{ $type->name }}</td>
                                        <td>{{ \App\Models\Type::withCount('subjectVideos')->find($type->id)->subject_videos_count }}</td>
                                        <td>
                                            @can('SubjectVideo-show')
                                                <a class="btn btn-success" href="{{ route('type.subject-video', $type->id) }}"
                                                   title="{{ trans('main_trans.Course_subjects') }}">
                                                    <i class="fas fa-book"></i> {{ trans('main_trans.Course_subjects') }}
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
                            <h5>{{ trans('main_trans.No_Types_Available') }}</h5>
                            <p>{{ trans('main_trans.No_Types_Available_Description') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add modal -->
        <div class="modal" id="modal1">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Add_type') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form method="POST" action="{{ route('type.store') }}" autocomplete="off">
                        @csrf
                        <input type="hidden" name="return_to" value="course-type">
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
                                <div class="col-md-8">
                                    <input id="name" class="form-control" name="name">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Add_type') }}</button>
                            <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ trans('main_trans.Close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add modal -->
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $('#sortable-table').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_',
            }
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
                    url: '{{ route("types.reorder") }}',
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
