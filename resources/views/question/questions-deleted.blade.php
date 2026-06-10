@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Deleted_questions') }} - {{ $lesson_selected->title }} - {{ $question_group->name }}
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
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Study') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    {{ trans('main_trans.Deleted_questions') }} - {{ $lesson_selected->title }} - {{ $question_group->name }}
                </span>
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
                            <a class="btn btn-primary btn-block" href="{{ url('question-group/' . $question_group->id) }}"><i class="fas fa-arrow-right"></i>{{' ' . trans('main_trans.Back') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example1" data-page-length='100' style=" text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Order') }}</th>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Lesson') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Question_type') }}</th>
                                <th class="wd-20p border-bottom-0">{{ trans('main_trans.Question_text') }}</th>
                                <th class="wd-20p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($archivedQuestions as $question)
                                    <tr>
                                        <td>{{ $question->order }}</td>
                                        <td>{{ $question->id }}</td>
                                        <td>{{ $lesson_selected->title }}</td>
                                        <td>{{ $question->typeQuestion->name }}</td>
                                        <td>{{ $question->questionText }}</td>
                                        <td>
                                            @can('Question-restore-deleted')
                                                <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical"
                                                   data-id="{{ $question->id }}" data-question="{{ $question->questionText }}" data-toggle="modal"
                                                   href="#modal2" title="{{trans('main_trans.Restore')}}"><i class="fas fa-trash-restore"></i></a>
                                            @endcan

                                            @can('Question-delete')
                                                <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                                   data-id="{{ $question->id }}" data-question="{{ $question->questionText }}" data-toggle="modal"
                                                   href="#modal3" title="{{trans('main_trans.Delete')}}"><i class="fas fa-trash"></i></a>
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

        <!-- Restore Modal -->
        <div class="modal" id="modal2">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Restore_question') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('archived-question.update', 'archived-question') }}" method="post">
                        {{ method_field('patch') }}
                        @csrf
                        <div class="modal-body">
                            <p>{{ trans('main_trans.Are_you_sure_to_restore') }}</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="question" id="question" type="text" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                            <button type="submit" class="btn btn-info">{{ trans('main_trans.Restore') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal" id="modal3">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Delete_question') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('archived-question.destroy', 'archived-question') }}" method="post">
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
        $('#modal2').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var question = button.data('question')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #question').val(question);
        })
    </script>

    <script>
        $('#modal3').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var question = button.data('question')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #question').val(question);
        })
    </script>

@endsection
