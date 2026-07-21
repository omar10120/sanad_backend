@extends('layouts.master')
@section('css')
    <!-- Internal Nice-select css  -->
    <link href="{{URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet" />
    <!---Internal Fileupload css-->
    <link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
    <!---Internal Fancy uploader css-->
    <link href="{{URL::asset('assets/plugins/fancyuploder/fancy_fileupload.css')}}" rel="stylesheet" />
@endsection

@section('title')
    {{ trans('main_trans.Edit_user') }}
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Users') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Edit_user') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

    @include('components.flash-messages')

    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('users.index') }}">{{ trans('main_trans.Back') }}</a>
                        </div>
                    </div><br>
                    <form class="parsley-style-1" id="selectForm2" autocomplete="off" name="selectForm2"
                          action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-6" id="fnWrapper">
                                <label for="name_ar" class="card-title mb-1">{{ trans('main_trans.Name_ar') }} <span class="tx-danger">*</span></label>
                                <input type="hidden" name="id" value="{{$user->id}}">
                                <input class="form-control mg-b-20" id="name_ar" value="{{$user->name_ar}}"
                                       data-parsley-class-handler="#lnWrapper" name="name_ar" required="" type="text" placeholder="{{ trans('main_trans.Name_ar') }}">
                            </div>
                            <div class="parsley-input col-md-6" id="fnWrapper">
                                <label for="name_en" class="card-title mb-1">{{ trans('main_trans.Name_en') }} <span class="tx-danger">*</span></label>
                                <input type="hidden" name="id" value="{{$user->id}}">
                                <input class="form-control mg-b-20" id="name_en" value="{{$user->name_en}}"
                                       data-parsley-class-handler="#lnWrapper" name="name_en" required="" type="text" placeholder="{{ trans('main_trans.Name_en') }}">
                            </div>
                        </div>

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="email" class="card-title mb-1">{{ trans('main_trans.Email') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="email" value="{{$user->email}}" required
                                       data-parsley-class-handler="#lnWrapper" name="email" type="email" placeholder="{{ trans('main_trans.Email') }}">
                            </div>

                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="phone" class="card-title mb-1">{{ trans('main_trans.Phone') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="phone" value="{{$user->phone}}"
                                       data-parsley-class-handler="#lnWrapper" name="phone" required="" type="text" placeholder="{{ trans('main_trans.Phone') }}">
                            </div>
                        </div>

                        <div class="row mg-b-20">

                            <div class="col-sm-12 col-md-3 mg-t-10 mg-sm-t-0">
                                <div class="col-sm-12 col-md-8 mg-t-10 mg-sm-t-0">
                                    <h4 class="card-title mb-1">{{ trans('main_trans.Add_image_user') }} <span class="tx-danger">*</span></h4>
                                </div>
                                @if($user->photo != null)
                                    <input type="file" class="dropify" id="photo" name="photo" accept=".jpg, .png, image/jpeg, image/png"
                                           data-default-file="{{URL::asset('assets/image/Users')}}/{{$user->id}}/{{$user->photo}}" data-height="80"/>
                                @else
                                    <input type="file" class="dropify" id="photo" name="photo" accept=".jpg, .png, image/jpeg, image/png"
                                           data-default-file="{{URL::asset('assets/image/sanad.jpg')}}" data-height="80"/>
                                @endif
                            </div>
                            <div class="col-sm-12 col-md-3 mg-t-10 mg-sm-t-0">
                                <p class="card-title mb-1">{{ trans('main_trans.Required_image_user') }}</p>
                                <p class="text-danger">{{ trans('main_trans.jpg') }}</p>
                                <p class="text-danger">{{ trans('main_trans.resolution_1280_1280') }}</p>
                            </div>

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <div class="form-group">
                                    <label for="role" class="form-label card-title mb-1">{{ trans('main_trans.User_permission') }} <span class="tx-danger">*</span></label>
                                    <select id="role" name="roles_name[]" class="form-control  nice-select  custom-select" {{--multiple--}}>
                                        <option value="" selected disabled>{{ trans('main_trans.Select_role') }}</option>
                                        @foreach($roles as $role)
                                            @if($role == $user->getRoleNames()[0])
                                                <option selected value="{{ $role }}">{{ $role }}</option>
                                            @else
                                                <option value="{{ $role }}">{{ $role }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <div class="form-group">
                                    <label for="status" class="form-label card-title mb-1">{{ trans('main_trans.User_status') }} <span class="tx-danger">*</span></label>
                                    <select name="status" id="status" class="form-control  nice-select  custom-select">
                                        <option value="" selected disabled>{{ trans('main_trans.Select_status') }}</option>
                                        <option value="0" @php if($user->status==0) echo "selected"; @endphp>{{ trans('main_trans.Disable') }}</option>
                                        <option value="1" @php if($user->status==1) echo "selected"; @endphp>{{ trans('main_trans.Enable') }}</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- Subject Assignment Section -->
                        <div class="row mg-b-20">
                            <div class="col-12">
                                <h5 class="card-title mb-3">{{ trans('main_trans.Assign_Subjects') }}</h5>

                                @if($user->hasRole('Owner'))
                                    <div class="alert alert-info">
                                        <strong>{{ trans('main_trans.Owner_Role_Notice') }}</strong><br>
                                        {{ trans('main_trans.Owner_Role_Description') }}
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <strong>{{ trans('main_trans.Subject_Assignment_Notice') }}</strong><br>
                                        {{ trans('main_trans.Subject_Assignment_Description') }}
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="subjects" class="form-label card-title mb-1">{{ trans('main_trans.Available_Subjects') }}</label>
                                                <select name="subjects[]" id="subjects" class="form-control" multiple size="12">
                                                    @foreach($availableSubjects as $subject)
                                                        <option value="{{ $subject->id }}"
                                                            {{ in_array($subject->id, $assignedSubjects->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                            {{ $subject->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">{{ trans('main_trans.Hold_Ctrl_Select_Multiple') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="hidden" name="show_all_teachers" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="show_all_teachers"
                                                           name="show_all_teachers" value="1"
                                                           {{ $user->show_all_teachers ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="show_all_teachers">
                                                        {{ trans('main_trans.Show_all_teachers') }}
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">{{ trans('main_trans.Show_all_teachers_hint') }}</small>
                                            </div>
                                            <div class="form-group">
                                                <label for="teacher_id" class="form-label card-title mb-1">{{ trans('main_trans.Available_Teachers') }}</label>
                                                <select name="teacher_id" id="teacher_id" class="form-control"
                                                    {{ $user->show_all_teachers ? 'disabled' : '' }}>
                                                    <option value="">{{ trans('main_trans.Select_teacher') }}</option>
                                                    @foreach($availableTeachers as $teacher)
                                                        <option value="{{ $teacher->id }}"
                                                            {{ (int) $assignedTeacherId === (int) $teacher->id ? 'selected' : '' }}>
                                                            {{ $teacher->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>{{ trans('main_trans.Currently_Assigned_Subjects') }}</h6>
                                            @if($assignedSubjects->count() > 0)
                                                <ul class="list-group">
                                                    @foreach($assignedSubjects as $subject)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $subject->name }}
                                                            <span class="badge badge-primary badge-pill">{{ $subject->lessons->count() ?? 0 }} {{ trans('main_trans.Lessons') }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="alert alert-warning">
                                                    {{ trans('main_trans.No_Subjects_Assigned') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            @can('User-edit')
                                <button class="btn btn-main-primary pd-x-20" type="submit">{{ trans('main_trans.Edit_user') }}</button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
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
    <script>
        (function () {
            var checkbox = document.getElementById('show_all_teachers');
            var teacherSelect = document.getElementById('teacher_id');
            if (!checkbox || !teacherSelect) return;

            function syncTeacherSelect() {
                teacherSelect.disabled = checkbox.checked;
                if (checkbox.checked) {
                    teacherSelect.value = '';
                }
            }

            checkbox.addEventListener('change', syncTeacherSelect);
            syncTeacherSelect();
        })();
    </script>
@endsection
