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
    {{ trans('main_trans.Edit_student') }}
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Students') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Edit_student') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('error') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @include('components.flash-messages')

    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('student.current-academic-year') }}">{{ trans('main_trans.Back') }}</a>
                        </div>
                    </div><br>
                    <form class="parsley-style-1" id="selectForm2" autocomplete="off" name="selectForm2"
                          action="{{ route('student.update', $student->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-4" id="fnWrapper">
                                <label for="first_name" class="card-title mb-1">{{ trans('main_trans.First_name') }} <span class="tx-danger">*</span></label>
                                <input type="hidden" name="id" value="{{$student->id}}">
                                <input class="form-control mg-b-20" id="first_name" value="{{$student->first_name}}"
                                       data-parsley-class-handler="#lnWrapper" name="first_name" required="" type="text" placeholder="{{ trans('main_trans.First_name') }}">
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="father_name" class="card-title mb-1">{{ trans('main_trans.Father_name') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="father_name" value="{{$student->father_name}}"
                                       data-parsley-class-handler="#lnWrapper" name="father_name" required="" type="text" placeholder="{{ trans('main_trans.Father_name') }}">
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="last_name" class="card-title mb-1">{{ trans('main_trans.Last_name') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="last_name" value="{{$student->last_name}}"
                                       data-parsley-class-handler="#lnWrapper" name="last_name" required="" type="text" placeholder="{{ trans('main_trans.Last_name') }}">
                            </div>
                        </div>

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="email" class="card-title mb-1">{{ trans('main_trans.Email') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="email" value="{{$student->email}}" required
                                       data-parsley-class-handler="#lnWrapper" name="email" type="email" placeholder="{{ trans('main_trans.Email') }}">
                            </div>

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="phone" class="card-title mb-1">{{ trans('main_trans.Phone') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="phone" value="{{$student->phone}}"
                                       data-parsley-class-handler="#lnWrapper" name="phone" required="" type="text" placeholder="{{ trans('main_trans.Phone') }}">
                            </div>

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <div class="form-group">
                                    <label for="city" class="form-label card-title mb-1">{{ trans('main_trans.City') }} <span class="tx-danger">*</span></label>
                                    <select id="city" name="city" class="form-control  nice-select  custom-select">
                                        <option value="" disabled>{{ trans('main_trans.Select_city') }}</option>
                                        <option value="damascus" @if($student->city=='damascus') selected @endif>{{ trans('main_trans.damascus') }}</option>
                                        <option value="damascus_suburb" @if($student->city=='damascus_suburb') selected @endif>{{ trans('main_trans.damascus_suburb') }}</option>
                                        <option value="homs" @if($student->city=='homs') selected @endif>{{ trans('main_trans.homs') }}</option>
                                        <option value="hama" @if($student->city=='hama') selected @endif>{{ trans('main_trans.hama') }}</option>
                                        <option value="aleppo" @if($student->city=='aleppo') selected @endif>{{ trans('main_trans.aleppo') }}</option>
                                        <option value="idlib" @if($student->city=='idlib') selected @endif>{{ trans('main_trans.idlib') }}</option>
                                        <option value="tartus" @if($student->city=='tartus') selected @endif>{{ trans('main_trans.tartus') }}</option>
                                        <option value="latakia" @if($student->city=='latakia') selected @endif>{{ trans('main_trans.latakia') }}</option>
                                        <option value="deir_ezzor" @if($student->city=='deir_ezzor') selected @endif>{{ trans('main_trans.deir_ezzor') }}</option>
                                        <option value="hasaka" @if($student->city=='hasaka') selected @endif>{{ trans('main_trans.hasaka') }}</option>
                                        <option value="raqqa" @if($student->city=='raqqa') selected @endif>{{ trans('main_trans.raqqa') }}</option>
                                        <option value="sweida" @if($student->city=='sweida') selected @endif>{{ trans('main_trans.sweida') }}</option>
                                        <option value="daraa" @if($student->city=='daraa') selected @endif>{{ trans('main_trans.daraa') }}</option>
                                        <option value="quneitra" @if($student->city=='quneitra') selected @endif>{{ trans('main_trans.quneitra') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="school" class="card-title mb-1">{{ trans('main_trans.School') }} </label>
                                <input class="form-control mg-b-20" id="school" value="{{$student->school}}"
                                       data-parsley-class-handler="#lnWrapper" name="school" required="" type="text" placeholder="{{ trans('main_trans.School') }}">
                            </div>

                        </div>

                        <div class="row mg-b-20">

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <div class="form-group">
                                    <label for="type_id" class="form-label card-title mb-1">{{ trans('main_trans.Certificate_type') }} <span class="tx-danger">*</span></label>
                                    <select id="type_id" name="type_id" class="form-control  nice-select  custom-select">
                                        <option value="" disabled>{{ trans('main_trans.Select_type') }}</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" @if($student->type_id== $type->id ) selected @endif>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-3 mg-t-10 mg-sm-t-0">
                                <div class="col-sm-12 col-md-8 mg-t-10 mg-sm-t-0">
                                    <h4 class="card-title mb-1">{{ trans('main_trans.Add_image_student') }} <span class="tx-danger">*</span></h4>
                                </div>
                                @if($student->photo != null)
                                    <input type="file" class="dropify" id="photo" name="photo" accept=".jpg, .png, image/jpeg, image/png"
                                           data-default-file="{{URL::asset('assets/image/Students')}}/{{$student->id}}/{{$student->photo}}" data-height="80"/>
                                @else
                                    <input type="file" class="dropify" id="photo" name="photo" accept=".jpg, .png, image/jpeg, image/png"
                                           data-default-file="{{URL::asset('assets/image/sanad.jpg')}}" data-height="80"/>
                                @endif
                            </div>
                            <div class="col-sm-12 col-md-3 mg-t-10 mg-sm-t-0">
                                <p class="card-title mb-1">{{ trans('main_trans.Required_image_student') }}</p>
                                <p class="text-danger">{{ trans('main_trans.jpg') }}</p>
                                <p class="text-danger">{{ trans('main_trans.resolution_1280_1280') }}</p>
                            </div>

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <div class="form-group">
                                    <label for="status" class="form-label card-title mb-1">{{ trans('main_trans.Student_status') }} <span class="tx-danger">*</span></label>
                                    <select name="status" id="status" class="form-control  nice-select  custom-select">
                                        <option value="" selected disabled>{{ trans('main_trans.Select_status') }}</option>
                                        <option value="0" @php if($student->status==0) echo "selected"; @endphp>{{ trans('main_trans.Disable') }}</option>
                                        <option value="1" @php if($student->status==1) echo "selected"; @endphp>{{ trans('main_trans.Enable') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="max_devices" class="card-title mb-1">
                                    {{trans('main_trans.Maximum_devices')}}
                                    @if(!config('features.student_devices'))
                                    <span class="badge badge-warning" style="font-size: 10px;">
                                        <i class="fas fa-crown"></i> {{ trans('main_trans.Pro_Only') }}
                                    </span>
                                    @else
                                    <span class="tx-danger">*</span>
                                    @endif
                                </label>
                                <input type="number" 
                                       class="form-control mg-b-20" 
                                       id="max_devices"
                                       name="max_devices" 
                                       min="1" 
                                       max="10" 
                                       value="{{$student->max_devices}}"
                                       data-parsley-class-handler="#lnWrapper"
                                       placeholder="{{trans('main_trans.Maximum_devices')}}" 
                                       @if(!config('features.student_devices')) disabled @else required @endif>
                                <small class="form-text text-muted">
                                    {{trans('main_trans.Device_limit_help_text')}}
                                </small>
                            </div>

                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            @can('Student-edit')
                                <button class="btn btn-main-primary pd-x-20" type="submit">{{ trans('main_trans.Edit_student') }}</button>
                            @endcan
                        </div>
                    </form>

                    <!-- Device Management Section -->
                    <div class="row mg-b-20">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        {{trans('main_trans.Registered_devices')}}
                                        @if(!config('features.student_devices'))
                                        <span class="badge badge-warning ml-2" style="font-size: 10px;">
                                            <i class="fas fa-crown"></i> {{ trans('main_trans.Pro_Only') }}
                                        </span>
                                        @endif
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(!config('features.student_devices'))
                                        <div class="text-center py-4">
                                            <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                                            <h5>{{ trans('main_trans.Pro_Feature_Title') }}</h5>
                                            <p class="text-muted">{{ trans('main_trans.Pro_Feature_Message') }}</p>
                                        </div>
                                    @elseif($student->studentDevices->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('main_trans.Device_info')}}</th>
                                                        <th>{{trans('main_trans.OS')}}</th>
                                                        <th>{{trans('main_trans.First_login')}}</th>
                                                        <th>{{trans('main_trans.Last_login')}}</th>
                                                        <th>{{trans('main_trans.Status')}}</th>
                                                        <th>{{trans('main_trans.Actions')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($student->studentDevices as $studentDevice)
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <strong>{{$studentDevice->device->brand}} {{$studentDevice->device->model}}</strong>
                                                                <br>
                                                                <small class="text-muted">{{$studentDevice->device->device_id}}</small>
                                                            </div>
                                                        </td>
                                                        <td>{{$studentDevice->device->os_name}} {{$studentDevice->device->os_version}}</td>
                                                        <td>{{$studentDevice->first_login_at ? $studentDevice->first_login_at->format('Y-m-d H:i') : '-'}}</td>
                                                        <td>{{$studentDevice->last_login_at ? $studentDevice->last_login_at->format('Y-m-d H:i') : '-'}}</td>
                                                        <td>
                                                            @if($studentDevice->is_current)
                                                                <span class="badge badge-success">{{trans('main_trans.Current')}}</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{trans('main_trans.Inactive')}}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @can('Student-edit')
                                                            <form action="{{route('student.remove-device', [$student->id, $studentDevice->device->id])}}" 
                                                                    method="POST" 
                                                                    style="display: inline;"
                                                                    onsubmit="return confirm('{{trans('main_trans.Confirm_remove_device')}}')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-mobile-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">{{trans('main_trans.No_devices_registered')}}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
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
@endsection
