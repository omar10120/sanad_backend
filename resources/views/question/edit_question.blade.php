@extends('layouts.master')

@section('css')
    <!-- Internal Nice-select css  -->
    <link href="{{URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet" />
    <!---Internal Fileupload css-->
    <link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
    <!---Internal Fancy uploader css-->
    <link href="{{URL::asset('assets/plugins/fancyuploder/fancy_fileupload.css')}}" rel="stylesheet" />
    <!--- Internal Select2 css-->
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <!--Internal  Quill css -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />

    <!---Question form css-->
    <link href="{{URL::asset('assets/plugins/question-form/question-form.css')}}" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.21/dist/katex.min.css" integrity="sha384-zh0CIslj+VczCZtlzBcjt5ppRcsAmDnRem7ESsYwWwg3m/OaJ2l4x7YBZl9Kxxib" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('title')
    {{ trans('main_trans.Edit_question') }}
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="col-lg-6 my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Questions') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Edit_question') }}</span>
            </div>
        </div>
        <div class="col-lg-6 margin-tb">
            <div class="pull-right">
                <a class="btn btn-primary btn-sm" href="{{ url('lesson/' . $lesson->id . '/question-group') }}">{{ trans('main_trans.Back') }}</a>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')

    @include('components.flash-messages')

    <!-- row -->
    <div class="row">
        <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
            <div class="card  box-shadow-0">
                <form id="editQuestionForm" method="POST" action="{{ route('question.update', $question->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body pt-4">
                        <input type="hidden" name="id" value="{{$question->id}}">

                        <div class="row mb-3">
                            <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
                                <!-- Select Subject -->
                                <div class="form-group mb-3">
                                    <label for="subject" class="card-title mb-1">{{ trans('main_trans.Subject') }}</label>
                                    <select class="form-control select2" id="subject" name="subject_id" required>
                                        <option value="" disabled>{{ trans('main_trans.Select_subject') }}</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{$subject->id}}" @if($subject->id == $lesson->subject->id) selected @endif>{{$subject->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
                                <!-- Select Lesson -->
                                <div class="form-group mb-3">
                                    <label for="lesson" class="card-title mb-1">{{ trans('main_trans.Lesson') }}</label>
                                    <select class="form-control select2" id="lesson" name="lesson_id" required>
                                        <option value="" disabled>{{ trans('main_trans.Select_lesson') }}</option>
                                        @foreach($lessons as $lessonOption)
                                            <option value="{{$lessonOption->id}}" @if($lessonOption->id == $lesson->id) selected @endif>{{$lessonOption->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Select question group -->
                        <div class="form-group mb-3">
                            <label for="question-group" class="card-title mb-1">{{ trans('main_trans.Question_group') }}</label>
                            <select class="form-control select2" id="question-group" name="question_group_id" data-new-group-label="{{ trans('main_trans.New_question_group') }}">
                                <option value="-1">{{ trans('main_trans.New_question_group') }}</option>
                                @foreach($question_groups as $question_group)
                                    <option value="{{$question_group->id}}"
                                            @if($question_group->id == $current_question_group->id) selected @endif
                                    >{{ $question_group->id . ' - ' . $question_group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Question Text -->
                        <div class="form-group mb-3">
                            <label for="questionText" class="card-title mb-1">{{ trans('main_trans.Question_text') }}</label>
                            <div class="question-editor ltr" id="questionTextEditor" style="height: 150px" data-placeholder="{{ trans('main_trans.Question_text') }}"></div>
                            <input type="hidden" name="text_question" id="questionText" value="{{ json_encode($question->text_question) }}" required>
                        </div>

                        <!-- Question image -->
                        <div class="row mb-3">
                            <div class="col-sm-12 col-md-4 mg-t-10 mg-sm-t-0">
                                <div class="col-sm-12 col-md-12 mg-t-10 mg-sm-t-0">
                                    <h4 class="card-title mb-1">{{ trans('main_trans.Image_Question') }}</h4>
                                </div>


                                <input type="file" class="dropify" id="questionPhoto" name="question_photo" data-height="120" accept=".jpg, .png, image/jpeg, image/png"
                                    {{-- @if($question->question_photo)
                                        data-default-file="{{ asset('assets/image/Question/' . $question->id . '/question-photo/' . $question->question_photo) }}"
                                    @endif --}}
                                />
                                <small class="text-muted">{{ trans('main_trans.Leave empty to keep current photo') }}</small>
                            </div>
                            <div class="col-sm-12 col-md-4 mg-t-10 mg-sm-t-0">
                                <p class="card-title mb-2">{{ trans('main_trans.Required_image_question') }}</p>
                                <p class="text-danger">{{ trans('main_trans.jpg') }}</p>
                                <p class="text-danger">{{ trans('main_trans.resolution_1280_1280') }}</p>
                                <p class="text-danger">{{ trans('main_trans.Size_less_than_1MB') }}</p>
                            </div>

                            <div class="col-sm-12 col-md-4 mg-t-10 mg-sm-t-0">
                                @if($question->question_photo)
                                    <div class="current-photo-container mb-3">
                                        <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('assets/image/Question/' . $question->id . '/question-photo/' . $question->question_photo) }}"
                                                    alt="Question Photo"
                                                    class="img-thumbnail me-3"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                <div>
                                                    <small class="text-muted">{{ trans('main_trans.Current Photo') }}</small>
                                                </div>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm delete-photo-btn"
                                                    data-photo-type="question"
                                                    data-question-id="{{ $question->id }}"
                                                    title="{{ trans('main_trans.Delete Photo') }}">
                                                <i class="fas fa-trash"></i> {{ trans('main_trans.Delete') }}
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Hint -->
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <label for="hint" class="card-title mb-1 mb-0">{{ trans('main_trans.Hint') }}</label>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="clearHintBtn">
                                    <i class="fas fa-trash-alt"></i> {{ trans('main_trans.Clear_hint') }}
                                </button>
                            </div>
                            <div class="question-editor ltr" id="hintEditor" style="height: 100px;" data-placeholder="{{ trans('main_trans.Hint') }}"></div>
                            <input type="hidden" name="hint" id="hint" value="{{ json_encode($question->hint) }}" required>
                            <input type="hidden" name="clear_hint" id="clear_hint" value="0">
                        </div>

                        <!-- Hint image -->
                        <div class="row mb-3">
                            <div class="col-sm-12 col-md-4 mg-t-10 mg-sm-t-0">
                                <div class="col-sm-12 col-md-12 mg-t-10 mg-sm-t-0">
                                    <h4 class="card-title mb-1">{{ trans('main_trans.Image_hint') }}</h4>
                                </div>

                                <input type="file" class="dropify" id="hintPhoto" name="hint_photo" data-height="120" accept=".jpg, .png, image/jpeg, image/png"
                                   {{-- @if($question->hint_photo)
                                       data-default-file="{{ asset('assets/image/Question/' . $question->id . '/hint-photo/' . $question->hint_photo) }}"
                                    @endif --}}
                                />
                                <small class="text-muted">{{ trans('main_trans.Leave empty to keep current photo') }}</small>
                            </div>
                            <div class="col-sm-12 col-md-4 mg-t-10 mg-sm-t-0">
                                <p class="card-title mb-2">{{ trans('main_trans.Required_image_hint') }}</p>
                                <p class="text-danger">{{ trans('main_trans.jpg') }}</p>
                                <p class="text-danger">{{ trans('main_trans.resolution_1280_1280') }}</p>
                                <p class="text-danger">{{ trans('main_trans.Size_less_than_1MB') }}</p>
                            </div>

                            <div class="col-sm-12 col-md-4 mg-t-10 mg-sm-t-0">
                                @if($question->hint_photo)
                                    <div class="current-photo-container mb-3">
                                        <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('assets/image/Question/' . $question->id . '/hint-photo/' . $question->hint_photo) }}"
                                                    alt="Hint Photo"
                                                    class="img-thumbnail me-3"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                <div>
                                                    <small class="text-muted">{{ trans('main_trans.Current Photo') }}</small>
                                                </div>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm delete-photo-btn"
                                                    data-photo-type="hint"
                                                    data-question-id="{{ $question->id }}"
                                                    title="{{ trans('main_trans.Delete Photo') }}">
                                                <i class="fas fa-trash"></i> {{ trans('main_trans.Delete') }}
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Question is edited -->
                        <div class="mb-3 mt-3">
                            <label for="is_edited" class="card-title mb-1">{{ trans('main_trans.Question_edited') }}</label>
                            <select class="form-control nice-select" id="is_edited" name="is_edited" required>
                                @if($question->is_edited)
                                    <option value="1" selected>{{ trans('main_trans.Edited') }}</option>
                                    <option value="0" >{{ trans('main_trans.Unedited') }}</option>
                                @else
                                    <option value="1" >{{ trans('main_trans.Edited') }}</option>
                                    <option value="0" selected>{{ trans('main_trans.Unedited') }}</option>
                                @endif
                            </select>
                        </div>

                    </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
            <div class="card  box-shadow-0 ">
                <div class="card-body pt-4">

                    <!-- Question Tags -->
                    <div class="form-group mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="card-title mb-1 mb-0">{{ trans('main_trans.Question_tags') }}</p>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="clearTagsBtn">
                                <i class="fas fa-times-circle"></i> {{ trans('main_trans.Clear_tags') }}
                            </button>
                        </div>
                        <select class="form-control select2" id="questionTags" name="tags[]" multiple="multiple">
                            @foreach($tags as $tag)
                                <option value="{{$tag->id}}" {{ in_array($tag->id, $question->tags->pluck('id')->toArray()) ? 'selected' : '' }}>{{$tag->name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="clear_tags" id="clear_tags" value="0">
                    </div>

                    <!-- Question Type -->
                    <div class="mb-3 mt-3">
                        <label for="form-group questionType" class="card-title mb-1">{{ trans('main_trans.Question_type') }}</label>
                        <select class="form-control select2" id="questionType" name="type_id" required>
                            <option value="" selected disabled>{{ trans('main_trans.Select_question_type') }}</option>
                            @foreach($types as $type)
                                <option value="{{$type->id}}" data-type="{{$type->type}}" {{ $question->type_id == $type->id ? 'selected' : '' }}>{{$type->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Answer Section -->
                    <div class="form-group mb-3" id="answerSection">
                        <!-- Dynamic fields will be added here -->
                    </div>

                    <!-- Hidden fields for dynamic data -->
                    <input type="hidden" name="choices" id="hiddenChoices" value="{{ json_encode($question->choices) }}">
                    <input type="hidden" name="correctAnswer" id="hiddenCorrectAnswer" value="{{ $question->right_choice }}">

                    <div class="card-header">
                        <h4 class="card-title mb-1">{{ trans('main_trans.Please_check') }}</h4>
                    </div>
                    <div class="form-group mb-0 mt-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            @can('Question-edit')
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary m-1" id="saveAndBackBtn">
                                        <i class="fas fa-save"></i> {{ trans('main_trans.Save_and_back') }}
                                    </button>
                                    @if(isset($nextQuestion) && $nextQuestion)
                                        <button type="submit" class="btn btn-success m-1" id="saveAndNextBtn">
                                            <i class="fas fa-arrow-right"></i> {{ trans('main_trans.Save_and_next') }}
                                        </button>
                                    @endif
                                    <button type="submit" class="btn btn-primary" id="defaultSubmitBtn" style="display: none;">
                                        {{ trans('main_trans.Edit_question') }}
                                    </button>
                                </div>
                            @endcan
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- row closed -->
    </div>

    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection

@section('js')
    <script>
        window.questionEditConfig = {
            routes: {
                lessonsBySubject: "{{ route('subjects.lessons-list', ':subjectId') }}",
                tagsBySubject: "{{ route('subjects.tags-list', ':subjectId') }}",
                questionGroupsByLesson: "{{ route('lessons.question-groups', ':lessonId') }}"
            },
            initial: {
                subjectId: {{ $lesson->subject->id }},
                lessonId: {{ $lesson->id }},
                questionGroupId: {{ $current_question_group->id ?? 'null' }}
            },
            labels: {
                newGroup: "{{ trans('main_trans.New_question_group') }}",
                selectLesson: "{{ trans('main_trans.Select_lesson') }}"
            }
        };
    </script>
    <!--Question form js -->
    @if(App::getLocale() == 'ar')
        <script src="{{URL::asset('assets/plugins/question-form/question-form-edit-ar.js')}}"></script>
    @else
        <script src="{{URL::asset('assets/plugins/question-form/question-form-edit.js')}}"></script>
    @endif

    <!--Internal Fileuploads js-->
    <script src="{{URL::asset('assets/plugins/fileuploads/js/fileupload.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fileuploads/js/file-upload.js')}}"></script>
    <!--Internal Fancy uploader js-->
    <script src="{{URL::asset('assets/plugins/fancyuploder/jquery.ui.widget.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fancyuploder/jquery.fileupload.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fancyuploder/jquery.iframe-transport.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/fancyuploder/fancy-uploader.js')}}"></script>
    <!-- Internal Nice-select js-->
    <script src="{{URL::asset('assets/plugins/jquery-nice-select/js/jquery.nice-select.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/jquery-nice-select/js/nice-select.js')}}"></script>

    <!--Internal  Parsley.min js -->
    <script src="{{URL::asset('assets/plugins/parsleyjs/parsley.min.js')}}"></script>
    <!-- Internal Form-validation js -->
    <script src="{{URL::asset('assets/js/form-validation.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/form-elements.js')}}"></script>


    <!--Internal quill js -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.21/dist/katex.min.js" integrity="sha384-Rma6DA2IPUwhNxmrB/7S3Tno0YY7sFu9WSYMCuulLhIqYSGZ2gKCJWIqhBWqMQfh" crossorigin="anonymous"></script>

    <script src="{{URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js')}}"></script>

    <!-- Photo deletion functionality -->
    <script>
    $(document).ready(function() {
        // Handle photo deletion
        $('.delete-photo-btn').click(function() {
            const photoType = $(this).data('photo-type');
            const questionId = $(this).data('question-id');
            const button = $(this);
            const container = button.closest('.current-photo-container');

            // Show confirmation dialog
            if (!confirm('{{ trans("main_trans.Are you sure you want to delete this photo?") }}')) {
                return;
            }

            // Disable button during request
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ trans("main_trans.Deleting...") }}');

            const route = photoType === 'question'
                ? '{{ route("questions.delete-question-photo", ":id") }}'
                : '{{ route("questions.delete-hint-photo", ":id") }}';

            const url = route.replace(':id', questionId);

            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the photo container with animation
                        container.fadeOut(300, function() {
                            $(this).remove();
                        });

                        // Show success message
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                        button.prop('disabled', false).html('<i class="fas fa-trash"></i> {{ trans("main_trans.Delete") }}');
                    }
                },
                error: function(xhr) {
                    let errorMessage = '{{ trans("main_trans.Error deleting photo") }}';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    toastr.error(errorMessage);
                    button.prop('disabled', false).html('<i class="fas fa-trash"></i> {{ trans("main_trans.Delete") }}');
                }
            });
        });

        // Handle Save and Back button
        $('#saveAndBackBtn').on('click', function(e) {
            e.preventDefault();

            // Add hidden input to indicate action
            if ($('input[name="action"]').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'action',
                    value: 'save_and_back'
                }).appendTo('#editQuestionForm');
            } else {
                $('input[name="action"]').val('save_and_back');
            }

            // Validate form before submit
            if (validateForm()) {
                $('#editQuestionForm').submit();
            }
        });

        // Handle Save and Next button
        $('#saveAndNextBtn').on('click', function(e) {
            e.preventDefault();

            // Add hidden input to indicate action
            if ($('input[name="action"]').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'action',
                    value: 'save_and_next'
                }).appendTo('#editQuestionForm');
            } else {
                $('input[name="action"]').val('save_and_next');
            }

            // Validate form before submit
            if (validateForm()) {
                $('#editQuestionForm').submit();
            }
        });

        // Form validation function
        function validateForm() {
            let isValid = true;

            // Check required fields
            const requiredFields = [
                { selector: '#subject', message: '{{ trans("main_trans.Subject_required") }}' },
                { selector: '#lesson', message: '{{ trans("main_trans.Lesson_required") }}' },
                { selector: '#questionType', message: '{{ trans("main_trans.Question_type_required") }}' }
            ];

            requiredFields.forEach(function(field) {
                const $field = $(field.selector);
                if (!$field.val() || $field.val() === '' || $field.val() === null) {
                    isValid = false;
                    toastr.error(field.message);
                }
            });

            // Check question text
            const questionText = $('#questionText').val();
            if (!questionText || questionText === '' || questionText === 'null') {
                isValid = false;
                toastr.error('{{ trans("main_trans.Question_text_required") }}');
            }

            return isValid;
        }
    });
    </script>

    <!-- CSS styling for photo containers -->
    <style>
    .current-photo-container {
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .current-photo-container .border {
        border-color: #dee2e6 !important;
        transition: all 0.3s ease;
    }

    .current-photo-container .border:hover {
        border-color: #adb5bd !important;
        background-color: #ffffff;
    }

    .delete-photo-btn {
        min-width: 80px;
        transition: all 0.3s ease;
    }

    .delete-photo-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
    }

    .img-thumbnail {
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .current-photo-container:hover .img-thumbnail {
        border-color: #adb5bd;
        transform: scale(1.02);
    }

    .badge-info {
        background-color: #17a2b8;
        font-size: 0.75em;
    }

    @media (max-width: 768px) {
        .current-photo-container .d-flex {
            flex-direction: column;
            text-align: center;
        }

        .current-photo-container .me-3 {
            margin-right: 0 !important;
            margin-bottom: 1rem;
        }
    }

    /* Button styling for save actions */
    #saveAndBackBtn,
    #saveAndNextBtn {
        min-width: 140px;
        transition: all 0.3s ease;
    }

    #saveAndBackBtn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    #saveAndNextBtn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    @media (max-width: 576px) {
        .d-flex.gap-2 {
            flex-direction: column;
            width: 100%;
        }

        #saveAndBackBtn,
        #saveAndNextBtn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
    </style>

@endsection
