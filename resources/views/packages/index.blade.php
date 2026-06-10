@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Code_packages') }}
@endsection
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Code_packages') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Code_packages_list') }}</span>
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
                <div class="card-header pb-0">
                    @can('Code-add')
                        <div class="d-flex justify-content-between">
                            <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-flip-vertical" data-toggle="modal" href="#modal1">{{ trans('main_trans.Add_code_package') }}</a>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example1" data-page-length='50' style=" text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_codes') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Expires_at') }}</th>
                                <th class="wd-20p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($packages as $package)
                                <tr>
                                    <td>{{ $package->id }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>{{ $package->codes->count() }}</td>
                                    <td>{{ $package->expires_at }}</td>
                                    <td>
                                        @can('Code-show')
                                            <a class="btn btn-success"
                                               href="{{ route('code-package.show', $package->id) }}" title="{{trans('main_trans.Codes')}}"><i class="fas fa-file"></i>{{' ' . trans('main_trans.Codes')}}</a>
                                        @endcan
                                        @can('Code-show')
                                            @if(config('features.code_export_pdf'))
                                            <a class="btn btn-info"
                                               href="{{ route('code-package.export-pdf', $package->id) }}"
                                               title="{{ trans('main_trans.Export_PDF') }}"><i class="fas fa-file-pdf"></i> {{ trans('main_trans.Export_PDF') }}</a>
                                            @else
                                            <a class="btn btn-outline-warning" style="border: 2px solid #f0ad4e;"
                                               href="#" onclick="showProModal(event)"
                                               title="{{ trans('main_trans.Export_PDF') }}"><i class="fas fa-crown text-warning mr-1"></i><i class="fas fa-file-pdf"></i> {{ trans('main_trans.Export_PDF') }}</a>
                                            @endif
                                        @endcan
                                        @can('Code-show')
                                            @if(config('features.code_export_excel'))
                                            <a class="btn btn-success"
                                               href="{{ route('code-package.export-excel', $package->id) }}"
                                               title="{{ trans('main_trans.Export_Excel') }}"><i class="fas fa-file-excel"></i> {{ trans('main_trans.Export_Excel') }}</a>
                                            @else
                                            <a class="btn btn-outline-warning" style="border: 2px solid #f0ad4e;"
                                               href="#" onclick="showProModal(event)"
                                               title="{{ trans('main_trans.Export_Excel') }}"><i class="fas fa-crown text-warning mr-1"></i><i class="fas fa-file-excel"></i> {{ trans('main_trans.Export_Excel') }}</a>
                                            @endif
                                        @endcan
                                        @can('Code-edit')
                                            <a class="btn btn-warning edit-package-btn" 
                                               data-toggle="modal" 
                                               data-target="#editPackageModal"
                                               data-package-id="{{ $package->id }}"
                                               data-package-name="{{ $package->name }}"
                                               data-package-expires="{{ $package->expires_at }}"
                                               data-package-subjects="{{ $package->subjects->pluck('id')->toJson() }}"
                                               title="{{ trans('main_trans.Edit_package') }}">
                                                <i class="fas fa-edit"></i> {{ trans('main_trans.Edit_package') }}
                                            </a>
                                        @endcan
{{--                                        @can('Code-edit')--}}
{{--                                            <a class="modal-effect btn btn-info" data-effect="effect-flip-vertical"--}}
{{--                                               data-id="{{ $package->id }}" data-name="{{ $package->name }}" data-toggle="modal"--}}
{{--                                               href="#modal2" title="{{trans('main_trans.Subject')}}"><i class="fas fa-pen"></i></a>--}}
{{--                                        @endcan--}}
                                        @can('Code-delete')
                                            <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                               data-id="{{ $package->id }}" data-name="{{ $package->name }}" data-toggle="modal"
                                               href="#modal3" title="{{trans('main_trans.Delete')}}"><i class="fas fa-trash"></i></a>
                                        @endcan
{{--                                            <a id="link-qr-code-{{$package->id}}" class="btn btn-success" href="#" download="QR-Code-{{$package->id}}.jpg"--}}
{{--                                               title="QR-Code"><i class="fas fa-qrcode m-1"></i> {{" QR-Code"}}</a>--}}
{{--                                            <script>--}}
{{--                                                const apiUrl{{$package->id}} = 'https://api.qrserver.com/v1/create-qr-code/' +--}}
{{--                                                    '?size=1500x1500' +--}}
{{--                                                    '&data={{$package->codes->first()->code}}';--}}
{{--                                                    '&margin=20';--}}

{{--                                                fetch(apiUrl{{$package->id}})--}}
{{--                                                    .then(response => {--}}
{{--                                                        if (!response.ok) {--}}
{{--                                                            throw new Error('Network response was not ok');--}}
{{--                                                        }--}}
{{--                                                        return response.blob();--}}
{{--                                                    })--}}
{{--                                                    .then(blob => {--}}
{{--                                                        const imageUrl = URL.createObjectURL(blob);--}}
{{--                                                        --}}{{--// document.getElementById('qr-code-{{$package->id}}').src = imageUrl;--}}
{{--                                                        document.getElementById('link-qr-code-{{$package->id}}').href = imageUrl;--}}
{{--                                                    })--}}
{{--                                                    .catch(error => {--}}
{{--                                                        console.error('There was a problem with the fetch operation:', error);--}}
{{--                                                    });--}}
{{--                                            </script>--}}


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

        <!-- Add modal -->
        <div class="modal" id="modal1">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Add_code_package') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form method="POST" action="{{ route('code-package.store') }}" autocomplete="off">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
                                <div class="col-md-8">
                                    <input id="name" class="form-control" name="name">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="subject_ids" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Subjects') }}</label>
                                <div class="col-md-8">
                                    <select name="subject_ids[]" id="subject_ids" class="form-control" required multiple>
{{--                                        <option value="" disabled>{{ trans('main_trans.Select_subject') }}</option>--}}
                                        @foreach ($subjects as $subject)
                                            <option value="{{$subject->id}}">{{$subject->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="codes_count" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Codes_count') }}</label>
                                <div class="col-md-8">
                                    <input id="codes_count" class="form-control" name="codes_count">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="expires_at" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Expires_at') }}</label>
                                <div class="col-md-8">
                                    <input id="expires_at" class="form-control" name="expires_at" type="date">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn ripple btn-primary" type="submit">{{ trans('main_trans.Add_code_package') }}</button>
                            <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ trans('main_trans.Close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add modal -->

        <!-- Edit Package Modal -->
        <div class="modal" id="editPackageModal">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Edit_package') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editPackageForm" method="post">
                        {{ method_field('PUT') }}
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_name">{{ trans('main_trans.Package_name') }}</label>
                                        <input type="text" class="form-control" id="edit_name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_expires_at">{{ trans('main_trans.Expires_at') }}</label>
                                        <input type="date" class="form-control" id="edit_expires_at" name="expires_at" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="edit_subject_ids">{{ trans('main_trans.Subjects') }}</label>
                                <select class="form-control" id="edit_subject_ids" name="subject_ids[]" multiple required style="height: 120px;">
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">{{ trans('main_trans.Hold_Ctrl_to_select_multiple') }}</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ trans('main_trans.Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal effects -->
        <div class="modal" id="modal3">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Code_package_delete') }}</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('code-package.destroy', 'test') }}" method="post">
                        {{ method_field('delete') }}
                        @csrf
                        <div class="modal-body">
                            <p>{{ trans('main_trans.Are_you_sure_to_delete') }}</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="name" id="name" type="text" readonly>
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

    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection

@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <!-- Internal Modal js-->
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>

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

        // Edit Package Modal functionality
        $('.edit-package-btn').on('click', function() {
            var packageId = $(this).data('package-id');
            var packageName = $(this).data('package-name');
            var packageExpires = $(this).data('package-expires');
            var packageSubjects = $(this).data('package-subjects');
            
            // Set form action
            $('#editPackageForm').attr('action', '{{ route("code-package.index") }}/' + packageId);
            
            // Fill form fields
            $('#edit_name').val(packageName);
            $('#edit_expires_at').val(packageExpires);
            
            // Clear previous selections
            $('#edit_subject_ids option').prop('selected', false);
            
            // Select the package's subjects
            if (packageSubjects && packageSubjects.length > 0) {
                packageSubjects.forEach(function(subjectId) {
                    $('#edit_subject_ids option[value="' + subjectId + '"]').prop('selected', true);
                });
            }
            
            console.log('Edit modal opened for package:', packageId);
        });

        // Validation for edit form
        $('#editPackageForm').on('submit', function(e) {
            var selectedSubjects = $('#edit_subject_ids option:selected').length;
            if (selectedSubjects === 0) {
                e.preventDefault();
                alert('{{ trans("main_trans.Please_select_at_least_one_subject") }}');
                return false;
            }
        });

    </script>

@endsection
