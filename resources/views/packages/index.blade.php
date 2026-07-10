@php
    $formatPackageSubjects = function ($package) {
        return $package->codePackageSubjects
            ->groupBy('subject_id')
            ->map(function ($items) {
                return [
                    'subject_name' => $items->first()->subject?->name,
                    'units' => $items->map(fn ($item) => $item->unit?->name)->filter()->values()->all(),
                ];
            })
            ->values();
    };
@endphp

@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Code_packages') }}
@endsection
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
    <style>
        .package-item-row { border: 1px solid #e8ebf1; border-radius: 6px; padding: 12px; margin-bottom: 10px; background: #fafbfc; }
        .package-subjects-tree { text-align: left; display: inline-block; }
        .package-subjects-tree ul { list-style: none; padding-left: 18px; margin-bottom: 0; }
        .package-subjects-tree > strong { display: block; margin-bottom: 6px; }
    </style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Code_packages') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Code_packages_list') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')

    @include('components.flash-messages')

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
                        <table class="table table-hover" id="example1" data-page-length='50' style="text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Subjects') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_codes') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Expires_at') }}</th>
                                <th class="wd-20p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($packages as $package)
                                @php
                                    $groupedSubjects = $formatPackageSubjects($package);
                                    $hasCourseUnits = $package->codePackageSubjects->contains(fn ($item) => !empty($item->unit_id));
                                    $hasSubjectsOnly = $package->codePackageSubjects->contains(fn ($item) => empty($item->unit_id));
                                    $courseItems = $package->codePackageSubjects
                                        ->filter(fn ($item) => !empty($item->unit_id))
                                        ->map(fn ($item) => ['subject_id' => $item->subject_id, 'unit_id' => $item->unit_id])
                                        ->values();
                                    $subjectIdsOnly = $package->codePackageSubjects
                                        ->filter(fn ($item) => empty($item->unit_id))
                                        ->pluck('subject_id')
                                        ->unique()
                                        ->values();
                                @endphp
                                <tr>
                                    <td>{{ $package->id }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>
                                        @forelse($groupedSubjects as $group)
                                            <div class="package-subjects-tree mb-2">
                                                <strong>{{ $group['subject_name'] }}</strong>
                                                <ul>
                                                    @foreach($group['units'] as $unitName)
                                                        <li>├─ {{ $unitName }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>
                                    <td>{{ $package->codes_count }}</td>
                                    <td>{{ $package->expires_at }}</td>
                                    <td>
                                        @can('Code-show')
                                            <a class="btn btn-success" href="{{ route('code-package.show', $package->id) }}" title="{{ trans('main_trans.Codes') }}">
                                                <i class="fas fa-file"></i> {{ trans('main_trans.Codes') }}
                                            </a>
                                        @endcan
                                        @can('Code-show')
                                            @if(config('features.code_export_pdf'))
                                                <a class="btn btn-info" href="{{ route('code-package.export-pdf', $package->id) }}" title="{{ trans('main_trans.Export_PDF') }}">
                                                    <i class="fas fa-file-pdf"></i> {{ trans('main_trans.Export_PDF') }}
                                                </a>
                                            @else
                                                <a class="btn btn-outline-warning" style="border: 2px solid #f0ad4e;" href="#" onclick="showProModal(event)" title="{{ trans('main_trans.Export_PDF') }}">
                                                    <i class="fas fa-crown text-warning mr-1"></i><i class="fas fa-file-pdf"></i> {{ trans('main_trans.Export_PDF') }}
                                                </a>
                                            @endif
                                        @endcan
                                        @can('Code-show')
                                            @if(config('features.code_export_excel'))
                                                <a class="btn btn-success" href="{{ route('code-package.export-excel', $package->id) }}" title="{{ trans('main_trans.Export_Excel') }}">
                                                    <i class="fas fa-file-excel"></i> {{ trans('main_trans.Export_Excel') }}
                                                </a>
                                            @else
                                                <a class="btn btn-outline-warning" style="border: 2px solid #f0ad4e;" href="#" onclick="showProModal(event)" title="{{ trans('main_trans.Export_Excel') }}">
                                                    <i class="fas fa-crown text-warning mr-1"></i><i class="fas fa-file-excel"></i> {{ trans('main_trans.Export_Excel') }}
                                                </a>
                                            @endif
                                        @endcan
                                        @can('Code-edit')
                                            <a class="btn btn-warning edit-package-btn"
                                               data-toggle="modal"
                                               data-target="#editPackageModal"
                                               data-package-id="{{ $package->id }}"
                                               data-package-name="{{ $package->name }}"
                                               data-package-expires="{{ $package->expires_at }}"
                                               data-include-with-course="{{ $hasCourseUnits ? '1' : '0' }}"
                                               data-include-without-course="{{ $hasSubjectsOnly ? '1' : '0' }}"
                                               data-course-items="{{ $courseItems->toJson() }}"
                                               data-subject-ids="{{ $subjectIdsOnly->toJson() }}"
                                               title="{{ trans('main_trans.Edit_package') }}">
                                                <i class="fas fa-edit"></i> {{ trans('main_trans.Edit_package') }}
                                            </a>
                                        @endcan
                                        @can('Code-delete')
                                            <a class="modal-effect btn btn-danger" data-effect="effect-flip-vertical"
                                               data-id="{{ $package->id }}" data-name="{{ $package->name }}" data-toggle="modal"
                                               href="#modal3" title="{{ trans('main_trans.Delete') }}"><i class="fas fa-trash"></i></a>
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

        <!-- Add Package Modal -->
        <div class="modal" id="modal1">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Add_code_package') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form method="POST" action="{{ route('code-package.store') }}" autocomplete="off" id="createPackageForm">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="name" class="col-md-3 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
                                <div class="col-md-9">
                                    <input id="name" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="codes_count" class="col-md-3 col-form-label text-md-end">{{ trans('main_trans.Codes_count') }}</label>
                                <div class="col-md-9">
                                    <input id="codes_count" class="form-control" name="codes_count" type="number" min="1" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="expires_at" class="col-md-3 col-form-label text-md-end">{{ trans('main_trans.Expires_at') }}</label>
                                <div class="col-md-9">
                                    <input id="expires_at" class="form-control" name="expires_at" type="date" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label text-md-end">{{ trans('main_trans.Package_content_type') }}</label>
                                <div class="col-md-9">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" id="create_include_with_course" name="include_with_course" value="1" class="custom-control-input include-with-course-checkbox" checked>
                                        <label class="custom-control-label" for="create_include_with_course">{{ trans('main_trans.With_course_subject') }}</label>
                                    </div>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" id="create_include_without_course" name="include_without_course" value="1" class="custom-control-input include-without-course-checkbox">
                                        <label class="custom-control-label" for="create_include_without_course">{{ trans('main_trans.Without_course_subject') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="with-course-section">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <label class="mb-0 font-weight-bold">{{ trans('main_trans.Subject_unit_pairs') }}</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary add-package-item-btn">
                                        <i class="fas fa-plus"></i> {{ trans('main_trans.Add_row') }}
                                    </button>
                                </div>
                                <div class="package-items-container"></div>
                            </div>
                            <div class="without-course-section" style="display: none;">
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label text-md-end">{{ trans('main_trans.Subjects') }}</label>
                                    <div class="col-md-9">
                                        <select name="subject_ids[]" class="form-control package-subjects-multi" multiple size="8" disabled>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted d-block mt-1">{{ trans('main_trans.Hold_Ctrl_to_select_multiple') }}</small>
                                    </div>
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

        <!-- Edit Package Modal -->
        <div class="modal" id="editPackageModal">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Edit_package') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
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
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="mb-2 d-block font-weight-bold">{{ trans('main_trans.Package_content_type') }}</label>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" id="edit_include_with_course" name="include_with_course" value="1" class="custom-control-input include-with-course-checkbox" checked>
                                        <label class="custom-control-label" for="edit_include_with_course">{{ trans('main_trans.With_course_subject') }}</label>
                                    </div>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" id="edit_include_without_course" name="include_without_course" value="1" class="custom-control-input include-without-course-checkbox">
                                        <label class="custom-control-label" for="edit_include_without_course">{{ trans('main_trans.Without_course_subject') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="with-course-section">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <label class="mb-0 font-weight-bold">{{ trans('main_trans.Subject_unit_pairs') }}</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary add-package-item-btn">
                                        <i class="fas fa-plus"></i> {{ trans('main_trans.Add_row') }}
                                    </button>
                                </div>
                                <div class="package-items-container"></div>
                            </div>
                            <div class="without-course-section" style="display: none;">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ trans('main_trans.Subjects') }}</label>
                                    <select name="subject_ids[]" class="form-control package-subjects-multi" multiple size="8" disabled>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1">{{ trans('main_trans.Hold_Ctrl_to_select_multiple') }}</small>
                                </div>
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

        <!-- Delete Package Modal -->
        <div class="modal" id="modal3">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Code_package_delete') }}</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
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
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>

    <script>
        const subjects = [
            @foreach($subjects as $subject)
                { id: {{ $subject->id }}, name: @json($subject->name) },
            @endforeach
        ];
        const subjectVideos = [
            @foreach($subjectVideos as $subjectVideo)
                { id: {{ $subjectVideo->id }}, name: @json($subjectVideo->name) },
            @endforeach
        ];
        const teachers = [
            @foreach($teachers as $teacher)
                {
                    id: {{ $teacher->id }},
                    name: @json($teacher->name),
                    subject_video_ids: @json($teacher->subjectVideos->pluck('id')->values())
                },
            @endforeach
        ];
        const units = [
            @foreach($units as $unit)
                { id: {{ $unit->id }}, name: @json($unit->name), teacher_id: {{ $unit->teacher_id }} },
            @endforeach
        ];

        function getTeacherIdByUnitId(unitId) {
            const unit = units.find(function(u) { return String(u.id) === String(unitId); });
            return unit ? unit.teacher_id : '';
        }

        function resolveSubjectVideoIdByTeacherId(teacherId) {
            const teacher = teachers.find(function(t) { return String(t.id) === String(teacherId); });
            if (!teacher || !teacher.subject_video_ids.length) {
                return '';
            }
            return teacher.subject_video_ids[0];
        }

        function populateSubjectVideoSelect(subjectVideoSelect, selectedSubjectVideoId = '') {
            subjectVideoSelect.html(`<option value="">{{ trans('main_trans.Select_course_subject') }}</option>`);
            subjectVideos.forEach(function(subjectVideo) {
                subjectVideoSelect.append(`<option value="${subjectVideo.id}">${subjectVideo.name}</option>`);
            });
            if (selectedSubjectVideoId) {
                subjectVideoSelect.val(selectedSubjectVideoId);
            }
        }

        function populateTeacherSelect(teacherSelect, subjectVideoId, selectedTeacherId = '') {
            teacherSelect.html(`<option value="">{{ trans('main_trans.Select_teacher') }}</option>`);
            if (!subjectVideoId) {
                teacherSelect.prop('disabled', true);
                return;
            }

            teacherSelect.prop('disabled', false);
            teachers
                .filter(function(teacher) {
                    return teacher.subject_video_ids.map(String).includes(String(subjectVideoId));
                })
                .forEach(function(teacher) {
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

        function applyPackageSections(modal) {
            const withCourse = modal.find('.include-with-course-checkbox').is(':checked');
            const withoutCourse = modal.find('.include-without-course-checkbox').is(':checked');

            modal.find('.with-course-section').toggle(withCourse);
            modal.find('.without-course-section').toggle(withoutCourse);

            modal.find('.with-course-section').find('select, button').prop('disabled', !withCourse);
            modal.find('.without-course-section').find('select').prop('disabled', !withoutCourse);
        }

        function buildPackageItemRow(container, index, selectedSubjectId = '', selectedUnitId = '', selectedTeacherId = '', selectedSubjectVideoId = '') {
            if (!selectedTeacherId && selectedUnitId) {
                selectedTeacherId = getTeacherIdByUnitId(selectedUnitId);
            }
            if (!selectedSubjectVideoId && selectedTeacherId) {
                selectedSubjectVideoId = resolveSubjectVideoIdByTeacherId(selectedTeacherId);
            }

            const row = $(`
                <div class="package-item-row" data-index="${index}">
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <label class="mb-1">{{ trans('main_trans.Subject') }}</label>
                            <select class="form-control package-subject-select" name="package_items[${index}][subject_id]" >
                                <option value="">{{ trans('main_trans.Select_subject') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="mb-1">{{ trans('main_trans.Course_subject') }}</label>
                            <select class="form-control package-subject-video-select">
                                <option value="">{{ trans('main_trans.Select_course_subject') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-2">
                            <label class="mb-1">{{ trans('main_trans.Teacher') }}</label>
                            <select class="form-control package-teacher-select" disabled>
                                <option value="">{{ trans('main_trans.Select_teacher') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-2">
                            <label class="mb-1">{{ trans('main_trans.Unit') }}</label>
                            <select class="form-control package-unit-select" name="package_items[${index}][unit_id]" required disabled>
                                <option value="">{{ trans('main_trans.Select_unit') }}</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-lg-2 d-flex align-items-end">
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
            populateSubjectVideoSelect(row.find('.package-subject-video-select'), selectedSubjectVideoId);
            populateTeacherSelect(row.find('.package-teacher-select'), selectedSubjectVideoId, selectedTeacherId);
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

        function resetWithCourseSection(modal, packageItems = []) {
            const container = modal.find('.package-items-container');
            container.empty();

            if (packageItems.length === 0) {
                buildPackageItemRow(container, 0);
                return;
            }

            packageItems.forEach(function(item, index) {
                buildPackageItemRow(container, index, item.subject_id, item.unit_id);
            });
        }

        $(document).on('change', '.include-with-course-checkbox, .include-without-course-checkbox', function() {
            applyPackageSections($(this).closest('.modal'));
        });

        $(document).on('click', '.add-package-item-btn', function() {
            const modal = $(this).closest('.modal');
            const container = modal.find('.package-items-container');
            buildPackageItemRow(container, container.find('.package-item-row').length);
        });

        $(document).on('change', '.package-subject-video-select', function() {
            const row = $(this).closest('.package-item-row');
            const subjectVideoId = $(this).val();
            populateTeacherSelect(row.find('.package-teacher-select'), subjectVideoId);
            populateUnitSelect(row.find('.package-unit-select'), '');
        });

        $(document).on('change', '.package-teacher-select', function() {
            const row = $(this).closest('.package-item-row');
            populateUnitSelect(row.find('.package-unit-select'), $(this).val());
        });

        $(document).on('click', '.remove-package-item', function() {
            const container = $(this).closest('.package-items-container');
            $(this).closest('.package-item-row').remove();
            reindexPackageItems(container);
        });

        $('#modal1').on('show.bs.modal', function() {
            const modal = $(this);
            modal.find('.include-with-course-checkbox').prop('checked', true);
            modal.find('.include-without-course-checkbox').prop('checked', false);
            modal.find('.package-subjects-multi').val([]);
            resetWithCourseSection(modal);
            applyPackageSections(modal);
        });

        $('.edit-package-btn').on('click', function() {
            const packageId = $(this).data('package-id');
            const includeWithCourse = String($(this).data('include-with-course')) === '1';
            const includeWithoutCourse = String($(this).data('include-without-course')) === '1';
            const courseItems = $(this).data('course-items') || [];
            const subjectIds = $(this).data('subject-ids') || [];
            const modal = $('#editPackageModal');

            $('#editPackageForm').attr('action', '{{ route('code-package.index') }}/' + packageId);
            $('#edit_name').val($(this).data('package-name'));
            $('#edit_expires_at').val($(this).data('package-expires'));
            modal.find('.include-with-course-checkbox').prop('checked', includeWithCourse);
            modal.find('.include-without-course-checkbox').prop('checked', includeWithoutCourse);
            modal.find('.package-subjects-multi').val(subjectIds.map(String));
            resetWithCourseSection(modal, courseItems);
            applyPackageSections(modal);
        });

        $('#createPackageForm, #editPackageForm').on('submit', function(e) {
            const modal = $(this).closest('.modal');
            const withCourse = modal.find('.include-with-course-checkbox').is(':checked');
            const withoutCourse = modal.find('.include-without-course-checkbox').is(':checked');

            if (!withCourse && !withoutCourse) {
                e.preventDefault();
                alert('{{ trans('main_trans.At_least_one_content_type_required') }}');
                return;
            }

            if (withCourse && modal.find('.package-item-row').length === 0) {
                e.preventDefault();
                alert('{{ trans('main_trans.At_least_one_subject_unit_required') }}');
                return;
            }

            if (withoutCourse) {
                const selectedSubjects = modal.find('.package-subjects-multi').val() || [];
                if (selectedSubjects.length === 0) {
                    e.preventDefault();
                    alert('{{ trans('main_trans.At_least_one_subject_required') }}');
                }
            }
        });

        $('#modal3').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            $(this).find('.modal-body #id').val(button.data('id'));
            $(this).find('.modal-body #name').val(button.data('name'));
        });
    </script>
@endsection
