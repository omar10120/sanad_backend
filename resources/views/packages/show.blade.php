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
    <link href="{{ URL::asset('assets/plugins/notif/css/notifIt.css') }}" rel="stylesheet" />
    <style>
        .gap-2 {
            gap: 0.5rem;
        }
        .card-header .d-flex {
            flex-wrap: wrap;
        }
        .package-item-row { border: 1px solid #e8ebf1; border-radius: 6px; padding: 12px; margin-bottom: 10px; background: #fafbfc; }
        .package-unit-select[multiple] { min-height: 90px; }
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
                                <a class="btn btn-warning edit-package-btn"
                                   data-toggle="modal"
                                   data-target="#editPackageModal"
                                   data-package-id="{{ $package->id }}"
                                   data-package-name="{{ $package->name }}"
                                   data-package-expires="{{ $package->expires_at }}"
                                   data-include-with-course="{{ $package->codePackageSubjects->contains(fn ($item) => !empty($item->unit_id)) ? '1' : '0' }}"
                                   data-include-without-course="{{ $package->codePackageSubjects->contains(fn ($item) => empty($item->unit_id)) ? '1' : '0' }}"
                                   data-course-items="{{ $package->codePackageSubjects->filter(fn ($item) => !empty($item->unit_id))->map(fn ($item) => ['subject_id' => $item->subject_id, 'unit_id' => $item->unit_id])->values()->toJson() }}"
                                   data-subject-ids="{{ $package->codePackageSubjects->filter(fn ($item) => empty($item->unit_id))->pluck('subject_id')->unique()->values()->toJson() }}"
                                   title="{{ trans('main_trans.Edit_package') }}">
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
                                                        @if($group['subject_name'])
                                                            <strong>{{ $group['subject_name'] }}</strong>
                                                        @endif
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
                    <form id="editPackageForm" action="{{ route('code-package.update', $package->id) }}" method="post">
                        {{ method_field('PUT') }}
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_name">{{ trans('main_trans.Package_name') }}</label>
                                        <input type="text" class="form-control" id="edit_name" name="name" value="{{ $package->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_expires_at">{{ trans('main_trans.Expires_at') }}</label>
                                        <input type="date" class="form-control" id="edit_expires_at" name="expires_at" value="{{ $package->expires_at }}" required>
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

        function populateUnitSelect(unitSelect, teacherId, selectedUnitIds = []) {
            unitSelect.empty();
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

            const ids = Array.isArray(selectedUnitIds)
                ? selectedUnitIds
                : (selectedUnitIds ? [selectedUnitIds] : []);

            if (ids.length) {
                unitSelect.val(ids.map(String));
            }
        }

        function groupCourseItemsForEdit(packageItems) {
            const groups = new Map();

            packageItems.forEach(function(item) {
                const teacherId = getTeacherIdByUnitId(item.unit_id);
                const key = (item.subject_id || 'null') + '_' + teacherId;

                if (!groups.has(key)) {
                    groups.set(key, {
                        subject_id: item.subject_id || '',
                        teacher_id: teacherId,
                        unit_ids: [],
                    });
                }

                groups.get(key).unit_ids.push(String(item.unit_id));
            });

            return Array.from(groups.values());
        }

        function applyPackageSections(modal) {
            const withCourse = modal.find('.include-with-course-checkbox').is(':checked');
            const withoutCourse = modal.find('.include-without-course-checkbox').is(':checked');

            modal.find('.with-course-section').toggle(withCourse);
            modal.find('.without-course-section').toggle(withoutCourse);

            modal.find('.with-course-section').find('select, button').prop('disabled', !withCourse);
            modal.find('.without-course-section').find('select').prop('disabled', !withoutCourse);
        }

        function buildPackageItemRow(container, index, selectedSubjectId = '', selectedUnitIds = [], selectedTeacherId = '', selectedSubjectVideoId = '') {
            const unitIds = Array.isArray(selectedUnitIds)
                ? selectedUnitIds
                : (selectedUnitIds ? [selectedUnitIds] : []);

            if (!selectedTeacherId && unitIds.length) {
                selectedTeacherId = getTeacherIdByUnitId(unitIds[0]);
            }
            if (!selectedSubjectVideoId && selectedTeacherId) {
                selectedSubjectVideoId = resolveSubjectVideoIdByTeacherId(selectedTeacherId);
            }

            const row = $(`
                <div class="package-item-row" data-index="${index}">
                    <div class="row">
                       
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
                            <select class="form-control package-unit-select" name="package_items[${index}][unit_ids][]" multiple disabled>
                            </select>
                            <small class="text-muted d-block mt-1">{{ trans('main_trans.Hold_Ctrl_to_select_multiple') }}</small>
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
            populateUnitSelect(row.find('.package-unit-select'), selectedTeacherId, unitIds);

            if (selectedSubjectId) {
                row.find('.package-subject-select').val(selectedSubjectId);
            }
        }

        function reindexPackageItems(container) {
            container.find('.package-item-row').each(function(index) {
                $(this).attr('data-index', index);
                $(this).find('.package-subject-select').attr('name', `package_items[${index}][subject_id]`);
                $(this).find('.package-unit-select').attr('name', `package_items[${index}][unit_ids][]`);
            });
        }

        function resetWithCourseSection(modal, packageItems = []) {
            const container = modal.find('.package-items-container');
            container.empty();

            if (packageItems.length === 0) {
                buildPackageItemRow(container, 0);
                return;
            }

            groupCourseItemsForEdit(packageItems).forEach(function(item, index) {
                buildPackageItemRow(container, index, item.subject_id, item.unit_ids, item.teacher_id);
            });
        }

        function rowHasSelectedUnits(unitSelect) {
            const val = unitSelect.val();
            return Array.isArray(val) ? val.length > 0 : !!val;
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
            populateUnitSelect(row.find('.package-unit-select'), '', []);
        });

        $(document).on('change', '.package-teacher-select', function() {
            const row = $(this).closest('.package-item-row');
            populateUnitSelect(row.find('.package-unit-select'), $(this).val(), []);
        });

        $(document).on('click', '.remove-package-item', function() {
            const container = $(this).closest('.package-items-container');
            $(this).closest('.package-item-row').remove();
            reindexPackageItems(container);
        });

        $('.edit-package-btn').on('click', function() {
            const includeWithCourse = String($(this).data('include-with-course')) === '1';
            const includeWithoutCourse = String($(this).data('include-without-course')) === '1';
            const courseItems = $(this).data('course-items') || [];
            const subjectIds = $(this).data('subject-ids') || [];
            const modal = $('#editPackageModal');

            $('#edit_name').val($(this).data('package-name'));
            $('#edit_expires_at').val($(this).data('package-expires'));
            modal.find('.include-with-course-checkbox').prop('checked', includeWithCourse);
            modal.find('.include-without-course-checkbox').prop('checked', includeWithoutCourse);
            modal.find('.package-subjects-multi').val(subjectIds.map(String));
            resetWithCourseSection(modal, courseItems);
            applyPackageSections(modal);
        });

        $('#editPackageForm').on('submit', function(e) {
            const modal = $(this).closest('.modal');
            const withCourse = modal.find('.include-with-course-checkbox').is(':checked');
            const withoutCourse = modal.find('.include-without-course-checkbox').is(':checked');

            if (!withCourse && !withoutCourse) {
                e.preventDefault();
                alert('{{ trans('main_trans.At_least_one_content_type_required') }}');
                return;
            }

            if (withCourse) {
                const hasUnit = modal.find('.package-unit-select').toArray().some(function(select) {
                    return rowHasSelectedUnits($(select));
                });
                if (!hasUnit) {
                    e.preventDefault();
                    alert('{{ trans('main_trans.At_least_one_subject_unit_required') }}');
                    return;
                }
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
            var button = $(event.relatedTarget);
            $(this).find('.modal-body #id').val(button.data('id'));
            $(this).find('.modal-body #name').val(button.data('name'));
        });
    </script>

@endsection
