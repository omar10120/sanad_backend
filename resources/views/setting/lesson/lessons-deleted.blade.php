@extends('layouts.master')
@section('title')
{{ trans('main_trans.Deleted_lessons') }} - {{$subject_selected->name}}
@endsection
@section('css')
<!-- Internal Data table css -->
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Study') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Deleted_lessons') }} - {{$subject_selected->name}}</span>
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
                            <div class="card-header pb-0">
                                <div class="main-content-label mg-b-5">
                                    <div class="pull-right">
                                        <a class="btn btn-primary btn-sm" href="{{ url('subject-lesson/' . $subject_selected->id) }}"><i class="fas fa-arrow-right"></i>{{' ' . trans('main_trans.Back') }}</a>
                                    </div>
                                </div>
                            </div>
							<div class="card-body">
                                <!-- row opened -->
                                <div class="table-responsive hoverable-table">
                                    <table class="table table-hover" id="example1" data-page-length='100' style=" text-align: center;">
                                        <thead>
                                        <tr>
                                            <th class="wd-5p-f border-bottom-0">#</th>
                                            <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                            <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_questions') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($lessons as $lesson)
                                            <tr>
                                                <td>{{ $lesson->id }}</td>
                                                <td>{{ $lesson->title }}</td>
{{--                                                <td>{{ \App\Models\Lesson::withCount('questionGroups')->find($lesson->id)->question_groups_count }}</td>--}}
                                                <td>{{ \App\Models\Lesson::withCount('questionGroups')->withTrashed()->find($lesson->id)->question_groups_count }}</td>
                                                <td>
                                                    @can('Lesson-restore-deleted')
                                                        <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical"
                                                           data-id="{{ $lesson->id }}" data-title="{{ $lesson->title }}" data-toggle="modal"
                                                           href="#modal2" title="{{trans('main_trans.Restore')}}"><i class="fas fa-trash-restore"></i></a>
                                                    @endcan
                                                    @can('Lesson-restore-deleted')
                                                        <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                                           data-id="{{ $lesson->id }}" data-title="{{ $lesson->title }}" data-toggle="modal"
                                                           href="#modal3" title="{{trans('main_trans.Delete')}}"><i class="fas fa-trash"></i></a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

{{--                                <div class="row row-sm">--}}

{{--                                    @foreach ($lessons as $x)--}}

{{--                                        <div class="col-xl-2 col-lg-2 col-md-12">--}}
{{--                                            <div class="card text-center">--}}
{{--                                                <div class="card-body">--}}
{{--                                                    <h4 class="card-title mb-3">{{$x->id}} - {{$x->title}}</h4>--}}
{{--                                                    <h4 class="card-title mb-3">{{$x->subject->name}}</h4>--}}
{{--                                                    <h4 class="card-title mb-3">{{trans('main_trans.Number_of_questions')}} : {{\App\Models\Lesson::withCount('questions')->find($x->id)->questions_count}}</h4>--}}
{{--    --}}
{{--                                                    @can('Lesson-edit')--}}
{{--                                                        <a class="modal-effect btn btn-sm btn-info" data-effect="effect-flip-vertical"--}}
{{--                                                           data-id="{{ $x->id }}" data-title="{{ $x->title }}" data-subject_selected="{{ $x->subject->id }}" data-toggle="modal"--}}
{{--                                                           href="#modal2" title="{{trans('main_trans.Edit')}}"><i class="las la-pen"></i></a>--}}
{{--                                                    @endcan--}}
{{--                                                    @can('Lesson-delete')--}}
{{--                                                        <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-flip-vertical"--}}
{{--                                                           data-id="{{ $x->id }}" data-title="{{ $x->title }}" data-toggle="modal"--}}
{{--                                                           href="#modal3" title="{{trans('main_trans.Delete')}}"><i class="las la-trash"></i></a>--}}
{{--                                                    @endcan--}}
{{--                                                    @can('Question-add')--}}
{{--                                                        <a class="btn btn-sm btn-success" href="{{ route('question.createWithLesson', $x->id )}}" title="{{trans('main_trans.Add_question')}}"><i class="las la-plus-circle"></i></a>--}}
{{--                                                    @endcan--}}
{{--                                                    </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

{{--                                        <div class="col-12 col-sm-6 col-lg-6 col-xl-3">--}}
{{--                                            <div class="card text-center--}}
{{--                                                @if($x->subject->id%5==1)--}}
{{--                                                    card-purple--}}
{{--                                                @elseif($x->subject->id%5==2)--}}
{{--                                                    card-info--}}
{{--                                                @elseif($x->subject->id%5==3)--}}
{{--                                                    card-warning--}}
{{--                                                @elseif($x->subject->id%5==4)--}}
{{--                                                    card-success--}}
{{--                                                @elseif($x->subject->id%5==0)--}}
{{--                                                    card-danger--}}
{{--                                                @endif--}}
{{--                                            ">--}}
{{--                                                <div class="card-header pb-0">--}}
{{--                                                    <h5 class="card-title mb-0 pb-0">{{$x->id}} - {{$x->title}}</h5>--}}
{{--                                                </div>--}}
{{--                                                <div class="card-body--}}
{{--                                                    @if($x->subject->id%5==1)--}}
{{--                                                        text-purple--}}
{{--                                                    @elseif($x->subject->id%5==2)--}}
{{--                                                        text-info--}}
{{--                                                    @elseif($x->subject->id%5==3)--}}
{{--                                                        text-warning--}}
{{--                                                    @elseif($x->subject->id%5==4)--}}
{{--                                                        text-success--}}
{{--                                                    @elseif($x->subject->id%5==0)--}}
{{--                                                        text-danger--}}
{{--                                                    @endif--}}
{{--                                                ">--}}
{{--                                                    {{trans('main_trans.Subject')}} : {{$x->subject->name}}--}}
{{--                                                    <br>--}}
{{--                                                    {{trans('main_trans.Number_of_questions')}} : {{\App\Models\Lesson::withCount('questions')->find($x->id)->questions_count}}--}}
{{--                                                </div>--}}
{{--                                                <div class="card-footer">--}}
{{--                                                    @can('Lesson-edit')--}}
{{--                                                        <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical"--}}
{{--                                                           data-id="{{ $x->id }}" data-title="{{ $x->title }}" data-subject_selected="{{ $x->subject->id }}" data-toggle="modal"--}}
{{--                                                           href="#modal2" title="{{trans('main_trans.Edit')}}"><i class="las la-pen"></i></a>--}}
{{--                                                    @endcan--}}
{{--                                                    @can('Lesson-delete')--}}
{{--                                                        <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"--}}
{{--                                                           data-id="{{ $x->id }}" data-title="{{ $x->title }}" data-toggle="modal"--}}
{{--                                                           href="#modal3" title="{{trans('main_trans.Delete')}}"><i class="las la-trash"></i></a>--}}
{{--                                                    @endcan--}}
{{--                                                    @can('Question-show')--}}
{{--                                                        <a class="btn btn-secondary" href="{{ route('lesson.question', $x->id )}}" title="{{trans('main_trans.Questions')}}"><i class="las la-question"></i></a>--}}
{{--                                                    @endcan--}}
{{--                                                    @can('Question-add')--}}
{{--                                                        <a class="btn btn-success" href="{{ route('question.createWithLesson', $x->id )}}" title="{{trans('main_trans.Add_question')}}"><i class="las la-plus-circle"></i></a>--}}
{{--                                                    @endcan--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endforeach--}}

{{--                                </div>--}}
                                <!-- row closed -->
							</div><!-- bd -->
						</div><!-- bd -->
					</div>
					<!--/div-->

                    <!-- Restore -->
                    <div class="modal" id="modal2">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Restore_lesson') }}</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                                                                                type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="{{route('archived-lesson.update','archived-lesson')}}" method="post">
                                    {{method_field('patch')}}
                                    {{csrf_field()}}
                                    <div class="modal-body">
                                        <p>{{ trans('main_trans.Are_you_sure_to_restore') }}</p><br>
                                        <input type="hidden" name="id" id="id" value="">
                                        <div class="row mb-3 mx-1">
                                            <input class="form-control" name="title" id="title" type="text" readonly>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                                        <button type="submit" class="btn btn-info">{{ trans('main_trans.Restore') }}</button>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
<!-- End delete -->

                    <!-- delete -->
                    <div class="modal" id="modal3">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Delete_lesson') }}</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                                                    type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="{{route('archived-lesson.destroy','archived-lesson')}}" method="post">
                                    {{method_field('delete')}}
                                    {{csrf_field()}}
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
                            </div>
                            </form>
                        </div>
                    </div>
				</div>
                <!-- End delete -->

				</div>
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

<script>
    $('#modal2').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var title = button.data('title')
        var subject_selected = button.data('subject_selected')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #title').val(title);
        modal.find('.modal-body #subject_selected').val(subject_selected);
    })
    $('#modal3').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var title = button.data('title')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #title').val(title);
    })
</script>

@endsection
