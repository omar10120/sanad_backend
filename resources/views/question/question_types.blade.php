@extends('layouts.master')
@section('title')
{{ trans('main_trans.Question_types') }}
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
							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Questions') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Question_types') }}</span>
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
							<div class="card-header pb-0">
								@can('Type-add')
									<div class="d-flex justify-content-between">
										<a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical" data-toggle="modal" href="#modal1">{{ trans('main_trans.Add_type') }}</a>
									</div>
								@endcan
							</div>
							<div class="card-header pb-0">

							</div>
							<div class="card-body">
								<!-- row opened -->

								<div class="row row-sm">

									@foreach ($types as $x)

									<div class="col-xl-2 col-lg-2 col-md-12">
										<div class="card text-center">
{{--                                            <img class="card-img-top w-100" src="{{URL::asset('assets/image/Types/' . $x->id . '/' . $x->photo_name)}}" alt="">--}}
											<div class="card-body">
												<h4 class="card-title mb-1">{{$x->id}} - {{$x->name}}</h4>
												<p class="mb-3">
													<span class="badge badge-{{ $x->type === 'Automation' ? 'success' : ($x->type === 'NotAutomation' ? 'secondary' : 'warning') }}">{{ $x->type ?? 'NotAutomation' }}</span>
												</p>
												@if($x->id>2)
													@can('Type-edit')
														<a class="modal-effect btn btn-sm btn-info" data-effect="effect-flip-vertical"
														   data-id="{{ $x->id }}" data-name="{{ $x->name }}" data-type="{{ $x->type }}" data-toggle="modal"
														   href="#modal2" title="{{trans('main_trans.Edit')}}"><i class="las la-pen"></i></a>
													@endcan
													@can('Type-delete')
														<a class="modal-effect btn btn-sm btn-danger" data-effect="effect-flip-vertical"
														   data-id="{{ $x->id }}" data-name="{{ $x->name }}" data-toggle="modal"
														   href="#modal3" title="{{trans('main_trans.Delete')}}"><i class="las la-trash"></i></a>
													@endcan
												@else
													@can('Type-edit')
{{--                                                        <p class="mb-1" style="color: red">{{trans('main_trans.Modifying_this_type_is_not_allowed')}}</p>--}}
														<a class="modal-effect btn btn-sm btn-info" data-effect="effect-flip-vertical"
														   data-id="{{ $x->id }}" data-name="{{ $x->name }}" data-type="{{ $x->type }}" data-toggle="modal"
														   href="#modal2" title="{{trans('main_trans.Edit')}}"><i class="las la-pen"></i></a>
													@endcan
												@endif
												</div>
										</div>
									</div>

									@endforeach

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
									<h6 class="modal-title">{{ trans('main_trans.Add_type') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
								</div>
								<form method="POST" action="{{ route('question-type.store') }}" autocomplete="off">
									@csrf
									<div class="modal-body">
										<div class="row mb-3">
											<label for="name" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
											<div class="col-md-8">
												<input id="name" class="form-control" name="name">
											</div>
										</div>
										<div class="row mb-3">
											<label for="type" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Type') }}</label>
											<div class="col-md-8">
												<select name="type" id="type" class="form-control" required>
													<option value="">{{ trans('main_trans.Select') }}</option>
													<option value="Automation">Automation</option>
                                                    <option value="TrueOrFalse">True Or False</option>
                                                    <option value="NotAutomation">Not Automation</option>
												</select>
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
								<form method="POST" action="question-type/update" autocomplete="off">
									{{method_field('patch')}}
									@csrf
									<div class="modal-body">
										<div class="row mb-3">
											<label for="name" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
											<input type="hidden" name="id" id="id" value="">
											<div class="col-md-8">
												<input id="name" class="form-control" name="name">
											</div>
										</div>
										<div class="row mb-3">
											<label for="type-edit" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Type') }}</label>
											<div class="col-md-8">
												<select name="type" id="type-edit" class="form-control" required>
													<option value="">{{ trans('main_trans.Select') }}</option>
													<option value="Automation">Automation</option>
													<option value="TrueOrFalse">True Or False</option>
													<option value="NotAutomation">Not Automation</option>
												</select>
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
								<form action="question-type/destroy" method="post">
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
		var type = button.data('type')
		var modal = $(this)
		modal.find('.modal-body #id').val(id);
		modal.find('.modal-body #name').val(name);
		modal.find('.modal-body #type-edit').val(type);
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
