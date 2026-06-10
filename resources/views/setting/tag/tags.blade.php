@extends('layouts.master')
@section('title')
{{ trans('main_trans.Tags') }} - {{$subject_selected->name}}
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
							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Study') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Tags') }} - {{$subject_selected->name}}</span>
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
                            <div class="card-header pb-0 row" style="margin-right: 0; margin-left: 0;">
                                <div class="col-12 col-sm-12 col-lg-2 col-xl-2">
                                    <div class="pull-right">
                                        <a class="btn btn-primary btn-block" href="{{ route('type.subject', $subject_selected->types->first->id )}}"><i class="fas fa-arrow-right"></i>{{' ' . trans('main_trans.Back') }}</a>
                                    </div>
                                </div>
                                @can('Tag-add')
                                    <div class="col-12 col-sm-12 col-lg-4
                                                @can('Tag-show-deleted')
                                                    @if(isset($subject_selected) && $subject_selected->tags()->onlyTrashed()->count())
                                                        col-xl-4
                                                    @else
                                                        col-xl-6
                                                    @endif
                                                @endcan
                                                @cannot('Tag-show-deleted') col-xl-6 @endcannot
                                                ">
                                        <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical"
                                           data-toggle="modal" href="#modal1">{{ trans('main_trans.Add_tag') }}</a>
                                    </div>
                                @endcan
                                @can('Tag-edit')
                                    <div class="col-12 col-sm-12 col-lg-4 col-xl-4">
                                        <button id="reorder-btn" class="btn btn-info btn-block">
                                            <i class="fas fa-sort"></i> {{ __('main_trans.Reorder') }}
                                        </button>
                                    </div>
                                @endcan
                                @can('Tag-show-deleted')
                                    @if(isset($subject_selected) && $subject_selected->tags()->onlyTrashed()->count())
                                        <div class="col-12 col-sm-12 col-lg-4 col-xl-2">
                                            <a class="btn btn-outline-primary btn-block" href="{{ route('archived-tag.subject', $subject_selected->id) }}">{{ trans('main_trans.Deleted_tags') }}</a>
                                        </div>
                                    @endif
                                @endcan
                            </div>
							<div class="card-body">
                                <!-- row opened -->
                                <div class="table-responsive hoverable-table">
                                    <table class="table table-hover" id="sortable-table" data-page-length='50' style=" text-align: center;">
                                        <thead>
                                        <tr>
                                            <th class="wd-5p-f drag-handle border-bottom-0" style="display: none;">{{ trans('main_trans.Order') }}</th>
                                            <th class="wd-5p-f border-bottom-0">#</th>
                                            <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                            <th class="wd-5p border-bottom-0">{{ trans('main_trans.Type') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody id="sortable-body">
                                        @foreach ($tags->sortBy('order') as $tag)
                                            <tr data-id="{{ $tag->id }}">
                                                <td class="drag-handle" style="display: none;">
                                                    <i class="fas fa-grip-vertical text-muted"></i>
                                                </td>
                                                <td>{{ $tag->id }}</td>
                                                <td>{{ $tag->name }}</td>
                                                <td>
                                                    @if($tag->is_exam)
                                                        <h4 class="mb-3 badge badge-purple-transparent" style="font-size: 15px !important;">{{trans('main_trans.Exam')}}</h4>
                                                    @else
                                                        <h4 class="mb-3 badge badge-pink-transparent" style="font-size: 15px !important;">{{trans('main_trans.Tag_')}}</h4>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('Tag-edit')
                                                        <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical" data-id="{{ $tag->id }}" data-name="{{ $tag->name }}"
                                                           data-subject_selected="{{ $tag->subject->id }}" data-is_exam="{{ $tag->is_exam ? 'true' : 'false' }}" data-toggle="modal"
                                                           href="#modal2" title="{{trans('main_trans.Edit')}}"><i class="fas fa-pen"></i> {{' ' . trans('main_trans.Edit')}}</a>
                                                    @endcan
                                                    @can('Tag-delete')
                                                        <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                                           data-id="{{ $tag->id }}" data-name="{{ $tag->name }}" data-toggle="modal"
                                                           href="#modal3" title="{{trans('main_trans.Delete')}}"><i class="fas fa-trash"></i> {{' ' . trans('main_trans.Delete')}}</a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
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
                                    <h6 class="modal-title">{{ trans('main_trans.Add_tag') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="{{route('tag.store')}}" method="post">
                                    {{csrf_field()}}
                                    <div class="modal-body">
                                        <div class="row mb-3 mx-1">
                                            <label for="name" class="col-sm-2 col-form-label">{{ trans('main_trans.Name') }}</label>
                                            <div class="col-sm-10">
                                                <input class="form-control" name="name" id="name" type="text" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mx-1">
                                            <label for="subject_id" class="col-sm-2 col-form-label">{{ trans('main_trans.Subject') }}</label>
                                            <div class="col-sm-10">
                                                <select class="form-control" name="subject_id" id="subject_id" required>
                                                    <option value="">{{ trans('main_trans.Select_subject') }}</option>
                                                    @foreach($subjects as $subject)
                                                        <option value="{{ $subject->id }}" {{ $subject->id == $subject_selected->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mx-1">
                                            <label for="is_exam" class="col-sm-2 col-form-label">{{ trans('main_trans.Is_exam') }}</label>
                                            <div class="col-sm-10">
                                                <input type="checkbox" name="is_exam" id="is_exam" value="1">
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
                    <!-- End Add modal -->

                    <!-- Edit modal -->
                    <div class="modal" id="modal2">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Edit_tag') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="{{route('tag.update','tag')}}" method="post">
                                    {{method_field('patch')}}
                                    {{csrf_field()}}
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="id" value="">
                                        <div class="row mb-3 mx-1">
                                            <label for="name" class="col-sm-2 col-form-label">{{ trans('main_trans.Name') }}</label>
                                            <div class="col-sm-10">
                                                <input class="form-control" name="name" id="name" type="text" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mx-1">
                                            <label for="subject_id" class="col-sm-2 col-form-label">{{ trans('main_trans.Subject') }}</label>
                                            <div class="col-sm-10">
                                                <select class="form-control" name="subject_id" id="subject_id" required>
                                                    <option value="">{{ trans('main_trans.Select_subject') }}</option>
                                                    @foreach($subjects as $subject)
                                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mx-1">
                                            <label for="is_exam" class="col-sm-2 col-form-label">{{ trans('main_trans.Is_exam') }}</label>
                                            <div class="col-sm-10">
                                                <input type="checkbox" name="is_exam" id="is_exam" value="1">
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
                    <!-- End Edit modal -->

                    <!-- delete -->
                    <div class="modal" id="modal3">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Delete_tag') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="{{route('tag.destroy','tag')}}" method="post">
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
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                                        <button type="submit" class="btn btn-danger">{{ trans('main_trans.Delete') }}</button>
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
    $('#modal1').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var modal = $(this)
        modal.find('.modal-body #name').val('');
        modal.find('.modal-body #subject_id').val('{{ $subject_selected->id }}');
        modal.find('.modal-body #is_exam').prop('checked', false);
    })
    $('#modal2').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var subject_selected = button.data('subject_selected');
        var is_exam = button.data('is_exam');

        var modal = $(this);
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #subject_id').val(subject_selected);
        modal.find('.modal-body #is_exam').prop('checked', is_exam === 'true' || is_exam === true);
    });
    $('#modal3').on('show.bs.modal', function(event) {
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
                url: '{{ route("tags.reorder", $subject_selected->id) }}',
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
