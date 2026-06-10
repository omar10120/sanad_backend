@extends('layouts.master')
@section('title')
{{ trans('main_trans.Deleted_subjects') }}
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
							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Study') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Deleted_subjects') }}</span>
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
                                        @if(isset($type_selected))
                                            <a class="btn btn-primary btn-sm" href="{{ route('type.subject', $type_selected->id) }}"><i class="fas fa-arrow-right"></i>{{' ' . trans('main_trans.Back') }}</a>
                                        @else
                                            <a class="btn btn-primary btn-sm" href="{{ route('type.index') }}"><i class="fas fa-arrow-right"></i>{{' ' . trans('main_trans.Back') }}</a>
                                        @endif
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
                                            <th class="wd-15p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                            <th class="wd-15p border-bottom-0">{{ trans('main_trans.Teacher') }}</th>
                                            <th class="wd-20p border-bottom-0">{{ trans('main_trans.Description') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ trans('main_trans.Status') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($archivedSubjects as $subject)
                                            <tr>
                                                <td>{{ $subject->id }}</td>
                                                <td>{{ $subject->name }}</td>
                                                <td>{{ $subject->teacher }}</td>
                                                <td>{{ Str::limit($subject->description, 50) }}</td>
                                                <td>
                                                    @if($subject->is_active)
                                                        <span class="badge badge-success">{{ trans('main_trans.Active') }}</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ trans('main_trans.Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('Subject-restore-deleted')
                                                        <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical"
                                                           data-id="{{ $subject->id }}" data-name="{{ $subject->name }}" data-toggle="modal"
                                                           href="#modal2" title="{{trans('main_trans.Restore')}}"><i class="fas fa-trash-restore"></i></a>
                                                    @endcan
                                                    @can('Subject-delete')
                                                        <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                                           data-id="{{ $subject->id }}" data-name="{{ $subject->name }}" data-toggle="modal"
                                                           href="#modal3" title="{{trans('main_trans.Delete')}}"><i class="fas fa-trash"></i></a>
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

                    <!-- Restore -->
                    <div class="modal" id="modal2">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Restore_subject') }}</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                                                                                type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="{{route('archived-subject.update','archived-subject')}}" method="post">
                                    {{method_field('patch')}}
                                    {{csrf_field()}}
                                    <div class="modal-body">
                                        <p>{{ trans('main_trans.Are_you_sure_to_restore') }}</p><br>
                                        <input type="hidden" name="id" id="id" value="">
                                        <div class="row mb-3 mx-1">
                                            <input class="form-control" name="name" id="name" type="text" readonly>
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
<!-- End restore -->

                    <!-- delete -->
                    <div class="modal" id="modal3">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Delete_subject') }}</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                                                    type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="{{route('archived-subject.destroy','archived-subject')}}" method="post">
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
</script>

@endsection
