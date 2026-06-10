@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Profile') }}
@endsection
@section('css')
    <!---Internal Fileupload css-->
    <link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
    <!---Internal Fancy uploader css-->
    <link href="{{URL::asset('assets/plugins/fancyuploder/fancy_fileupload.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Users') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Profile') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

    @include('components.flash-messages')

    <!-- row -->
    <div class="row row-sm">
        <div class="col-lg-4">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="pl-0">
                        <div class="main-profile-overview">
                            <div class="main-img-user profile-user">
                                <img alt="" src="{{URL::asset('assets/image/Users/' . Auth::user()->id . '/' . Auth::user()->photo)}}">
                            </div>
                            <div class="d-flex justify-content-between mg-b-20">
                                <div>
                                    <h5 class="main-profile-name">{{ Auth::user()->first_name }}</h5>
                                </div>
                            </div>
                            <hr class="mg-y-30">
                            <label class="main-content-label tx-13 mg-b-20">{{ Auth::user()->email }}</label>
                            <div class="main-profile-social-list">
                                <div class="media">
                                    <h5 class="main-profile-name">{{ Auth::user()->phone }}</h5>
                                </div>
                            </div>
                            <hr class="mg-y-30">
                            <label class="main-content-label tx-13 mg-b-20">{{ trans('main_trans.User_type') }}</label>
                            <div class="main-profile-social-list">
                                <div class="media">
                                    <h5 class="main-profile-name">
                                        @if (!empty(Auth::user()->getRoleNames()))
                                            @foreach (Auth::user()->getRoleNames() as $v)
                                                {{ $v }}
                                            @endforeach
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div><!-- main-profile-overview -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">

                    <div class="border-left border-bottom border-right border-top p-4">
                        <div class="tab-pane" id="settings">
                            <form method="POST" action="updatePassword">
                                {{method_field('post')}}
                                @csrf
                                <div class="form-group row mb-4">
                                    <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                        <label for="first_name">{{ trans('main_trans.First_name') }}</label>
                                        <input type="hidden" value="{{ Auth::user()->id }}" id="id" name="id" class="form-control">
                                        <input type="text" id="first_name" value="{{ Auth::user()->first_name }}" name="first_name" class="form-control" readonly>
                                    </div>
                                    <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                        <label for="last_name">{{ trans('main_trans.Last_name') }}</label>
                                        <input type="text" id="last_name" value="{{ Auth::user()->last_name }}" name="last_name" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                        <label for="father_name">{{ trans('main_trans.Father_name') }}</label>
                                        <input type="text" id="father_name" value="{{ Auth::user()->father_name }}" name="father_name" class="form-control" readonly>
                                    </div>
                                    <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                        <label for="city">{{ trans('main_trans.City') }}</label>
                                        <input type="text" id="city" value="{{ Auth::user()->city->name }}" name="city" class="form-control" readonly>
                                    </div>
                                </div>
{{--                                <hr class="mg-y-10">--}}
{{--                                <div class="row mb-4">--}}
{{--                                    <div class="col-sm-12 col-md-12 mg-t-10 mg-sm-t-0">--}}
{{--                                        <label for="password">{{ trans('main_trans.Your_profile_page_has_been_visited') }}</label>--}}
{{--                                        <span style="font-size: 20px"> {{Auth::user()->count}} </span>--}}
{{--                                        <label for="password">{{ trans('main_trans.Times') }}</label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <hr class="mg-y-10">
                                <div class="row mb-4">
                                    <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                        <label for="password">{{ trans('main_trans.Password') }}</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">
                                        <label for="password">{{ trans('main_trans.Confirm_password') }}</label>
                                        <input type="password" name="password_confirmation" class="form-control">
                                    </div>
                                </div>
                                <button class="btn btn-primary waves-effect waves-light w-md" type="submit">{{ trans('main_trans.Set_password') }}</button>
                            </form>
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
@endsection


{{--@extends('layouts.master')--}}
{{--@section('title')--}}
{{--{{ trans('main_trans.Profile') }}--}}
{{--@endsection--}}
{{--@section('css')--}}
{{--<!---Internal Fileupload css-->--}}
{{--<link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>--}}
{{--<!---Internal Fancy uploader css-->--}}
{{--<link href="{{URL::asset('assets/plugins/fancyuploder/fancy_fileupload.css')}}" rel="stylesheet" />--}}
{{--@endsection--}}
{{--@section('page-header')--}}
{{--				<!-- breadcrumb -->--}}
{{--				<div class="breadcrumb-header justify-content-between">--}}
{{--					<div class="my-auto">--}}
{{--						<div class="d-flex">--}}
{{--							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Users') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Profile') }}</span>--}}
{{--						</div>--}}
{{--					</div>--}}
{{--				</div>--}}
{{--				<!-- breadcrumb -->--}}
{{--@endsection--}}
{{--@section('content')--}}
{{--				<!-- row -->--}}
{{--				<div class="row row-sm">--}}
{{--					<div class="col-lg-4">--}}
{{--						<div class="card mg-b-20">--}}
{{--							<div class="card-body">--}}
{{--								<div class="pl-0">--}}
{{--									<div class="main-profile-overview">--}}
{{--										<div class="main-img-user profile-user">--}}
{{--											<img alt="" src="{{URL::asset('assets/image/Users/' . Auth::user()->id . '/' . Auth::user()->photo_name)}}">--}}
{{--										</div>--}}
{{--										<div class="d-flex justify-content-between mg-b-20">--}}
{{--											<div>--}}
{{--												<h5 class="main-profile-name">{{ Auth::user()->name }}</h5>--}}
{{--											</div>--}}
{{--										</div>--}}
{{--										<hr class="mg-y-30">--}}
{{--										<label class="main-content-label tx-13 mg-b-20">{{ trans('main_trans.User_type') }}</label>--}}
{{--										<div class="main-profile-social-list">--}}
{{--											<div class="media">--}}
{{--                                                @if (Auth::user()->admin_value==1)--}}
{{--                                                    <h5 class="main-profile-name">{{ trans('main_trans.Super_admin') }}</h5>--}}
{{--                                                @else--}}
{{--                                                    <h5 class="main-profile-name">{{ trans('main_trans.Admin') }}</h5>--}}
{{--                                                @endif--}}
{{--											</div>--}}
{{--										</div>--}}
{{--									</div><!-- main-profile-overview -->--}}
{{--								</div>--}}
{{--							</div>--}}
{{--						</div>--}}
{{--					</div>--}}
{{--					<div class="col-lg-8">--}}
{{--						<div class="card">--}}
{{--							<div class="card-body">--}}

{{--								<div class="border-left border-bottom border-right border-top p-4">--}}
{{--									<div class="tab-pane" id="settings">--}}
{{--										<form method="POST" action="{{ route('users.update','users') }}" enctype="multipart/form-data">--}}
{{--                                            {{method_field('patch')}}--}}
{{--                                            @csrf--}}
{{--											<div class="form-group">--}}
{{--												<label class="card-title mb-1" for="name">{{ trans('main_trans.Username') }}</label>--}}
{{--                                                <input type="hidden" value="{{ Auth::user()->id }}" id="id" name="id" class="form-control">--}}
{{--												<input type="text" value="{{ Auth::user()->name }}" id="name" name="name" class="form-control">--}}
{{--											</div>--}}
{{--                                            <div class="row mb-4">--}}
{{--                                                <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">--}}
{{--                                                    <div class="col-sm-12 col-md-8 mg-t-10 mg-sm-t-0">--}}
{{--                                                        <h4 class="card-title mb-1">{{ trans('main_trans.Add_image_user') }}</h4>--}}
{{--                                                    </div>--}}
{{--                                                    <input type="file" class="dropify" id="pic" name="pic" data-height="100" data-default-file="{{URL::asset('assets/image/Users')}}/{{Auth::user()->id}}/{{Auth::user()->photo_name}}" accept=".jpg, .png, image/jpeg, image/png"/>--}}
{{--                                                </div>--}}
{{--                                                <div class="col-sm-12 col-md-6 mg-t-10 mg-sm-t-0">--}}
{{--                                                    <p class="card-title mb-1">{{ trans('main_trans.Required_image_user') }}</p>--}}
{{--                                                    <p class="text-danger">{{ trans('main_trans.jpg') }}</p>--}}
{{--                                                    <p class="text-danger">{{ trans('main_trans.resolution_1280_1280') }}</p>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--											<button class="btn btn-primary waves-effect waves-light w-md" type="submit">{{ trans('main_trans.Edit_user') }}</button>--}}
{{--										</form>--}}
{{--									</div>--}}
{{--								</div>--}}
{{--							</div>--}}
{{--						</div>--}}
{{--					</div>--}}
{{--				</div>--}}
{{--				<!-- row closed -->--}}
{{--			</div>--}}
{{--			<!-- Container closed -->--}}
{{--		</div>--}}
{{--		<!-- main-content closed -->--}}
{{--@endsection--}}
{{--@section('js')--}}
{{--<!--Internal Fileuploads js-->--}}
{{--<script src="{{URL::asset('assets/plugins/fileuploads/js/fileupload.js')}}"></script>--}}
{{--<script src="{{URL::asset('assets/plugins/fileuploads/js/file-upload.js')}}"></script>--}}
{{--<!--Internal Fancy uploader js-->--}}
{{--<script src="{{URL::asset('assets/plugins/fancyuploder/jquery.ui.widget.js')}}"></script>--}}
{{--<script src="{{URL::asset('assets/plugins/fancyuploder/jquery.fileupload.js')}}"></script>--}}
{{--<script src="{{URL::asset('assets/plugins/fancyuploder/jquery.iframe-transport.js')}}"></script>--}}
{{--<script src="{{URL::asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js')}}"></script>--}}
{{--<script src="{{URL::asset('assets/plugins/fancyuploder/fancy-uploader.js')}}"></script>--}}
{{--@endsection--}}
