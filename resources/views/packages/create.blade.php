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
    {{ trans('main_trans.Add_code_package') }}
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Code_packages') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Add_code_package') }}</span>
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
                            <a class="btn btn-primary btn-sm" href="{{ route('code-package.index') }}">{{ trans('main_trans.Back') }}</a>
                        </div>
                    </div>
                    <br>
                    <form class="parsley-style-1" id="selectForm2" autocomplete="off" name="selectForm2"
                          action="{{route('code-package.store','test')}}" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-4" id="fnWrapper">
                                <label for="name_ar" class="card-title mb-1">{{ trans('main_trans.Name') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="name"
                                       data-parsley-class-handler="#lnWrapper" name="name" required="" type="text" placeholder="{{ trans('main_trans.First_name') }}">
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="name_en" class="card-title mb-1">{{ trans('main_trans.Name_en') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="name_en"
                                       data-parsley-class-handler="#lnWrapper" name="name_en" required="" type="text" placeholder="{{ trans('main_trans.Father_name') }}">
                            </div>

                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="email" class="card-title mb-1">{{ trans('main_trans.Email') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="email"
                                       data-parsley-class-handler="#lnWrapper" name="email" required="" type="email" placeholder="{{ trans('main_trans.Email') }}">
                            </div>
                        </div>

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-4 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label for="phone" class="card-title mb-1">{{ trans('main_trans.Phone') }} <span class="tx-danger">*</span></label>
                                <input class="form-control mg-b-20" id="phone"
                                       data-parsley-class-handler="#lnWrapper" name="phone" required="" type="text" placeholder="{{ trans('main_trans.Phone') }}">
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
                                    <h4 class="card-title mb-1">{{ trans('main_trans.Add_image_user') }} <span class="tx-danger">*</span></h4>
                                </div>
                                <input type="file" class="dropify" id="photo" name="photo" data-height="80" accept=".jpg, .png, image/jpeg, image/png"/>
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
                                            <option value="{{ $role }}">{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="parsley-input col-md-3 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <div class="form-group">
                                    <label for="status" class="form-label card-title mb-1">{{ trans('main_trans.User_status') }} <span class="tx-danger">*</span></label>
                                    <select name="status" id="status" class="form-control  nice-select  custom-select">
                                        <option value="" selected disabled>{{ trans('main_trans.Select_status') }}</option>
                                        <option value="0">{{ trans('main_trans.Disable') }}</option>
                                        <option value="1">{{ trans('main_trans.Enable') }}</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            @can('User-add')
                                <button class="btn btn-main-primary pd-x-20" type="submit">{{ trans('main_trans.Add_user') }}</button>
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
