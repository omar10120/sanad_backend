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
    <style>
        .gap-2 {
            gap: 0.5rem;
        }
        .card-header .d-flex {
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
            .card-header .d-flex > div {
                width: 100%;
            }
        }
    </style>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a class="btn btn-secondary" href="{{ route('code-package.index') }}" title="{{ trans('main_trans.Back') }}">
                                <i class="fas fa-arrow-left"></i> {{ trans('main_trans.Back') }}
                            </a>
                        </div>
                        @can('Code-show')
                            <div class="d-flex gap-2">
                                @if(config('features.code_export_pdf'))
                                <a class="btn btn-primary" 
                                   href="{{ route('code-package.export-pdf', $package->id) }}"
                                   title="{{ trans('main_trans.Export_PDF') }}">
                                    <i class="fas fa-file-pdf"></i> {{ trans('main_trans.Export_all_codes_to_PDF') }}
                                </a>
                                @else
                                <a class="btn btn-outline-warning" style="border: 2px solid #f0ad4e;"
                                   href="#" onclick="showProModal(event)"
                                   title="{{ trans('main_trans.Export_PDF') }}">
                                    <i class="fas fa-crown text-warning mr-1"></i><i class="fas fa-file-pdf"></i> {{ trans('main_trans.Export_all_codes_to_PDF') }}
                                </a>
                                @endif
                                @if(config('features.code_export_excel'))
                                <a class="btn btn-success" 
                                   href="{{ route('code-package.export-excel', $package->id) }}"
                                   title="{{ trans('main_trans.Export_Excel') }}">
                                    <i class="fas fa-file-excel"></i> {{ trans('main_trans.Export_all_codes_to_Excel') }}
                                </a>
                                @else
                                <a class="btn btn-outline-warning" style="border: 2px solid #f0ad4e;"
                                   href="#" onclick="showProModal(event)"
                                   title="{{ trans('main_trans.Export_Excel') }}">
                                    <i class="fas fa-crown text-warning mr-1"></i><i class="fas fa-file-excel"></i> {{ trans('main_trans.Export_all_codes_to_Excel') }}
                                </a>
                                @endif
                                <a class="btn btn-warning edit-package-btn" data-toggle="modal" data-target="#editPackageModal" title="{{ trans('main_trans.Edit_package') }}">
                                    <i class="fas fa-edit"></i> {{ trans('main_trans.Edit_package') }}
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example1" data-page-length='50' style=" text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Code_package') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Subjects') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Code') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Expires_at') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Student') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($package->codes as $code)
                                <tr>
                                    <td>{{ $code->id }}</td>
                                    <td>{{ $package->name }}</td>
{{--                                    <td>{{ $package->subjects->pluck('name')->toArray() }}</td>--}}
                                    <td>
                                        @if(count($packageSubjectsGrouped['study_subjects']) || count($packageSubjectsGrouped['course_subjects']))
                                            @if(count($packageSubjectsGrouped['study_subjects']))
                                                <div class="text-left mb-2">
                                                    <small class="text-muted d-block mb-1">{{ trans('main_trans.Without_course_subject') }}</small>
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($packageSubjectsGrouped['study_subjects'] as $subject)
                                                            <li>{{ $subject['subject_name'] }} ({{ trans('main_trans.Full_subject') }})</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            @if(count($packageSubjectsGrouped['course_subjects']))
                                                <div class="text-left mb-2">
                                                    <small class="text-muted d-block mb-1">{{ trans('main_trans.With_course_subject') }}</small>
                                                    @foreach($packageSubjectsGrouped['course_subjects'] as $group)
                                                        <strong>{{ $group['subject_name'] }}</strong>
                                                        <ul class="mb-0 pl-3">
                                                            @foreach($group['units'] as $unit)
                                                                <li>├─ {{ $unit['name'] }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $code->code }}</td>
                                    <td>{{ $package->expires_at }}</td>
                                    <td>{{ $code->student
                                            ? $code->student->id . ' - ' . $code->student->first_name . ' ' . $code->student->father_name . ' ' . $code->student->last_name
                                            : trans('main_trans.Not_used') }}
                                    </td>
                                    <td>
                                            <a id="link-qr-code-{{$code->id}}" class="btn btn-info" href="#" download="QR-Code-{{$code->id}}.jpg"
                                               title="{{ trans('main_trans.QR_Code') }}"><i class="fas fa-qrcode m-1"></i> {{ trans('main_trans.QR_Code') }}</a>
                                            <script>
                                                const apiUrl{{$code->id}} = 'https://api.qrserver.com/v1/create-qr-code/' +
                                                    '?size=1500x1500' +
                                                    '&data={{$code->code}}';
                                                    '&margin=20';

                                                fetch(apiUrl{{$code->id}})
                                                    .then(response => {
                                                        if (!response.ok) {
                                                            throw new Error('Network response was not ok');
                                                        }
                                                        return response.blob();
                                                    })
                                                    .then(blob => {
                                                        const imageUrl = URL.createObjectURL(blob);
                                                        {{--// document.getElementById('qr-code-{{$code->id}}').src = imageUrl;--}}
                                                        document.getElementById('link-qr-code-{{$code->id}}').href = imageUrl;
                                                    })
                                                    .catch(error => {
                                                        console.error('There was a problem with the fetch operation:', error);
                                                    });
                                            </script>

                                        @can('Code-delete')
                                            <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                               data-id="{{ $code->id }}" data-name="{{ $code->code }}" data-toggle="modal"
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
        <!--/div-->

        <!-- Modal effects -->
        <div class="modal" id="modal3">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Code_delete') }}</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('code.destroy', 'test') }}" method="post">
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
                    <form action="{{ route('code-package.update', $package->id) }}" method="post">
                        {{ method_field('PUT') }}
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ trans('main_trans.Package_name') }}</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $package->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expires_at">{{ trans('main_trans.Expires_at') }}</label>
                                        <input type="date" class="form-control" id="expires_at" name="expires_at" value="{{ $package->expires_at }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="mb-0 font-weight-bold">{{ trans('main_trans.Subject_unit_pairs') }}</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-edit-package-item">
                                    <i class="fas fa-plus"></i> {{ trans('main_trans.Add_row') }}
                                </button>
                            </div>
                            <div id="edit-package-items-container"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ trans('main_trans.Update') }}</button>
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
        const subjects = [
            @foreach($subjects as $subject)
                { id: {{ $subject->id }}, name: @json($subject->name) },
            @endforeach
        ];
        const teachers = [
            @foreach($teachers as $teacher)
                { id: {{ $teacher->id }}, name: @json($teacher->name) },
            @endforeach
        ];
        const units = [
            @foreach($units as $unit)
                { id: {{ $unit->id }}, name: @json($unit->name), teacher_id: {{ $unit->teacher_id }} },
            @endforeach
        ];
        const packageItems = @json($package->codePackageSubjects->map(function ($item) {
            return ['subject_id' => $item->subject_id, 'unit_id' => $item->unit_id];
        })->values());

        function getTeacherIdByUnitId(unitId) {
            const unit = units.find(function(u) { return String(u.id) === String(unitId); });
            return unit ? unit.teacher_id : '';
        }

        function populateTeacherSelect(teacherSelect, selectedTeacherId = '') {
            teacherSelect.html(`<option value="">{{ trans('main_trans.Select_teacher') }}</option>`);
            teachers.forEach(function(teacher) {
                teacherSelect.append(`<option value="${teacher.id}">${teacher.name}</option>`);
            });
            if (selectedTeacherId) {
                teacherSelect.val(selectedTeacherId);
            }
        }

        function populateUnitSelect(unitSelect, teacherId, selectedUnitId = '') {
            unitSelect.html(`<option value="">{{ trans('main_trans.Select_unit') }}</option>`);
            if (!teacherId) {
                unitSelect.prop('disabled', true);
                return;
            }

            unitSelect.prop('disabled', false);
            units
                .filter(function(unit) { return String(unit.teacher_id) === String(teacherId); })
                .forEach(function(unit) {
                    unitSelect.append(`<option value="${unit.id}">${unit.name}</option>`);
                });

            if (selectedUnitId) {
                unitSelect.val(selectedUnitId);
            }
        }

        function buildPackageItemRow(container, index, selectedSubjectId = '', selectedUnitId = '', selectedTeacherId = '') {
            if (!selectedTeacherId && selectedUnitId) {
                selectedTeacherId = getTeacherIdByUnitId(selectedUnitId);
            }

            const row = $(`
                <div class="package-item-row" data-index="${index}" style="border:1px solid #e8ebf1;border-radius:6px;padding:12px;margin-bottom:10px;">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="mb-1">{{ trans('main_trans.Subject') }}</label>
                            <select class="form-control package-subject-select" name="package_items[${index}][subject_id]" required>
                                <option value="">{{ trans('main_trans.Select_subject') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="mb-1">{{ trans('main_trans.Teacher') }}</label>
                            <select class="form-control package-teacher-select">
                                <option value="">{{ trans('main_trans.Select_teacher') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="mb-1">{{ trans('main_trans.Unit') }}</label>
                            <select class="form-control package-unit-select" name="package_items[${index}][unit_id]" required disabled>
                                <option value="">{{ trans('main_trans.Select_unit') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-block remove-package-item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);

            subjects.forEach(function(subject) {
                row.find('.package-subject-select').append(`<option value="${subject.id}">${subject.name}</option>`);
            });

            container.append(row);
            populateTeacherSelect(row.find('.package-teacher-select'), selectedTeacherId);
            populateUnitSelect(row.find('.package-unit-select'), selectedTeacherId, selectedUnitId);

            if (selectedSubjectId) {
                row.find('.package-subject-select').val(selectedSubjectId);
            }
        }

        function reindexPackageItems(container) {
            container.find('.package-item-row').each(function(index) {
                $(this).attr('data-index', index);
                $(this).find('.package-subject-select').attr('name', `package_items[${index}][subject_id]`);
                $(this).find('.package-unit-select').attr('name', `package_items[${index}][unit_id]`);
            });
        }

        $(document).on('change', '.package-teacher-select', function() {
            const row = $(this).closest('.package-item-row');
            populateUnitSelect(row.find('.package-unit-select'), $(this).val());
        });

        $('#add-edit-package-item').on('click', function() {
            const container = $('#edit-package-items-container');
            buildPackageItemRow(container, container.find('.package-item-row').length);
        });

        $(document).on('click', '.remove-package-item', function() {
            const container = $('#edit-package-items-container');
            $(this).closest('.package-item-row').remove();
            reindexPackageItems(container);
        });

        $('#editPackageModal').on('show.bs.modal', function() {
            const container = $('#edit-package-items-container');
            container.empty();

            if (packageItems.length === 0) {
                buildPackageItemRow(container, 0);
            } else {
                packageItems.forEach(function(item, index) {
                    buildPackageItemRow(container, index, item.subject_id, item.unit_id);
                });
            }
        });

        $('#editPackageModal form').on('submit', function(e) {
            if ($(this).find('.package-item-row').length === 0) {
                e.preventDefault();
                alert('{{ trans('main_trans.At_least_one_subject_unit_required') }}');
            }
        });

        $('#modal3').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            $(this).find('.modal-body #id').val(button.data('id'));
            $(this).find('.modal-body #name').val(button.data('name'));
        });
    </script>

@endsection
