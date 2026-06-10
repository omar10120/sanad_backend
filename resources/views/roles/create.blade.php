@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Add_role') }}
@stop
@section('css')
    <!--Internal  Font Awesome -->
    <link href="{{URL::asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <!--Internal  treeview -->
    <link href="{{URL::asset('assets/plugins/treeview/treeview-rtl.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Roles') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"> / {{ trans('main_trans.Add_role') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>خطا</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif




    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        <div class="col-xs-7 col-sm-7 col-md-7">
                            <div class="form-group">
                                <p> {{ trans('main_trans.Role_name') }}</p>
{{--                                {!! Form::text('name', null, array('class' => 'form-control')) !!}--}}
                                <label style="font-size: 16px;">
                                    <input type="text" name="name" class="form-control">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- col -->
                        <div class="col-lg-12">
                            <ul id="treeview1">
                                <li>{{ trans('main_trans.Permissions') }}
                                </li>
                                <ul>
                                    <div class="row">

                                    @foreach($permission as $value)
{{--                                        <label--}}
{{--                                            style="font-size: 16px;">{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}--}}
{{--                                            {{ $value->name }}</label>--}}
{{--                                            <div class="col-lg-3">--}}
{{--                                                <label class="ckbox"><input type="checkbox"><span>Checkbox Unchecked</span></label>--}}
{{--                                            </div>--}}

                                            <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                                                <label style="font-size: 16px;">
                                                    <input type="checkbox" name="permission[]" value="{{ $value->id }}" class="name">
                                                    {{ trans('main_trans.' . $value->name) }}
                                                </label>
                                            </div>

{{--                                            <label style="font-size: 16px;">--}}
{{--                                                <input type="checkbox" name="permission[]" value="{{ $value->id }}" class="name">--}}
{{--                                                {{ trans('main_trans.' . $value->name) }}--}}
{{--                                            </label>--}}
                                    @endforeach
                                    </div>
                                </ul>
                            </ul>
                        </div>
                        <!-- /col -->
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <button type="submit" class="btn btn-main-primary"> {{ trans('main_trans.Add_role') }}</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
    </form>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!-- Internal Treeview js -->
    <script src="{{URL::asset('assets/plugins/treeview/treeview.js')}}"></script>
@endsection
