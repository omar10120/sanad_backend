@extends('layouts.master')
@section('title')
{{ trans('main_trans.Certificate_types') }}
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
							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Coures') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Certificate_types') }}</span>
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

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header pb-0">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-lg-4
                                            @can('Type-show-deleted')
                                                @if(\App\Models\Type::onlyTrashed()->count())
                                                    col-xl-4
                                                @else
                                                    col-xl-6
                                                @endif
                                            @endcan
                                            @cannot('Type-show-deleted') col-xl-6 @endcannot
                                            ">
                                        @can('Type-add')
                                            <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical" data-toggle="modal" href="#modal1">{{ trans('main_trans.Add_type') }}</a>
                                        @endcan
                                    </div>
                                    @can('Type-edit')
                                        <div class="col-12 col-sm-12 col-lg-4 
                                            @can('Type-show-deleted')
                                                @if(\App\Models\Type::onlyTrashed()->count())
                                                    col-xl-4
                                                @else
                                                    col-xl-6
                                                @endif
                                            @endcan
                                            @cannot('Type-show-deleted') col-xl-6 @endcannot
                                        ">
                                            <button id="reorder-btn" class="btn btn-info btn-block">
                                                <i class="fas fa-sort"></i> {{ __('main_trans.Reorder') }}
                                            </button>
                                        </div>
                                    @endcan
                                    @can('Type-show-deleted')
                                        @if(\App\Models\Type::onlyTrashed()->count())
                                            <div class="col-12 col-sm-12 col-lg-4 col-xl-4">
                                                <a class="btn btn-outline-primary btn-block" href="{{ route('archived-type.index') }}">{{ trans('main_trans.Deleted_types') }}</a>
                                            </div>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                            <div class="card-body">
                                @if($types->count() > 0)
                                    <div class="table-responsive hoverable-table">
                                        <table class="table table-hover" id="sortable-table" data-page-length='50' style=" text-align: center;">
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
                                                           title="{{ trans('main_trans.Course_subjects') }}"><i class="fas fa-book"></i> {{ ' ' . trans('main_trans.Course_subjects') }}</a>
                                                    @endcan
                                                    @can('Type-edit')
                                                        <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical"
                                                           data-id="{{ $type->id }}" data-name="{{ $type->name }}" data-toggle="modal"
                                                           href="#modal2" title="{{ trans('main_trans.Edit_type') }}"><i class="fas fa-pen"></i></a>
                                                    @endcan
                                                    @can('Type-edit')
                                                        <form action="{{ route('types.toggle', $type->id) }}" method="POST"
                                                              style="display: initial">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                    class="btn {{ $type->is_active ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                                                <i class="fas {{!$type->is_active ? 'fa-times' : 'fa-check'}} "></i>
                                                                {{ !$type->is_active ? ' ' . trans('main_trans.Disable') : ' ' . trans('main_trans.Enable') }}
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('Type-delete')
                                                        <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                                           data-id="{{ $type->id }}" data-name="{{ $type->name }}" data-toggle="modal"
                                                           href="#modal3" title="{{trans('main_trans.Delete')}}"><i class="fas fa-trash"></i></a>
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
                    <!--/div-->


                    <!-- Add modal -->
                    <div class="modal" id="modal1">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Add_type') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
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

                    <!-- Edit modal -->
                    <div class="modal" id="modal2">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Edit_type') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form method="POST" action="type/update" autocomplete="off">
                                    {{method_field('patch')}}
                                    @csrf
                                    <input type="hidden" name="return_to" value="course-type">
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
                                            <input type="hidden" name="id" id="id" value="">
                                            <div class="col-md-8">
                                                <input id="name" class="form-control" name="name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Edit_type') }}</button>
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
                                    <h6 class="modal-title">{{ trans('main_trans.Delete_type') }}</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                                                    type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="type/destroy" method="post">
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
        var name = button.data('name')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
    })
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
