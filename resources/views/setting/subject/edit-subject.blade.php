@extends('layouts.master')
@section('title')
{{ trans('main_trans.Edit_subject') }}
@endsection
@section('css')
<!---Internal Fileupload css-->
<link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
<!---Internal Fancy uploader css-->
<link href="{{URL::asset('assets/plugins/fancyuploder/fancy_fileupload.css')}}" rel="stylesheet" />

<!-- google icon material -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Subjects') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Edit_subject') }}</span>
						</div>
					</div>
				</div>
				<!-- breadcrumb -->
@endsection
@section('content')

    @include('components.flash-messages')

				<!-- row -->
				<div class="row row-sm">
                        <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
                            <div class="card  box-shadow-0">
                                <form method="POST" action="{{ route('subject.update', $subject->id) }}" enctype="multipart/form-data">
                                    {{method_field('patch')}}
                                    @csrf
                                <div class="card-header">
                                    <h4 class="card-title mb-1">{{ trans('main_trans.Edit_subject') }}</h4>
                                    <p class="mb-2"></p>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="form-group">
                                        <label for="name" class="col-md-6 col-form-label text-md-end">{{ trans('main_trans.Name') }}</label>
                                        <input type="hidden" name="id" id="id" value="{{$subject->id}}">
                                        <div class="col-md-12">
                                            <input id="name" class="form-control" name="name" placeholder="{{ trans('main_trans.Name') }}" required value="{{$subject->name}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="icon" class="col-md-6 col-form-label text-md-end">{{ trans('main_trans.Icon') }}</label>
                                        <div class="col-md-12 mb-3">
                                            <input id="icon" class="form-control" name="icon" placeholder="{{ trans('main_trans.Icon') }}" value="{{$subject->icon}}">
                                        </div>
                                    </div>
                                    <div class="card-header">
                                        <h4 class="card-title mb-1">{{ trans('main_trans.Old_icon') }}<span class="material-icons mx-3" style="font-size: 48px ">{{$subject->icon}}</span></h4>
                                    </div>
                                    <div class="form-group">
                                        <label for="description" class="col-md-6 col-form-label text-md-end">{{ trans('main_trans.Description') }}</label>
                                        <div class="col-md-12 mb-3">
                                            <input id="description" class="form-control" name="description" placeholder="{{ trans('main_trans.Description') }}" value="{{$subject->description}}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="icon_photo" class="col-md-4 col-form-label text-md-end">{{ trans('main_trans.Subject_photo') }}</label>
                                        <div class="col-md-8">
                                            @if($subject->icon_photo != null)
                                                <input class="dropify" id="icon_photo" name="icon_photo" type="file" data-height="120" accept=".jpg, .png, image/jpeg, image/png"
                                                       data-default-file="{{URL::asset('assets/image/Subjects')}}/{{$subject->id}}/{{$subject->icon_photo}}">
                                            @else
                                                <input class="dropify" id="icon_photo" name="icon_photo" type="file" data-height="120" accept=".jpg, .png, image/jpeg, image/png">
                                            @endif
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-xl-6 col-md-12 col-sm-12">
                            <div class="card  box-shadow-0 ">
                                <div class="card-header">
{{--                                    <h4 class="card-title mb-1">{{ trans('main_trans.Info_subject_ar') }}</h4>--}}
                                    <p class="mb-2"></p>
                                </div>
                                <div class="card-body pt-0">

                                    <div class="form-group">
                                        <label for="teacher" class="col-md-6 col-form-label text-md-end">{{ trans('main_trans.Teacher') }}</label>
                                        <div class="col-md-8">
                                            <input id="teacher" class="form-control" name="teacher" placeholder="{{ trans('main_trans.Teacher') }}" value="{{$subject->teacher}}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="link" class="col-md-6 col-form-label text-md-end">{{ trans('main_trans.Link') }}</label>
                                        <div class="col-md-8">
                                            <input id="link" class="form-control" name="link" placeholder="{{ trans('main_trans.Link') }}" value="{{$subject->link}}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="types" class="col-md-6 col-form-label text-md-end">{{ trans('main_trans.Certificate_types') }}</label>
                                        <div class="col-md-8">
                                            <select name="types[]" id="types" class="form-control" required multiple>
                                                @foreach ($types as $type)
                                                    <option
                                                        @foreach($types_subject as $type_subject)
                                                            @php
                                                                $id1 = $type->id;
                                                                $id2 = $type_subject->id;
                                                                $is_select = $id1 == $id2;
                                                            @endphp
                                                            @if($is_select)
                                                                selected
                                                            @endif
                                                        @endforeach

                                                        value="{{$type->id}}">{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="light_color_code" class="col-md-2 col-form-label text-md-end">{{ trans('main_trans.Light_color') }}</label>
                                        <div class="col-md-2">
                                            <input id="light_color_code" class="form-control" name="light_color_code" type="color" value="{{$subject->light_color_code}}">
                                        </div>

                                        <label for="dark_color_code" class="col-md-2 col-form-label text-md-end">{{ trans('main_trans.Dark_color') }}</label>
                                        <div class="col-md-2">
                                            <input id="dark_color_code" class="form-control" name="dark_color_code" type="color" value="{{$subject->dark_color_code}}">
                                        </div>
                                    </div>

                                    <div class="card-header">
{{--                                        <p class="mb-5">.</p>--}}
{{--                                        <p class="mb-3">.</p>--}}
                                        <h4 class="card-title mb-1">{{ trans('main_trans.Please_check') }}</h4>
                                    </div>
                                    <div class="form-group mb-0 mt-3 justify-content-end">
                                        <div>
                                            @can('Subject-edit')
                                                <button type="submit" class="btn btn-primary">{{ trans('main_trans.Edit_subject') }}</button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
				</div>
				<!-- row -->
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
