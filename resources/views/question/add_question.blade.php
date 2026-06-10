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
    {{ trans('main_trans.Add_question') }}
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="col-lg-6 my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Questions') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Add_question') }}</span>
            </div>
        </div>
        <div class="col-lg-6 margin-tb">
            <div class="pull-right">
                <a class="btn btn-primary btn-sm" href="{{ url('lesson/' . $data->lesson->id . '/question-group') }}">{{ trans('main_trans.Back') }}</a>
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
                <form id="addQuestionForm" method="POST" action="{{ route('question.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body pt-4">
                        <div class="row mb-3">
                            <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
                                <!-- Select Subject -->
                                <div class="form-group mb-3">
                                    <label for="subject" class="card-title mb-1">{{ trans('main_trans.Subject') }}</label>
                                    <select class="form-control  nice-select  custom-select" id="subject" name="" required disabled>
                                        {{--                                    <option value="" selected disabled>{{ trans('main_trans.Select_subject') }}</option>--}}
                                        <option value="{{$data->lesson->subject->id}}" selected>{{$data->lesson->subject->name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
                                <!-- Select Lesson -->
                                <div class="form-group mb-3">
                                    <label for="lesson" class="card-title mb-1">{{ trans('main_trans.Lesson') }}</label>
                                    <select class="form-control  nice-select  custom-select" id="lesson" name="" required disabled>
                                        {{--                                    <option value="" selected disabled>{{ trans('main_trans.Select_lesson') }}</option>--}}
                                        <option value="{{$data->lesson->id}}" selected>{{$data->lesson->title}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="lesson_id" value="{{$data->lesson->id}}">
                        <input type="hidden" name="subject_id" value="{{$data->lesson->subject->id}}">

                        <!-- Select question group -->
                        <div class="form-group mb-3">
                            <label for="question-group" class="card-title mb-1">{{ trans('main_trans.Question_group') }}</label>
                            <select class="form-control select2" id="question-group" name="question_group_id">
                                <option value="-1" selected>{{ trans('main_trans.New_question_group') }}</option>
                                @foreach($data->question_groups as $question_group)
                                    <option value="{{$question_group->id}}"
                                            @if($question_group->id == $data->group_id) selected @endif
                                    >{{ $question_group->id . ' - ' . $question_group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Question Text -->
                        <div class="form-group mb-3">
                            <label for="questionText" class="card-title mb-1">{{ trans('main_trans.Question_text') }}</label>
                            <div class="question-editor ltr" id="questionTextEditor" style="height: 150px;" data-placeholder="{{ trans('main_trans.Question_text') }}"></div>
                            <input type="hidden" name="text_question" id="questionText" required>
                        </div>

                        <!-- Question image -->
                        <div class="row mb-3">
                            <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                <div class="col-sm-12 col-md-8 mg-t-10 mg-sm-t-0">
                                    <h4 class="card-title mb-1">{{ trans('main_trans.Image_Question') }}</h4>
                                </div>
                                <input type="file" class="dropify" id="questionPhoto" name="question_photo" data-height="120" accept=".jpg, .png, image/jpeg, image/png"/>
                            </div>
                            <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                <p class="card-title mb-2">{{ trans('main_trans.Required_image_question') }}</p>
                                <p class="text-danger">{{ trans('main_trans.jpg') }}</p>
                                <p class="text-danger">{{ trans('main_trans.resolution_1280_1280') }}</p>
                                <p class="text-danger">{{ trans('main_trans.Size_less_than_1MB') }}</p>
                            </div>
                        </div>

                        <!-- Hint -->
                        <div class="form-group mb-3">
                            <label for="hint" class="card-title mb-1">{{ trans('main_trans.Hint') }}</label>
                            <div class="question-editor ltr" id="hintEditor" style="height: 100px;" data-placeholder="{{ trans('main_trans.Hint') }}"></div>
                            <input type="hidden" name="hint" id="hint" required>
                        </div>

                        <!-- Hint image -->
                        <div class="row mb-3">
                            <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                <div class="col-sm-12 col-md-8 mg-t-10 mg-sm-t-0">
                                    <h4 class="card-title mb-1">{{ trans('main_trans.Image_hint') }}</h4>
                                </div>
                                <input type="file" class="dropify" id="hintPhoto" name="hint_photo" data-height="120" accept=".jpg, .png, image/jpeg, image/png"/>
                            </div>
                            <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                <p class="card-title mb-2">{{ trans('main_trans.Required_image_hint') }}</p>
                                <p class="text-danger">{{ trans('main_trans.jpg') }}</p>
                                <p class="text-danger">{{ trans('main_trans.resolution_1280_1280') }}</p>
                                <p class="text-danger">{{ trans('main_trans.Size_less_than_1MB') }}</p>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
            <div class="card  box-shadow-0 ">
                <div class="card-body pt-4">

                    <!-- Question Tags -->
                    <div class="form-group mb-3">
                        <p class="card-title mb-1">{{ trans('main_trans.Question_tags') }}</p>
                        <select class="form-control select2" name="tags[]" multiple="multiple">
{{--                            <option value="Firefox">Firefox</option>--}}
                            @foreach($data->tags as $tag)
                                <option value="{{$tag->id}}">{{$tag->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Question Type -->
                    <div class="mb-3 mt-3">
                        <label for="form-group questionType" class="card-title mb-1">{{ trans('main_trans.Question_type') }}</label>
                        <select class="form-control select2" id="questionType" name="type_id" required>
                            <option value="" disabled>{{ trans('main_trans.Select_question_type') }}</option>
                            @foreach($data->types as $type)
                                <option value="{{$type->id}}" data-type="{{$type->type}}" @if($type->id==1) selected @endif>{{$type->name}}</option>
                            @endforeach
{{--                            <option value="multipleChoice">Multiple Choice</option>--}}
{{--                            <option value="trueFalse">True / False</option>--}}
{{--                            <option value="other">Other</option>--}}
                        </select>
                    </div>

                    <!-- Answer Section -->
                    <div class="form-group mb-3" id="answerSection">
                        <!-- Dynamic fields will be added here -->
                    </div>

                    <!-- Hidden fields for dynamic data -->
                    <input type="hidden" name="choices" id="hiddenChoices">
                    <input type="hidden" name="correctAnswer" id="hiddenCorrectAnswer">

                    <div class="card-header">
                        <h4 class="card-title mb-1">{{ trans('main_trans.Please_check') }}</h4>
                    </div>
                    <div class="form-group mb-0 mt-3 justify-content-end">
                        <div>
                            @can('Question-add')
                                <button type="submit" class="btn btn-primary">{{ trans('main_trans.Add_question') }}</button>
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
    <!--Question form js -->
    @if(App::getLocale() == 'ar')
        <script src="{{URL::asset('assets/plugins/question-form/question-form-ar.js')}}"></script>
    @else
        <script src="{{URL::asset('assets/plugins/question-form/question-form.js')}}"></script>
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

{{--    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>--}}
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.21/dist/katex.min.js" integrity="sha384-Rma6DA2IPUwhNxmrB/7S3Tno0YY7sFu9WSYMCuulLhIqYSGZ2gKCJWIqhBWqMQfh" crossorigin="anonymous"></script>
@endsection
