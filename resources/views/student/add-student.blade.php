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
    {{ trans('main_trans.Add_student') }}
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Student') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Add_student') }}</span>
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
                    </div>
                    <br>
                    <form class="parsley-style-1" id="selectForm2" autocomplete="off" name="selectForm2"
                          action="{{route('student.store','test')}}" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-4" id="fnWrapper">
                                <label for="first_name" class="card-title mb-1">{{ trans('main_trans.First_name') }} <span class="tx-danger">*</span></label>
                                <input type="hidden" name="device_id" value="Unknown">
                                <input class="form-control mg-b-20" id="first_name"
                                       data-parsley-class-handler="#lnWrapper" name="first_name" required="" type="text" placeholder="{{ trans('main_trans.First_name') }}">
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="father_name" class="card-title mb-1">{{ trans('main_trans.Father_name') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="father_name"
                                       data-parsley-class-handler="#lnWrapper" name="father_name" required="" type="text" placeholder="{{ trans('main_trans.Father_name') }}">
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="last_name" class="card-title mb-1">{{ trans('main_trans.Last_name') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="last_name"
                                       data-parsley-class-handler="#lnWrapper" name="last_name" required="" type="text" placeholder="{{ trans('main_trans.Last_name') }}">
                            </div>

                        </div>

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="email" class="card-title mb-1">{{ trans('main_trans.Email') }} </label>
                                <input class="form-control mg-b-20" id="email"
                                       data-parsley-class-handler="#lnWrapper" name="email" type="email" placeholder="{{ trans('main_trans.Email') }}">
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="phone" class="card-title mb-1">{{ trans('main_trans.Phone') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="phone"
                                       data-parsley-class-handler="#lnWrapper" name="phone" required="" type="text" placeholder="{{ trans('main_trans.Phone') }}">
                            </div>
                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="country_code" class="card-title mb-1">{{ trans('main_trans.Country_code') }} <span class="tx-danger">*</span></label>
                                <select id="country_code" name="country_code" class="form-control  nice-select  custom-select">
                                    <option value="" selected disabled>{{ trans('main_trans.Select_country_code') }}</option>
                                    <option value="+963"> +963 {{ trans('main_trans.Syria') }}</option>
                                    <option value="+964"> +964 {{ trans('main_trans.Iraq') }}</option>
                                    <option value="+965"> +965 {{ trans('main_trans.Kuwait') }}</option>
                                    <option value="+966"> +966 {{ trans('main_trans.Saudi_Arabia') }}</option>
                                    <option value="+967"> +967 {{ trans('main_trans.Yemen') }}</option>
                                    <option value="+968"> +968 {{ trans('main_trans.Oman') }}</option>
                                    <option value="+970"> +970 {{ trans('main_trans.Palestine') }}</option>
                                    <option value="+971"> +971 {{ trans('main_trans.United_Arab_Emirates') }}</option>
                                    <option value="+973"> +973 {{ trans('main_trans.Bahrain') }}</option>
                                    <option value="+974"> +974 {{ trans('main_trans.Qatar') }}</option>
                                    <option value="+975"> +975 {{ trans('main_trans.Bhutan') }}</option>
                                    <option value="+976"> +976 {{ trans('main_trans.Mongolia') }}</option>
                                    <option value="+977"> +977 {{ trans('main_trans.Nepal') }}</option>
                                </select>
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <div class="form-group">
                                    <label for="city" class="form-label card-title mb-1">{{ trans('main_trans.City') }} <span class="tx-danger">*</span></label>
                                    <select id="city" name="city" class="form-control  nice-select  custom-select">
                                        <option value="" selected disabled>{{ trans('main_trans.Select_city') }}</option>
                                        <option value="other">{{ trans('main_trans.other') }}</option>
                                        <option value="damascus">{{ trans('main_trans.damascus') }}</option>
                                        <option value="damascus_suburb">{{ trans('main_trans.damascus_suburb') }}</option>
                                        <option value="homs">{{ trans('main_trans.homs') }}</option>
                                        <option value="hama">{{ trans('main_trans.hama') }}</option>
                                        <option value="aleppo">{{ trans('main_trans.aleppo') }}</option>
                                        <option value="idlib">{{ trans('main_trans.idlib') }}</option>
                                        <option value="tartus">{{ trans('main_trans.tartus') }}</option>
                                        <option value="latakia">{{ trans('main_trans.latakia') }}</option>
                                        <option value="deir_ezzor">{{ trans('main_trans.deir_ezzor') }}</option>
                                        <option value="hasaka">{{ trans('main_trans.hasaka') }}</option>
                                        <option value="raqqa">{{ trans('main_trans.raqqa') }}</option>
                                        <option value="sweida">{{ trans('main_trans.sweida') }}</option>
                                        <option value="daraa">{{ trans('main_trans.daraa') }}</option>
                                        <option value="quneitra">{{ trans('main_trans.quneitra') }}</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row mg-b-20">

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <div class="form-group">
                                    <label for="type_id" class="form-label card-title mb-1">{{ trans('main_trans.Certificate_type') }} <span class="tx-danger">*</span></label>
                                    <select id="type_id" name="type_id" class="form-control  nice-select  custom-select">
                                        <option value="" disabled>{{ trans('main_trans.Select_type') }}</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label class="card-title mb-1">{{ trans('main_trans.Password') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" data-parsley-class-handler="#lnWrapper"
                                       name="password" required="" type="password" placeholder="{{ trans('main_trans.Password') }}">
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label class="card-title mb-1">{{ trans('main_trans.Confirm_password') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" data-parsley-class-handler="#lnWrapper"
                                       name="password_confirmation" required="" type="password" placeholder="{{ trans('main_trans.Confirm_password') }}">
                            </div>

                        </div>

                        <div class="row mg-b-20">

                            <div class="col-sm-12 col-md-3 mg-t-10 mg-sm-t-0">
                                <div class="col-sm-12 col-md-8 mg-t-10 mg-sm-t-0">
                                    <h4 class="card-title mb-1">{{ trans('main_trans.Add_image_student') }} <span class="tx-danger">*</span></h4>
                                </div>
                                <input type="file" class="dropify" id="photo" name="photo" data-height="80" accept=".jpg, .png, image/jpeg, image/png"/>
                            </div>
                            <div class="col-sm-12 col-md-3 mg-t-10 mg-sm-t-0">
                                <p class="card-title mb-1">{{ trans('main_trans.Required_image_student') }}</p>
                                <p class="text-danger">{{ trans('main_trans.jpg') }}</p>
                                <p class="text-danger">{{ trans('main_trans.resolution_1280_1280') }}</p>
                            </div>

{{--                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="role" class="form-label card-title mb-1">{{ trans('main_trans.User_permission') }} <span class="tx-danger">*</span></label>--}}
{{--                                    <select id="role" name="roles_name[]" class="form-control  nice-select  custom-select" --}}{{--multiple--}}{{-->--}}
{{--                                        <option value="" selected disabled>{{ trans('main_trans.Select_role') }}</option>--}}
{{--                                        @foreach($roles as $role)--}}
{{--                                            <option value="{{ $role }}">{{ $role }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="school" class="card-title mb-1">{{ trans('main_trans.School') }} </label>
                                <input class="form-control mg-b-20" id="school"
                                       data-parsley-class-handler="#lnWrapper" name="school" type="text" placeholder="{{ trans('main_trans.School') }}">
                            </div>

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <div class="form-group">
                                    <label for="status" class="form-label card-title mb-1">{{ trans('main_trans.Student_status') }} <span class="tx-danger">*</span></label>
                                    <select name="status" id="status" class="form-control  nice-select  custom-select">
                                        <option value="" selected disabled>{{ trans('main_trans.Select_status') }}</option>
                                        <option value="0">{{ trans('main_trans.Disable') }}</option>
                                        <option value="1">{{ trans('main_trans.Enable') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="max_devices" class="card-title mb-1">
                                    {{trans('main_trans.Maximum_devices')}}
                                    <span class="tx-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control mg-b-20" 
                                       id="max_devices"
                                       name="max_devices" 
                                       min="1" 
                                       max="10" 
                                       value="1"
                                       data-parsley-class-handler="#lnWrapper"
                                       placeholder="{{trans('main_trans.Maximum_devices')}}" 
                                       required>
                                <small class="form-text text-muted">
                                    {{trans('main_trans.Device_limit_help_text')}}
                                </small>
                            </div>

                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            @can('Student-add')
                                <button class="btn btn-main-primary pd-x-20" type="submit">{{ trans('main_trans.Add_student') }}</button>
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
@endsection
