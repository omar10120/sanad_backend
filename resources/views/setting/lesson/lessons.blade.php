@extends('layouts.master')
@section('title')
{{ trans('main_trans.Lessons') }} - {{$subject_selected->name}}
@endsection
@section('css')
<!-- Internal Data table css -->
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
<!-- jQuery UI for drag and drop -->
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
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Study') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Lessons') }} - {{$subject_selected->name}}</span>
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
                                        <a class=" btn btn-primary btn-block" href="{{ url('type-subject/' . $subject_selected->types[0]->id) }}"><i class="fas fa-arrow-right"></i>{{' ' . trans('main_trans.Back') }}</a>
                                    </div>
                                </div>
                                @can('Lesson-add')
                                    <div class="col-12 col-sm-12 col-lg-4
                                    @can('Lesson-show-deleted')
                                        @if($subject_selected->lessons()->onlyTrashed()->count())
                                            col-xl-4
                                        @else
                                            col-xl-6
                                        @endif
                                    @endcan
                                    @cannot('Lesson-show-deleted') col-xl-6 @endcannot
                                    ">
                                        <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical" data-toggle="modal" href="#modal1">{{ trans('main_trans.Add_lesson') }}</a>
                                    </div>
                                @endcan
                                @can('Lesson-edit')
                                    <div class="col-12 col-sm-12 col-lg-4 col-xl-4">
                                        <button id="reorder-btn" class="btn btn-info btn-block">
                                            <i class="fas fa-sort"></i> {{ __('main_trans.Reorder') }}
                                        </button>
                                    </div>
                                @endcan
                                @can('Lesson-show-deleted')
                                    @if($subject_selected->lessons()->onlyTrashed()->count())
                                        <div class="col-12 col-sm-12 col-lg-4 col-xl-2">
                                            <a class="btn btn-outline-primary btn-block" href="{{ route('archived-lesson.show', $subject_selected->id) }}">{{ trans('main_trans.Deleted_lessons') }}</a>
                                        </div>
                                    @endif
                                @endcan
                            </div>
							<div class="card-body">
                                <!-- row opened -->
                                <div class="table-responsive hoverable-table">
                                    <table class="table table-hover" id="sortable-table" data-page-length='100' style=" text-align: center;">
                                        <thead>
                                        <tr>
                                            <th class="wd-5p-f drag-handle border-bottom-0" style="display: none;">{{ trans('main_trans.Order') }}</th>
                                            <th class="wd-5p-f border-bottom-0">#</th>
                                            <th class="wd-10p-f border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                            <th class="wd-5p-f border-bottom-0">{{ trans('main_trans.Number_of_questions') }}</th>
                                            <th class="wd-5p-f border-bottom-0">{{ trans('main_trans.Number_of_question_groups') }}</th>
                                            <th class="wd-15p-f border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody id="sortable-body">
                                        @foreach ($lessons->sortBy('order') as $lesson)
                                            <tr data-id="{{ $lesson->id }}">
                                                <td class="drag-handle" style="display: none;">
                                                    <i class="fas fa-grip-vertical text-muted"></i>
                                                </td>
                                                <td>{{ $lesson->id }}</td>
                                                <td>{{ $lesson->title }}</td>
                                                <td>{{ $lesson->questions()->count() }}</td>
                                                <td>{{ \App\Models\Lesson::withCount('questionGroups')->find($lesson->id)->question_groups_count }}</td>
                                                <td>
                                                    @can('Question-show')
                                                        <a class="btn btn-secondary" href="{{ route('lesson.questionGroup', $lesson->id )}}"
                                                           title="{{trans('main_trans.Questions')}}"><i class="fas fa-question"></i> {{' ' . trans('main_trans.Questions')}}</a>
                                                    @endcan
                                                    @can('Question-add')
                                                        <a class="btn btn-success" href="{{ route('question.createWithLesson', $lesson->id )}}"
                                                           title="{{trans('main_trans.Add_question')}}"><i class="fas fa-plus-circle"></i> {{' ' . trans('main_trans.Add_question')}}</a>
                                                    @endcan
                                                    @can('Lesson-edit')
                                                        <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical" data-id="{{ $lesson->id }}"
                                                           data-title="{{ $lesson->title }}" data-subject_selected="{{ $lesson->subject->id }}" data-toggle="modal"
                                                           href="#modal2" title="{{trans('main_trans.Edit')}}"><i class="fas fa-pen"></i></a>
                                                    @endcan
                                                    @can('Lesson-edit')
                                                        <form action="{{ route('lessons.toggle', $lesson->id) }}" method="POST"
                                                              style="display: initial">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                    class="btn {{ $lesson->is_active ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                                                <i class="fas {{!$lesson->is_active ? 'fa-times' : 'fa-check'}} "></i>
                                                                {{ !$lesson->is_active ? ' ' . trans('main_trans.Disable') : ' ' . trans('main_trans.Enable') }}
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('Lesson-delete')
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

                    <!-- Add modal -->
                    <div class="modal" id="modal1">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Add_lesson') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form method="POST" action="{{ route('lesson.store') }}" autocomplete="off">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <label for="title" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Title') }}</label>
                                            <div class="col-md-8">
                                                <input id="title" class="form-control" name="title">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="subject_id" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Subject') }}</label>
                                            <div class="col-md-8">
                                                <select name="subject_id" id="subject_id" class="form-control" required>
                                                    <option value="" disabled>{{ trans('main_trans.Select_subject') }}</option>
                                                    @foreach ($subjects as $subject)
                                                        <option value="{{$subject->id}}"
                                                        @if($subject_selected->id == $subject->id)
                                                            selected
                                                        @endif
                                                        >{{$subject->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Add_lesson') }}</button>
                                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ trans('main_trans.Close') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Add modal -->

                    <!-- Edit modal -->
                    <div class="modal" id="modal2">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Edit_lesson') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form method="POST" action="../lesson/update" autocomplete="off">
                                    {{method_field('patch')}}
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <label for="title" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Title') }}</label>
                                            <input type="hidden" name="id" id="id" value="">
                                            <div class="col-md-8">
                                                <input id="title" class="form-control" name="title">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="subject_id" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Subject') }}</label>
                                            <div class="col-md-8">
                                                <select name="subject_id" id="subject_id" class="form-control" required>
                                                    <option id="subject_selected" value="">{{ trans('main_trans.Not_change') }}</option>
                                                    @foreach ($subjects as $subject)
                                                        <option value="{{$subject->id}}">{{$subject->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Edit_lesson') }}</button>
                                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ trans('main_trans.Close') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Edit modal -->

                    <!-- delete -->
                    <div class="modal" id="modal3">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Delete_lesson') }}</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                                                    type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="../lesson/destroy" method="post">
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
<!-- jQuery UI for drag and drop -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

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
                url: '{{ route("lessons.reorder", $subject_selected->id) }}',
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
