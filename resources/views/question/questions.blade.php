@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Question_group') }} - {{ $lesson_selected->title }} - {{ $question_group->name }}
@endsection
@section('css')
    <!-- Internal Data table css -->
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">

    <!-- google icon material -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- jQuery UI for drag and drop -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    
    <style>
        .drag-handle {
            cursor: move;
            color: #6c757d;
            font-size: 16px;
            padding: 8px;
            text-align: center;
        }
        
        .ui-sortable-helper {
            background-color: #f8f9fa !important;
            border: 2px dashed #007bff !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
        }
        
        .ui-state-highlight {
            background-color: #e3f2fd !important;
            border: 2px dashed #2196f3 !important;
            height: 50px;
        }
        
        #sortable-body tr:hover .drag-handle {
            color: #007bff;
        }
        
        .reorder-mode .drag-handle {
            display: table-cell !important;
        }
        
        .reorder-mode .btn:not(#reorder-btn) {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Study') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    {{ trans('main_trans.Question_group') }} - {{ $lesson_selected->title }} - {{ $question_group->name }}
                </span>
            </div>
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
                <div class="card-header pb-0 row">
                    <div class="col-12 col-sm-12 col-lg-2 col-xl-2">
                        <div class="pull-right">
                            <a class="btn btn-primary btn-block" href="{{ url('lesson/' . $lesson_selected->id . '/question-group') }}"><i class="fas fa-arrow-right"></i>{{' ' . trans('main_trans.Back') }}</a>
                        </div>
                    </div>
                    @can('Question-add')
                        <div class="col-12 col-sm-12 col-lg-3
                                    @can('Question-show-deleted')
                                        @if($question_group->questions()->onlyTrashed()->count())
                                            col-xl-3
                                        @else
                                            col-xl-5
                                        @endif
                                    @endcan
                                    @cannot('Question-show-deleted') col-xl-5 @endcannot
                                    ">
                            <a class="btn btn-outline-primary btn-block" href="{{ url('question/createInGroup/' . $question_group->id) }}">{{ trans('main_trans.Add_question_to_group') }}</a>
                        </div>
                    @endcan
                    @can('Question-edit')
                        <div class="col-12 col-sm-12 col-lg-3
                                    @can('Question-show-deleted')
                                        @if($question_group->questions()->onlyTrashed()->count())
                                            col-xl-3
                                        @else
                                            col-xl-5
                                        @endif
                                    @endcan
                                    @cannot('Question-show-deleted') col-xl-5 @endcannot
                                    ">
                            <button id="reorder-btn" class="btn btn-info btn-block">
                                <i class="fas fa-sort"></i> {{ __('main_trans.Reorder') }}
                            </button>
                        </div>
                    @endcan
                    @can('Question-show-deleted')
                        @if($question_group->questions()->onlyTrashed()->count())
                            <div class="col-12 col-sm-12 col-lg-5 col-xl-5">
                                <a class="btn btn-outline-primary btn-block" href="{{ route('archived-question.group', $question_group->id) }}">{{ trans('main_trans.Deleted_questions') }}</a>
                            </div>
                        @endif
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="sortable-table" data-page-length='100' style=" text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f drag-handle border-bottom-0" style="display: none;">{{ trans('main_trans.Order') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Order') }}</th>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Lesson') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Question_type') }}</th>
                                <th class="wd-20p border-bottom-0">{{ trans('main_trans.Question_text') }}</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Tags') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Created_by') }}</th>
                                <!-- <th class="wd-10p border-bottom-0">{{ trans('main_trans.Created_at') }}</th> -->
                                <th class="wd-20p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody id="sortable-body">
                                @foreach ($question_group->questions->sortBy('order') as $question)
                                    <tr data-id="{{ $question->id }}">
                                        <td class="drag-handle" style="display: none;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </td>
                                        <td>{{ $question->order }}</td>
                                        <td>{{ $question->id }}</td>
                                        <td>{{ $lesson_selected->title }}</td>
                                        <td>{{ $question->typeQuestion->name }}</td>
                                        <td>{{ $question->questionText }}</td>
                                        <td>
                                            {{ $question->tags->pluck('name')->unique()->implode(', ') ?: '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $createdUser = App\Models\User::withTrashed()->find($question->created_by);
                                            @endphp
                                            {{ $createdUser->name_ar ?? $createdUser->name_en ?? '-' }}
                                        </td>
                                        <!-- <td>{{ $question->created_at ? $question->created_at->format('Y-m-d H:i') : '-' }}</td> -->
                                        <td>
                                            @can('Question-edit')
                                                <a href="{{ route('question.edit', $question->id) }}" class="btn btn-info"
                                                   title="{{ trans('main_trans.Edit') }}"><i class="fas fa-pen"></i></a>
                                            @endcan

                                            @can('Question-edit')
                                                <a class="modal-effect btn btn-purple" data-effect="effect-scale"
                                                   data-question-id="{{ $question->id }}" data-question="{{ $question->questionText }}"
                                                   data-toggle="modal" href="#modaldemo2" title="{{ trans('main_trans.Move_question_group') }}"><i
                                                        class="fas fa-sort"></i></a>
                                            @endcan

                                            @can('Question-delete')
                                                <a class="modal-effect btn btn-danger" data-effect="effect-scale"
                                                   data-id="{{ $question->id }}" data-question="{{ $question->questionText }}"
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

        <!-- Modal moved -->
        <div class="modal" id="modaldemo2">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Sort_question_group') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ url('/question-group/' . $question_group->id . '/sort' ) }}" method="post">
                        {{ method_field('put') }}
                        @csrf
                        <div class="modal-body">
                            <p>{{ trans('main_trans.Are_you_sure_to_sort') }}</p><br>
                            <input type="hidden" name="question_id" id="question-id" value="">
                            <input class="form-control" name="question" id="question" type="text" readonly>
                            <br><p>{{ trans('main_trans.Order') }}</p><br>
                            <input class="form-control" name="new_position" id="new_position" type="number" max="{{ $question_group->questions()->count() }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                            <button type="submit" class="btn btn-purple">{{ trans('main_trans.Sort') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Deleted -->
        <div class="modal" id="modaldemo8">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Delete_question') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('question.destroy', 'test') }}" method="post">
                        {{ method_field('delete') }}
                        @csrf
                        <div class="modal-body">
                            <p>{{ trans('main_trans.Are_you_sure_to_delete') }}</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="question" id="question" type="text" readonly>
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

    <!-- /row -->
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

    <script>
        $('#modaldemo8').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var question = button.data('question')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #question').val(question);
        })
    </script>

    <script>
        $('#modaldemo2').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var question_id = button.data('question-id')
            var question = button.data('question')
            var modal = $(this)
            modal.find('.modal-body #question-id').val(question_id);
            modal.find('.modal-body #question').val(question);
        })
    </script>

    <!-- jQuery UI for drag and drop -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    
    <script>
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
                $('#reorder-btn').html('<i class="fas fa-save"></i> {{ __("main_trans.Save Order") }}').removeClass('btn-info').addClass('btn-success');
                $('.drag-handle').show();
                $('body').addClass('reorder-mode');
                
                // Initialize sortable
                $('#sortable-body').sortable({
                    handle: '.drag-handle',
                    helper: 'clone',
                    placeholder: 'ui-state-highlight',
                    cursor: 'move',
                    opacity: 0.8,
                    update: function(event, ui) {
                        // Update order numbers in the display
                        $('#sortable-body tr').each(function(index) {
                            $(this).find('td:eq(1)').text(index + 1);
                        });
                    }
                });
            }
            
            function exitReorderMode() {
                const orderedIds = [];
                $('#sortable-body tr').each(function() {
                    orderedIds.push($(this).data('id'));
                });
                
                $.ajax({
                    url: '{{ route("questions.reorder", $question_group->id) }}',
                    method: 'POST',
                    data: {
                        ordered_ids: orderedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ __("main_trans.Order updated successfully") }}');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error('{{ __("main_trans.Error updating order") }}');
                        }
                    },
                    error: function() {
                        toastr.error('{{ __("main_trans.Error updating order") }}');
                    }
                });
                
                // Reset UI
                isReorderMode = false;
                $('#reorder-btn').html('<i class="fas fa-sort"></i> {{ __("main_trans.Reorder") }}').removeClass('btn-success').addClass('btn-info');
                $('.drag-handle').hide();
                $('body').removeClass('reorder-mode');
                $('#sortable-body').sortable('destroy');
            }
        });
    </script>

@endsection
