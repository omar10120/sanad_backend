@extends('layouts.master')
@section('title')
{{ trans('main_trans.App_updates') }}
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
							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Study') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.App_updates') }}</span>
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
                                @can('Role-add')
                                    <div class="d-flex justify-content-between">
                                        <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical" data-toggle="modal" href="#modal1">{{ trans('main_trans.Add_app_update') }}</a>
                                    </div>
                                @endcan
                            </div>
                            <div class="card-body">
                                <div class="table-responsive hoverable-table">
                                    <table class="table table-hover" id="example1" data-page-length='50' style=" text-align: center;">
                                        <thead>
                                        <tr>
                                            <th class="wd-5p-f border-bottom-0">#</th>
                                            <th class="wd-5p border-bottom-0">{{ trans('main_trans.Platform') }}</th>
                                            <th class="wd-5p border-bottom-0">{{ trans('main_trans.Version') }}</th>
                                            <th class="wd-5p border-bottom-0">{{ trans('main_trans.Update_type') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ trans('main_trans.Change_log') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($app_updates as $app_update)
                                            <tr>
                                                <td>{{ $app_update->id }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $app_update->platform == 'android' ? 'success' : 'info' }}">
                                                        {{ ucfirst($app_update->platform) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">{{ $app_update->version }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $app_update->is_force_update ? 'danger' : 'warning' }}">
                                                        {{ $app_update->is_force_update ? trans('main_trans.Force') : trans('main_trans.Not_force') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($app_update->changelog)
                                                        <span title="{{ $app_update->changelog }}">
                                                            {{ Str::limit($app_update->changelog, 50) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">{{ trans('main_trans.No_changelog') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($app_update->update_url)
                                                        <a class="btn btn-success btn-sm" href="{{$app_update->update_url}}" target="_blank" title="{{trans('main_trans.Url_update')}}">
                                                            <i class="fas fa-link"></i>
                                                        </a>
                                                    @endif

                                                    @can('Role-edit')
                                                        <a class="modal-effect btn btn-info btn-sm" data-effect="effect-flip-vertical"
                                                           data-id="{{ $app_update->id }}"
                                                           data-platform="{{ $app_update->platform }}"
                                                           data-version="{{ $app_update->version }}"
                                                           data-is_force_update="{{ $app_update->is_force_update }}"
                                                           data-changelog="{{ $app_update->changelog }}"
                                                           data-update_url="{{ $app_update->update_url }}"
                                                           data-toggle="modal"
                                                           href="#modal2" title="{{trans('main_trans.Edit')}}">
                                                            <i class="fas fa-pen"></i>
                                                        </a>
                                                    @endcan

                                                    @can('Role-delete')
                                                        <a class="modal-effect btn btn-danger btn-sm" data-effect="effect-flip-vertical"
                                                           data-id="{{ $app_update->id }}"
                                                           data-name="{{ $app_update->platform . ' - ' . $app_update->version }}"
                                                           data-toggle="modal"
                                                           href="#modal3" title="{{trans('main_trans.Delete')}}">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <div class="alert alert-info">
                                                        {{ trans('main_trans.No_app_updates_found') }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/div-->

                    <!-- Add modal -->
                    <div class="modal" id="modal1">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">{{ trans('main_trans.Add_app_update') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form method="POST" action="{{ route('app-update.store') }}" autocomplete="off">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <label for="platform" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Platform') }} <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                                <select name="platform" id="platform" class="form-control @error('platform') is-invalid @enderror" required>
                                                    <option value="" disabled selected>{{ trans('main_trans.Select_platform') }}</option>
                                                    <option value="android" {{ old('platform') == 'android' ? 'selected' : '' }}>Android</option>
                                                    <option value="ios" {{ old('platform') == 'ios' ? 'selected' : '' }}>iOS</option>
                                                </select>
                                                @error('platform')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="version" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Version') }} <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                                <input id="version" class="form-control @error('version') is-invalid @enderror" name="version" value="{{ old('version') }}" placeholder="e.g., 1.0.0">
                                                @error('version')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="is_force_update" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Update_type') }} <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                                <select name="is_force_update" id="is_force_update" class="form-control @error('is_force_update') is-invalid @enderror" required>
                                                    <option value="" disabled selected>{{ trans('main_trans.Select_update_type') }}</option>
                                                    <option value="0" {{ old('is_force_update') == '0' ? 'selected' : '' }}>{{ trans('main_trans.Not_force') }}</option>
                                                    <option value="1" {{ old('is_force_update') == '1' ? 'selected' : '' }}>{{ trans('main_trans.Force') }}</option>
                                                </select>
                                                @error('is_force_update')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="changelog" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Change_log') }}</label>
                                            <div class="col-md-8">
                                                <textarea class="form-control @error('changelog') is-invalid @enderror" name="changelog" id="changelog" cols="30" rows="5" placeholder="{{ trans('main_trans.Enter_changelog') }}">{{ old('changelog') }}</textarea>
                                                @error('changelog')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="update_url" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Url_update') }}</label>
                                            <div class="col-md-8">
                                                <input id="update_url" class="form-control @error('update_url') is-invalid @enderror" name="update_url" value="{{ old('update_url') }}" placeholder="https://example.com/download">
                                                @error('update_url')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Add_app_update') }}</button>
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
                                    <h6 class="modal-title">{{ trans('main_trans.Edit_app_update') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form method="POST" action="" id="edit-form" autocomplete="off">
                                    @method('PUT')
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <label for="edit_platform" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Platform') }} <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                                <select name="platform" id="edit_platform" class="form-control" required>
                                                    <option value="android">Android</option>
                                                    <option value="ios">iOS</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="edit_version" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Version') }} <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                                <input id="edit_version" class="form-control" name="version" placeholder="e.g., 1.0.0">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="edit_is_force_update" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Update_type') }} <span class="text-danger">*</span></label>
                                            <div class="col-md-8">
                                                <select name="is_force_update" id="edit_is_force_update" class="form-control" required>
                                                    <option value="0">{{ trans('main_trans.Not_force') }}</option>
                                                    <option value="1">{{ trans('main_trans.Force') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="edit_changelog" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Change_log') }}</label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" name="changelog" id="edit_changelog" cols="30" rows="5" placeholder="{{ trans('main_trans.Enter_changelog') }}"></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="edit_update_url" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Url_update') }}</label>
                                            <div class="col-md-8">
                                                <input id="edit_update_url" class="form-control" name="update_url" placeholder="https://example.com/download">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Update_app_update') }}</button>
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
                                    <h6 class="modal-title">{{ trans('main_trans.Delete_app_update') }}</h6><button aria-label="Close" class="close" data-dismiss="modal"
                                                                                    type="button"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <form action="" method="post" id="delete-form">
                                    @method('DELETE')
                                    @csrf
                                    <div class="modal-body">
                                        <p>{{ trans('main_trans.Are_you_sure_to_delete') }}</p><br>
                                        <div class="row mb-3 mx-1">
                                            <input class="form-control" name="name" id="delete_name" type="text" readonly>
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
    // Auto-open modal if there are validation errors
    @if($errors->any() && old('_token'))
        $(document).ready(function() {
            $('#modal1').modal('show');
        });
    @endif

    $('#modal2').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var platform = button.data('platform')
        var version = button.data('version')
        var isForceUpdate = button.data('is_force_update')
        var changelog = button.data('changelog')
        var updateUrl = button.data('update_url')
        var modal = $(this)

        // Set the form action
        modal.find('#edit-form').attr('action', '{{ route("app-update.update", "") }}/' + id);

        // Set the form values
        modal.find('#edit_platform').val(platform);
        modal.find('#edit_version').val(version);
        modal.find('#edit_is_force_update').val(isForceUpdate);
        modal.find('#edit_changelog').val(changelog);
        modal.find('#edit_update_url').val(updateUrl);
    })

    $('#modal3').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name = button.data('name')
        var modal = $(this)

        // Set the form action
        modal.find('#delete-form').attr('action', '{{ route("app-update.destroy", "") }}/' + id);

        modal.find('#delete_name').val(name);
    })

    // Clear form when modal is closed
    $('#modal1').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();
    });
</script>

@endsection
